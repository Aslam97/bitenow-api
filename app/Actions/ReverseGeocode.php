<?php

/**
 * This an alternative to the NextJs /api/geo endpoint.
 * in case you want to use geo in the main API.
 */

namespace App\Actions;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ReverseGeocode
{
    use AsAction;

    public function authorize(ActionRequest $request): bool
    {
        return $request->wantsJson();
    }

    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['required', 'numeric', 'min:-180', 'max:180'],
        ];
    }

    public function handle(array $data)
    {
        return cookie(
            name: 'aralu_geo',
            value: json_encode($data),
            minutes: config('session.lifetime')
        );
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle($request->validated());
    }

    public function jsonResponse(\Illuminate\Cookie\CookieJar|\Symfony\Component\HttpFoundation\Cookie $cookie)
    {
        return response()->json([
            'message' => 'Success',
        ])->withCookie($cookie);
    }
}
