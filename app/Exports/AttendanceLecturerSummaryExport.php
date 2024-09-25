<?php

namespace App\Exports;

use App\Helpers\Helper;
use App\Models\LecturerCourse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceLecturerSummaryExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithStrictNullComparison
{
    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $event->sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            },
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->mergeCells('A1:I1');
                $event->sheet->getDelegate()->getStyle('A1:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    public function headings(): array
    {
        $headers = [
            'No',
            'Dosen',
            'Mata Kuliah',
//            'Kelas',
            'Jumlah Hadir',
            'Jumlah Tidak Hadir',
            'Jumlah Izin',
            'Jumlah Ganti Jadwal',
            'Total Upah',
        ];

        return [
            [
                'Rekap Absensi Dosen',
            ],
            $headers,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['align' => ['center' => true]],
            2 => ['align' => ['center' => true]],
        ];
    }

    public function columnFormats(): array
    {
        //
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): \Illuminate\Support\Collection
    {
//        return $this->query->map(function ($item, $key) {
//            $lecturerCourse = LecturerCourse::where('user_id', $item->id)->where('academic_year', date('Y'))->get();
//            $getLecturerCoursesByCurrentSemester = Helper::getCurrentSemester($lecturerCourse);
//
//            $courses = $getLecturerCoursesByCurrentSemester->map(function ($course) {
//                return $course->course->name;
//            })->implode(', ');
//
//            return [
//                $key + 1,
//                $item->lecturerProfile->name,
//                $courses,
//            ];
//        });

        $result = collect();

        $this->query->each(function ($item, $key) use ($result) {
            $lecturerCourse = LecturerCourse::where('user_id', $item->id)->where('academic_year', date('Y'))->get();
            $getLecturerCoursesByCurrentSemester = Helper::getCurrentSemester($lecturerCourse);

            $result->push([
                'no' => $key + 1,
                'lecturer' => $item->lecturerProfile->name,
                'course' => null,
                'attendances' => null,
                'total_present' => null,
                'total_absent' => null,
                'total_excused' => null,
            ]);

            $getLecturerCoursesByCurrentSemester->each(function ($course) use ($result) {
                $attendances = collect();

                $totalPresent = 0;
                $totalAbsent = 0;
                $totalExcused = 0;

                foreach ($course->schedules as $schedule)
                {
                    $scheduleAttendance = $schedule->attendances;
                    $attendances = $attendances->merge($scheduleAttendance);

                    $pending = $scheduleAttendance->where('status', \App\States\AttendanceStatus\Pending::$name)
                        ->where('expired_at', '<', now())->count();

                    $totalAbsent += $scheduleAttendance->where('status', \App\States\AttendanceStatus\Absent::$name)->count() + $pending;
                    $totalPresent += $scheduleAttendance->where('status', \App\States\AttendanceStatus\Present::$name)->count();
                    $totalExcused += $scheduleAttendance->where('status', \App\States\AttendanceStatus\Excused::$name)->count();
                }

//                dd($attendances);

                $result->push([
                    'no' => null,
                    'lecturer' => null,
                    'course' => $course->course->name,
//                    'class' => $course->class
                    'total_present' => $totalPresent,
                    'total_absent' => $totalAbsent,
                    'total_excused' => $totalExcused,
                ]);
            });
        });

        return $result;
    }
}
