<?php

namespace App\States\AttendanceStatus;

use App\States\AttendanceStatus\AttendanceStatusState;

class Pending extends AttendanceStatusState
{

    public static string $name = 'Belum Hadir';

    public function label(): string
    {
        return 'Belum Hadir';
    }

    public function color(): string
    {
        return 'secondary';
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
