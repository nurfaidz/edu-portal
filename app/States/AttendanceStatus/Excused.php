<?php

namespace App\States\AttendanceStatus;

class Excused extends AttendanceStatusState
{
    public static string $name = 'Excused';

    public function label(): string
    {
        return 'Izin';
    }

    public function color(): string
    {
        return 'warning';
    }

    public function toLivewire(): static
    {
        return new static(static::getModel());
    }

    public static function fromLivewire($value)
    {
        return $value;
    }
}
