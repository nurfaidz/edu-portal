<?php

namespace App\Filament\Resources\AdminLecturerAttendanceSummaryResource\Widgets;

use App\Models\LecturerCourse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AbsentOverview extends BaseWidget
{
    public $record;

    protected function getStats(): array
    {
        $queryAttendances = $this->record->attendances;
        $queryChangeSchedules = $this->record->schedules;

        // Attendances
        $absent = $queryAttendances->where('status', \App\States\AttendanceStatus\Absent::$name)->count();
        $pending = $queryAttendances->where('status', \App\States\AttendanceStatus\Pending::$name)
            ->where('expired_at', '<', now())->count();

        $present = $queryAttendances->where('status', \App\States\AttendanceStatus\Present::$name)->count();
        $excused = $queryAttendances->where('status', \App\States\AttendanceStatus\Excused::$name)->count();

        // Reschedule
        $reschedule = $queryChangeSchedules->where('is_reschedule', true)->count();

        return [Stat::make('Tidak Hadir:', $absent+$pending),
            Stat::make('Hadir:', $present),
            Stat::make('Izin:', $excused),
            Stat::make('Reschedule:', $reschedule),];
    }
}
