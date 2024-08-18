<?php

namespace App\States\AttendanceStatus;

class Present extends AttendanceStatusState
{
    public static string $name = 'Present';

    public function label(): string
    {
        return 'Hadir';
    }

    public function color(): string
    {
        return 'success';
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
