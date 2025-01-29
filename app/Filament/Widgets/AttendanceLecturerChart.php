<?php

namespace App\Filament\Widgets;

use App\States\AttendanceStatus\Absent;
use App\States\AttendanceStatus\Excused;
use App\States\AttendanceStatus\Present;
use Filament\Widgets\ChartWidget;

class AttendanceLecturerChart extends ChartWidget
{
    protected static ?string $heading = 'Presentase Kehadiran';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $monthDay = date('m-d');

        $lecturerCourse = \App\Models\LecturerCourse::where('user_id', auth()->id())
            ->whereRaw($monthDay >= '02-01' && $monthDay <= '08-31'
                ? 'MOD(semester, 2) = 0'  // Semester genap
                : 'MOD(semester, 2) <> 0' // Semester ganjil
            )
            ->where('academic_year', now()->year)
            ->get();

        $attendances = collect();

        foreach ($lecturerCourse as $course) {
            $schedules = $course->schedules()->get();

            foreach ($schedules as $schedule) {
                $scheduleAttendance = $schedule->attendances;
                $attendances = $attendances->merge($scheduleAttendance)->where('attendable_id', auth()->id());
            }
        }

        $present = $attendances->where('status', Present::$name)->count();
        $absent = $attendances->where('status', Absent::$name)->count();
        $pending = $attendances->where('status', \App\States\AttendanceStatus\Pending::$name)
            ->where('expired_at', '<', now())->count();
        $excused = $attendances->where('status', Excused::$name)->count();

        return [
            'labels' => ['Hadir', 'Tidak Hadir', 'Izin'],
            'datasets' => [
                [
                    'label' => 'Presentase Kehadiran',
                    'data' => [$present, $absent + $pending, $excused],
                    'backgroundColor' => ['#4CAF50', '#F44336', '#FFC107'],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'suggestedMax' => $this->getData()['datasets'][0]['data'][0] + 5,
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah Kehadiran',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Status Kehadiran',
                    ],
                ],
            ],
            'layout' => [
                'padding' => 20,
            ],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->role === \App\Enums\Roles\Role::Lecturer->value;
    }
}
