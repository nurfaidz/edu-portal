<?php

namespace App\Filament\Resources\AdminLecturerAttendanceSummaryResource\Widgets;

use App\Settings\PayrollLecturer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PayrollOverview extends BaseWidget
{
    public $record;

    protected function getStats(): array
    {
        $lecturerSalary = $this->record->user->lecturerSalary;

        return [
            Stat::make('Upah Transport: ', $lecturerSalary ? 'Rp. ' . number_format($lecturerSalary->amount_salary_transport, 0, ',', '.') : 'Rp. 0'),
            Stat::make('Upah per SKS: ', $lecturerSalary ? 'Rp. ' . number_format($lecturerSalary->amount_salary_sks, 0, ',', '.') : 'Rp. 0'),
            Stat::make('Total Upah: ', $lecturerSalary ? 'Rp. ' . number_format($lecturerSalary->total_salary, 0, ',', '.') : 'Rp. 0'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
