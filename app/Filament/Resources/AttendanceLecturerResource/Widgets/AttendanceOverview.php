<?php

namespace App\Filament\Resources\AttendanceLecturerResource\Widgets;

use App\Models\Schedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Collection;

class AttendanceOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $monthDay = date('m-d');

        $query = Schedule::whereHas('lecturerCourse', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->where(function ($query) use ($monthDay) {
                if ($monthDay >= '02-01' && $monthDay <= '08-31') {
                    $query->whereRaw('MOD(semester, 2) = 0')->where('academic_year', now()->year);
                } else {
                    $query->whereRaw('MOD(semester, 2) <> 0')->where('academic_year', now()->year);
                }
            })
            ->get();

        $allAttendance = new Collection();

        foreach ($query as $schedule) {
            $allAttendance = $allAttendance->merge($schedule->attendances->where('attendable_type', '\App\Models\Lecturer')->where('attendable_id', auth()->id()));
        }

        // Attendances
        $absent = $allAttendance->where('status', \App\States\AttendanceStatus\Absent::$name)->count();
        $pending = $allAttendance->where('status', \App\States\AttendanceStatus\Pending::$name)
            ->where('expired_at', '<', now())->count();

        $present = $allAttendance->where('status', \App\States\AttendanceStatus\Present::$name)->count();
        $excused = $allAttendance->where('status', \App\States\AttendanceStatus\Excused::$name)->count();

        // Reschedule
        $reschedule = $query->where('is_reschedule', true)->count();

        return [
            Stat::make('Tidak Hadir:', $absent + $pending),
            Stat::make('Hadir:', $present),
            Stat::make('Izin:', $excused),
            Stat::make('Reschedule:', $reschedule),
        ];
    }
}
