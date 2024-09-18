<?php

namespace App\Filament\Resources\AdminLecturerAttendanceSummaryResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PayrollOverview extends BaseWidget
{
    public $record;

    protected function getStats(): array
    {
        $lecturerSalary = $this->record->user->lecturerSalary;

        return [
            Stat::make('Upah yang Diterima:', $lecturerSalary ? $lecturerSalary->amount : 'Rp. 0'),
        ];
    }

    protected function getColumns(): int
    {
        return 1;
    }
}
