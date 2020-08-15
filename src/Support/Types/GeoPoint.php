<?php

namespace CbtechLtd\Fastlane\Support\Types;

class GeoPoint
{
    private ?float $longitude = null;
    private ?float $latitude = null;

    public function __construct(?float $longitude, ?float $latitude)
    {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }


    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function isValid(): bool
    {
        return $this->latitude && $this->longitude;
    }

    public function toPoint(): array
    {
        return [$this->longitude, $this->latitude];
    }
}
