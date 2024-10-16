<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PayrollLecturer extends Settings
{

    public int $amount_transport_salary;

    public int $amount_sks_salary;

    public static function group(): string
    {
        return 'salary';
    }
}
