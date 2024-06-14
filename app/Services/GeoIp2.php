<?php

namespace App\Services;

use GeoIp2\Database\Reader;

class GeoIp2
{
    public static function all(string $ip): array
    {
        try {
            $record = (new Reader(storage_path('app/geoip2/GeoLite2-City.mmdb')))->city($ip);

            return [
                'latitude' => $record->location->latitude,
                'longitude' => $record->location->longitude,
            ];
        } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
            return ['latitude' => 0, 'longitude' => 0];
        }
    }
}
