<?php

namespace App\Actions\Business;

use App\Http\Resources\BusinessResource;
use App\Models\Business;
use Illuminate\Database\Eloquent\Builder;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use MatanYadaev\EloquentSpatial\Enums\Srid;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\Filters\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->orWhere(fn ($query) => $query->withAnyTags($value));
    }
}

class LatitudeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->whereRaw('ST_X(coordinates) LIKE ?', ['%'.$value.'%']);
    }
}

class LongitudeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->whereRaw('ST_Y(coordinates) LIKE ?', ['%'.$value.'%']);
    }
}

class TermFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->where('name', 'like', "%{$value}%");
    }
}

class BusinessList
{
    use AsAction;

    public function handle(array $data = []): mixed
    {
        $userPoint = new Point($data['user']['latitude'], $data['user']['longitude'], Srid::WGS84->value);

        $business = QueryBuilder::for(Business::class)
            ->allowedFilters([
                AllowedFilter::scope('location', 'search_address'),
                AllowedFilter::custom('term', new TermFilter),
                AllowedFilter::custom('categories', new CategoryFilter),
                AllowedFilter::callback(
                    'radius',
                    fn ($query, $value) => $query->whereDistance('coordinates', $userPoint, '<', $value)
                ),
                // if you wondering why X and Y are swapped, read this: https://dba.stackexchange.com/a/242004
                AllowedFilter::custom('latitude', new LatitudeFilter),
                AllowedFilter::custom('longitude', new LongitudeFilter),
            ])
            ->defaultSort('created_at')
            ->allowedSorts(['distance', 'rating'])
            ->allowedIncludes([
                'reviews',
                'reviews.author',
                AllowedInclude::relationship('categories', 'tags'),
            ])
            ->withAvg('reviews as rating', 'rating')
            ->withDistanceSphere('coordinates', $userPoint)
            ->when(
                $data['paginate'] ?? false,
                fn ($query, $paginate) => $query->paginate($paginate),
                fn ($query) => $query->get()
            );

        return $business;
    }

    public function asController(ActionRequest $request)
    {
        $paginate = $request->input('paginate', 0);
        $user = [
            'latitude' => $request->input('user.latitude', 0),
            'longitude' => $request->input('user.longitude', 0),
        ];

        try {
            $this->validateFilter($request->all());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return $this->handle(compact('paginate', 'user'));
    }

    public function jsonResponse($data)
    {
        if ($data instanceof \Illuminate\Http\JsonResponse) {
            return $data;
        }

        return BusinessResource::collection($data);
    }

    protected function validateFilter(array $data)
    {
        $location = $data['filter']['location'] ?? null;
        $latitude = $data['filter']['latitude'] ?? null;
        $longitude = $data['filter']['longitude'] ?? null;
        $radius = $data['filter']['radius'] ?? null;

        // either location or latitude and longitude must be provided
        abort_if(! $location && ! $latitude && ! $longitude, 400, 'Either location or latitude and longitude must be provided');
        abort_if(($latitude && ! $longitude) || (! $latitude && $longitude), 400, 'Both latitude and longitude must be provided');

        // radius must be integer and max value is 40_000 meters
        abort_if($radius && (! is_numeric($radius) || $radius > 40_000), 400, 'Radius must be integer and max value is 40_000');
    }
}
