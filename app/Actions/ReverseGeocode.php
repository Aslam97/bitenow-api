<?php

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
            'latlong' => ['required', 'string'],
        ];
    }

    public function handle(ActionRequest $request)
    {
        [$latitude, $longitude] = explode(',', $request->latlong);

        return cookie(
            name: 'gf_chosen_loc',
            value: json_encode(compact('latitude', 'longitude')),
            minutes: config('session.lifetime')
        );
    }

    public function jsonResponse(\Illuminate\Cookie\CookieJar|\Symfony\Component\HttpFoundation\Cookie $cookie)
    {
        return response()->json([
            'message' => 'Success',
        ])->withCookie($cookie);
    }
}
