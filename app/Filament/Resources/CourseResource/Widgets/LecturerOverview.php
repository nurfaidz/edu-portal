<?php

namespace App\Filament\Resources\CourseResource\Widgets;

use App\Models\Course;
use App\Models\LecturerCourse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LecturerOverview extends BaseWidget
{
    public Course $record;

    protected function getStats(): array
    {
        $query = $this->record->lecturerCourse;
        $lecturer = $query ? $query->lecturer->lecturerProfile->name : 'Belum ada dosen';

        return [
            BaseWidget\Stat::make('Dosen Mata Kuliah:', $lecturer),
        ];
    }

    protected function getColumns(): int
    {
        return 1;
    }
}
