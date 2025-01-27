<?php

namespace App\Filament\Resources\AdminLecturerAttendanceSummaryResource\Widgets;

use App\Models\LecturerCourse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceOverview extends BaseWidget
{
    public $record;

    protected function getStats(): array
    {
        $monthDay = date('m-d');
        $queryAttendances = collect();
        $queryChangeSchedules = collect();

        foreach ($this->record->lecturerCourses as $course) {
            if ($monthDay >= '02-01' && $monthDay <= '08-31') {
                $schedules = $course->schedules()
                    ->whereRaw('MOD(semester, 2) = 0')
                    ->where('academic_year', now()->year)
                    ->get();
            } else {
                $schedules = $course->schedules()
                    ->whereRaw('MOD(semester, 2) <> 0')
                    ->where('academic_year', now()->year)
                    ->get();
            }

            $queryChangeSchedules = $queryChangeSchedules->merge($schedules);

            foreach ($schedules as $schedule) {
                $queryAttendances = $queryAttendances->merge($schedule->attendances)->where('attendable_id', $this->record->user_id);
            }
        }

        $absent = $queryAttendances->where('status', \App\States\AttendanceStatus\Absent::$name)->count();
        $pending = $queryAttendances->where('status', \App\States\AttendanceStatus\Pending::$name)
            ->where('expired_at', '<', now())->count();
        $present = $queryAttendances->where('status', \App\States\AttendanceStatus\Present::$name)->count();
        $excused = $queryAttendances->where('status', \App\States\AttendanceStatus\Excused::$name)->count();
        $reschedule = $queryChangeSchedules->where('is_reschedule', true)->count();

        return [
            Stat::make('Tidak Hadir:', $absent + $pending),
            Stat::make('Hadir:', $present),
            Stat::make('Izin:', $excused),
            Stat::make('Reschedule:', $reschedule),
        ];
    }
}
