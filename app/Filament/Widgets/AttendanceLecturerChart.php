<?php

namespace App\Filament\Widgets;

use App\States\AttendanceStatus\Present;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class AttendanceLecturerChart extends ChartWidget
{
    protected static ?string $heading = 'Presentase Kehadiran';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $monthDay = date('m-d');

        $lecturerCourses = \App\Models\LecturerCourse::where('user_id', auth()->id())
            ->whereRaw($monthDay >= '02-01' && $monthDay <= '08-31'
                ? 'MOD(semester, 2) = 0'
                : 'MOD(semester, 2) <> 0'
            )
            ->where('academic_year', now()->year)
            ->get();

        $attendanceData = [];

        foreach ($lecturerCourses as $course) {
            $courseName = $course->course->name;
            $presentCount = 0;

            $schedules = $course->schedules()->get();
            foreach ($schedules as $schedule) {
                $scheduleAttendance = $schedule->attendances()
                    ->where('attendable_type', '\App\Models\Student')
                    ->where('status', Present::$name)
                    ->count();

                $presentCount += $scheduleAttendance;
            }

            $attendanceData[] = [
                'label' => $courseName,
                'data' => $presentCount
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Presentase Hadir',
                    'data' => array_column($attendanceData, 'data'),
                    'backgroundColor' => ['#4CAF50', '#FFC107', '#2196F3', '#FF5722'], // Warna berbeda untuk tiap matkul
                ],
            ],
            'labels' => array_column($attendanceData, 'label'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
            },
            scales: {
                y:{
                    beginAtZero: true,
                    suggestedMax: 100,
                    ticks: {
                        callback: (value) => value + ' %',
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Kehadiran',
                    },
                },
                x: {
                    title: {
                        display: true,
                        text: 'Mata Kuliah',
                        },
                     },
            },
        }
        JS);
    }

    public static function canView(): bool
    {
        return auth()->user()->role === \App\Enums\Roles\Role::Lecturer->value;
    }
}
