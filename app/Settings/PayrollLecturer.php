<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PayrollLecturer extends Settings
{

    public int $amount;

    public static function group(): string
    {
        return 'payroll';
    }
}
