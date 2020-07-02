<?php

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

use CbtechLtd\Fastlane\Support\Types\GeoPoint;
use Illuminate\Support\Arr;

trait HasGeoCoordinates
{
    public function initializeHasGeoCoordinates(): void
    {
        if (! in_array('latitude', $this->fillable)) {
            $this->fillable[] = 'latitude';
        }

        if (! in_array('longitude', $this->fillable)) {
            $this->fillable[] = 'longitude';
        }

        if (! in_array('geocode', $this->fillable)) {
            $this->fillable[] = 'geocode';
        }
    }

    public function setGeocodeAttribute(GeoPoint $point): void
    {
        $this->attributes['latitude'] = (float)$point->getLatitude();
        $this->attributes['longitude'] = (float)$point->getLongitude();
    }

    public function getGeocodeAttribute(): GeoPoint
    {
        $longitude = Arr::get($this->attributes, 'longitude');
        $latitude = Arr::get($this->attributes, 'latitude');

        return new GeoPoint($longitude, $latitude);
    }
}
