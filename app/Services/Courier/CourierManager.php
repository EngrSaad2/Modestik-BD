<?php

namespace App\Services\Courier;

use App\Models\Setting;
use InvalidArgumentException;

class CourierManager
{
    protected array $drivers = [];

    public function driver(?string $name = null): CourierServiceInterface
    {
        $name = $name ?: Setting::get('default_courier', 'steadfast');

        if (!isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->createDriver($name);
        }

        return $this->drivers[$name];
    }

    protected function createDriver(string $name): CourierServiceInterface
    {
        return match (strtolower($name)) {
            'steadfast' => new SteadFastService(),
            default => throw new InvalidArgumentException("Courier driver [{$name}] is not supported."),
        };
    }
}
