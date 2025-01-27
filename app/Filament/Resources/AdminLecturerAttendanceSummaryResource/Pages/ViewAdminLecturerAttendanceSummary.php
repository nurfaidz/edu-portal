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
use Illuminate\Support\Facades\DB;

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
                        DB::beginTransaction();

                        $monthDay = date('m-d');
                        $lecturerCourse = null;


                        $lecturerCourse = \App\Models\LecturerCourse::where('user_id', $this->record->user_id)
                            ->whereRaw($monthDay >= '02-01' && $monthDay <= '08-31'
                                ? 'MOD(semester, 2) = 0'  // Semester genap
                                : 'MOD(semester, 2) <> 0' // Semester ganjil
                            )
                            ->where('academic_year', now()->year)
                            ->first();

                        if (!$lecturerCourse) {
                            Notification::make()
                                ->title('Tidak Dapat Melakukan Kalkulasi Upah')
                                ->body('Dosen belum memiliki mata kuliah pada tahun akademik ini.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $schedules = $lecturerCourse->schedules()->get();

                        $attendances = collect();

                        foreach ($schedules as $schedule) {
                            $scheduleAttendance = $schedule->attendances;
                            $attendances = $attendances->merge($scheduleAttendance)->where('attendable_id', $this->record->user_id);
                        }

                        $attendanceWithPresentStatus = $attendances->filter(function ($attendance) {
                            return $attendance->status === Present::$name;
                        })->count();

                        $transportSalary = $attendanceWithPresentStatus * app(PayrollLecturer::class)->amount_transport_salary;
                        $sksSalary = $lecturerCourse->course->credits * app(PayrollLecturer::class)->amount_sks_salary;

                        $saveSalary = \App\Models\LecturerSalary::where('user_id', $this->record->user_id)
                            ->first();

                        $semester = null;
                            if ($monthDay >= '02-01' && $monthDay <= '08-31') {
                                $semester = 2;
                            } else {
                                $semester = 1;
                            }

                        if ($saveSalary) {
                            $saveSalary->update([
                                'semester' => $semester,
                                'amount_salary_transport' => $transportSalary,
                                'amount_salary_sks' => $sksSalary,
                                'total_salary' => $transportSalary + $sksSalary,
                            ]);
                        } else {
                            \App\Models\LecturerSalary::create([
                                'user_id' => $this->record->user_id,
                                'semester' => $semester,
                                'academic_year' => $lecturerCourse->academic_year,
                                'amount_salary_transport' => $transportSalary,
                                'amount_salary_sks' => $sksSalary,
                                'total_salary' => $transportSalary + $sksSalary,
                            ]);
                        }
                        DB::commit();

                        Notification::make()
                            ->title('Kalkulasi Upah Berhasil')
                            ->body('Upah dosen berhasil dikalkulasi.')
                            ->success()
                            ->send();

                        return;
                    } catch (\Exception $e) {
                        DB::rollBack();

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
