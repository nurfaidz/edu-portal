<?php

namespace App\Filament\Resources\AdminLecturerAttendanceSummaryResource\Pages;

use App\Enums\Courses\Type;
use App\Filament\Resources\AdminLecturerAttendanceSummaryResource;
use App\Settings\PayrollLecturer;
use App\States\AttendanceStatus\Present;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAdminLecturerAttendanceSummary extends ViewRecord
{
    protected static string $resource = AdminLecturerAttendanceSummaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('calculatePayroll')
                ->label('Kalkulasi Upah')
                ->modalHeading('Kalkulasi Upah Dosen')
                ->requiresConfirmation()
                ->modalSubheading('Upah dosen akan dikalkulasi dari data absensi dosen dari tahun akademik sekarang dan semester terbaru.')
                ->color('info')
                ->action(function () {
                    try {
                        $oddDateStart = date('Y') . '-02-01';
                        $oddDateEnd = date('Y') . '-08-31';
                        $evenDateStart = date('Y') . '-09-01';
                        $evenDateEnd = date('Y') . '-12-31';
                        $now = date('Y-m-d');

                        if ($now >= $oddDateStart && $now <= $oddDateEnd) {
                            $lecturerCourse = \App\Models\LecturerCourse::where('user_id', $this->record->user_id)
                                ->where('academic_year', now()->year)
                                ->whereRaw('MOD(semester, 2) <> 0')
                                ->first();
                        } elseif ($now >= $evenDateStart && $now <= $evenDateEnd) {
                            $lecturerCourse = \App\Models\LecturerCourse::where('user_id', $this->record->user_id)
                                ->where('academic_year', now()->year)
                                ->whereRaw('MOD(semester, 2) = 0')
                                ->first();
                        }

                        if (!$lecturerCourse) {
                            Notification::make()
                                ->title('Tidak Dapat Melakukan Kalkulasi Upah')
                                ->body('Dosen belum memiliki mata kuliah pada tahun akademik ini.')
                                ->danger()
                                ->send();

                            return;
                        }

                        if ($now >= $oddDateStart && $now <= $oddDateEnd) {
                            $schedules = $lecturerCourse->schedules()->whereRaw('MOD(semester, 2) <> 0')->get();
                        } elseif ($now >= $evenDateStart && $now <= $evenDateEnd) {
                            $schedules = $lecturerCourse->schedules()->whereRaw('MOD(semester, 2) = 0')->get();
                        }

                        $attendances = collect();

                        foreach ($schedules as $schedule) {
                            $scheduleAttendance = $schedule->attendances;
                            $attendances = $attendances->merge($scheduleAttendance);
                        }

                        $attendanceWithPresentStatus = $attendances->filter(function ($attendance) {
                            return $attendance->status === Present::$name;
                        })->count();

                        $transportSalary = $attendanceWithPresentStatus * app(PayrollLecturer::class)->amount_transport_salary;
                        $sksSalary = $lecturerCourse->course->credits * app(PayrollLecturer::class)->amount_sks_salary;

                        \App\Models\LecturerSalary::updateOrCreate(
                            [
                                'user_id' => $this->record->user_id,
                                'semester' => $lecturerCourse->semester,
                                'academic_year' => $lecturerCourse->academic_year,
                            ],
                            [
                                'amount_salary_transport' => $transportSalary,
                                'amount_salary_sks' => $sksSalary,
                                'total_salary' => $transportSalary + $sksSalary,
                            ]
                        );

                        Notification::make()
                            ->title('Kalkulasi Upah Berhasil')
                            ->body('Upah dosen berhasil dikalkulasi.')
                            ->success()
                            ->send();

                        return;
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Kalkulasi Upah Gagal')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }
                }),

        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Dosen')
                    ->schema([
                        Infolists\Components\TextEntry::make('npp')
                            ->label('NIP'),
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nama Dosen'),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AdminLecturerAttendanceSummaryResource\Widgets\PayrollOverview::make(),
            AdminLecturerAttendanceSummaryResource\Widgets\AttendanceOverview::make(),
        ];
    }
}
