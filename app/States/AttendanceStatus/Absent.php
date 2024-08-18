<?php

namespace App\States\AttendanceStatus;

class Absent extends AttendanceStatusState
{
    public static string $name = 'Absent';

    public function label(): string
    {
        return 'Tidak Hadir';
    }

    public function color(): string
    {
        return 'danger';
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
