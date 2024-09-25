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
        $oddDateStart = date('Y') . '-02-01';
        $oddDateEnd = date('Y') . '-08-31';
        $evenDateStart = date('Y') . '-09-01';
        $evenDateEnd = date('Y') . '-12-31';
        $now = date('Y-m-d');

        $queryAttendances = collect();
        $queryChangeSchedules = collect();

        $query = \App\Helpers\Helper::getCurrentSemester($this->record->lecturerCourses);

        foreach ($query as $course) {
            if ($now >= $oddDateStart && $now <= $oddDateEnd) {
                $schedules = $course->schedules()->whereRaw('MOD(semester, 2) <> 0')->get();
                $queryChangeSchedules = $queryChangeSchedules->merge($schedules);
                foreach ($schedules as $schedule) {
                    $queryAttendances = $queryAttendances->merge($schedule->attendances);
                }
            } elseif ($now >= $evenDateStart && $now <= $evenDateEnd) {
                $schedules = $course->schedules()->whereRaw('MOD(semester, 2) = 0')->get();
                $queryChangeSchedules = $queryChangeSchedules->merge($schedules);
                foreach ($schedules as $schedule) {
                    $queryAttendances = $queryAttendances->merge($schedule->attendances);
                }
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
