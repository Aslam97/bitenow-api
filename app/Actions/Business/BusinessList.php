<?php

namespace App\Actions\Business;

use App\Http\Resources\BusinessResource;
use App\Models\Business;
use App\Services\GeoIp2;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Validator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use MatanYadaev\EloquentSpatial\Enums\Srid;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\Filters\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class BusinessList
{
    use AsAction;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // open_now and open_at cannot be used together
            'filter.open_now' => ['boolean'],
            'filter.open_at' => ['integer'],
            // location is required if latitude and longitude are not provided
            'filter.location' => ['required_without_all:latitude,longitude', 'string', 'min:3'],
            'filter.radius' => ['integer', 'max:40000'],
            'filter.price' => ['integer', 'min:1', 'max:5'],
            // latitude and longitude must be provided
            'latitude' => ['required_with:longitude', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['required_with:latitude', 'numeric', 'min:-180', 'max:180'],
            // paginate max value is 50
            'paginate' => ['integer', 'max:50'],
        ];
    }

    public function afterValidator(Validator $validator, ActionRequest $request): void
    {
        if ($request->filled('filter.open_now') && $request->filled('filter.open_at')) {
            $validator->errors()->add('filter.open_now', 'open now and open at cannot be used together');
            $validator->errors()->add('filter.open_at', 'open now and open at cannot be used together');
        }
    }

    public function prepareForValidation(ActionRequest $request): void
    {
        $request->whenFilled('filter.open_now', function (string $input) use ($request) {
            $request->merge([
                'filter' => [
                    ...$request->input('filter', []),
                    'open_now' => filter_var($input, FILTER_VALIDATE_BOOLEAN),
                ],
            ]);
        });
    }

    public function getValidationAttributes(): array
    {
        return [
            'filter.open_now' => 'open now',
            'filter.open_at' => 'open at',
            'filter.location' => 'location',
            'filter.radius' => 'radius',
            'filter.price' => 'price',
        ];
    }

    public function handle(array $data = []): mixed
    {
        $userPoint = new Point($data['latitude'] ?? 0, $data['longitude'] ?? 0, Srid::WGS84->value);

        $businesses = QueryBuilder::for(Business::class)
            ->allowedFilters([
                AllowedFilter::exact('price'),
                AllowedFilter::scope('location', 'search_address'),
                AllowedFilter::custom('term', new TermFilter),
                AllowedFilter::custom('cuisines', new CuisineFilter),
                AllowedFilter::custom('transactions', new TransactionFilter),
                AllowedFilter::callback(
                    'radius',
                    fn ($query, $value) => $query->when($value, fn ($query) => $query->whereDistance('coordinates', $userPoint, '<', $value))
                ),
                AllowedFilter::custom('open_at', new OpenAtFilter),
                AllowedFilter::custom('open_now', new OpenNowFilter),
            ])
            ->allowedIncludes([
                'reviews',
                'reviews.author',
                'openingHours',
                'cuisines',
                AllowedInclude::relationship('transactions', 'tags'), // set alias for tags relationship
            ])
            ->defaultSort('reviews_count')
            ->allowedSorts(['distance', 'rating', 'reviews_count'])
            ->withAvg('reviews as rating', 'rating')
            ->withCount('reviews')
            ->withDistanceSphere('coordinates', $userPoint)
            ->when(
                $data['paginate'] ?? false,
                fn ($query, $paginate) => $query->paginate($paginate),
                fn ($query) => $query->get()
            );

        return BusinessResource::collection($businesses);
    }

    public function asController(ActionRequest $request)
    {
        $this->setGfChoosenLoc($request);

        return $this->handle(
            $request->validated()
        );
    }

    private function setGfChoosenLoc(ActionRequest $request)
    {
        $gfChosenLoc = json_decode($request->cookie('gf_chosen_loc', '{}'), true);

        if (empty($gfChosenLoc)) {
            $location = GeoIp2::all($request->ip());

            Cookie::queue('gf_chosen_loc', json_encode($location), config('session.lifetime'));

            $gfChosenLoc = $location;
        }

        $request->mergeIfMissing($gfChosenLoc);
    }
}

class CuisineFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? $value : [$value];

        return $query->whereHas('cuisines', fn ($query) => $query->whereIn('slug', $value));
    }
}

class TransactionFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // return $query->orWhere(fn ($query) => $query->withAnyTags($value));
        return $query->withAnyTags($value);
    }
}

class TermFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->where('name', 'like', "%{$value}%");
    }
}

class OpenAtFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->when($value, function ($query, $value) {
            $datetime = now()->setTimestamp($value);
            $day = day_of_week($datetime->dayOfWeek);
            $openAt = $datetime->format('H:i:s');

            return $query->whereHas(
                'openingHours',
                fn ($query) => $query->where('day', $day)->whereTime('open', '<=', $openAt)->whereTime('close', '>=', $openAt)
            );
        });
    }
}

class OpenNowFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $openNow = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        $now = now();
        $day = day_of_week();

        $curtime = $now->format('H:i:s');

        return $query->when(
            $openNow,
            function ($query) use ($day, $curtime) {
                return $query->whereHas(
                    'openingHours',
                    fn ($query) => $query->where('day', $day)->whereTime('open', '<=', $curtime)->whereTime('close', '>=', $curtime)
                );
            },
            function ($query) use ($day, $curtime) {
                return $query->whereHas(
                    'openingHours',
                    fn ($query) => $query->where('day', $day)->where(
                        fn ($query) => $query->whereTime('close', '<', $curtime)->orWhereTime('open', '>', $curtime)
                    )
                );
            }
        );
    }
}
