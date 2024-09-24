<?php

namespace App\Exports;

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
            'Kelas',
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
        return $this->query->map(function ($item, $key) {
//            $lecturerCourse = LecturerCourse::where('lecturer_id', $item->id)->get();
//            $totalAttendance = 0;
//            $totalAbsent = 0;
//            $totalPermission = 0;
//            $totalReschedule = 0;
//            $totalSalary = 0;
//
//            foreach ($lecturerCourse as $course) {
//                $totalAttendance += $course->attendance;
//                $totalAbsent += $course->absent;
//                $totalPermission += $course->permission;
//                $totalReschedule += $course->reschedule;
//                $totalSalary += $course->salary;
//            }
//
//            return [
//                $key + 1,
//                $item->name,
//                $lecturerCourse->first()->course->name,
//                $lecturerCourse->first()->course->classroom,
//                $totalAttendance,
//                $totalAbsent,
//                $totalPermission,
//                $totalReschedule,
//                $totalSalary,
//            ];
            $lecturerCourses = LecturerCourse::where('user_id', $item->id)->get();
//            foreach ($lecturerCourses as $course) {
//
//            }

            return [
                $key + 1,
                $item->lecturerProfile->name,
                $lecturerCourses->first() ? $lecturerCourses->first()->course->name : '-',
            ];
        });
    }
}
