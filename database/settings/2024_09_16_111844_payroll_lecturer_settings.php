<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('salary.amount_transport_salary', 0);
        $this->migrator->add('salary.amount_sks_salary', 0);
    }
};
