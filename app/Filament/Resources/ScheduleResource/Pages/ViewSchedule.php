<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Enums\Courses\Type;
use App\Filament\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSchedule extends ViewRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus Jadwal')
                ->form([
                    Forms\Components\Select::make('classroom')
                        ->label('Ruang Kelas')
                        ->options([
                            \App\Enums\Courses\Classroom::A->value => 'Kelas A',
                            \App\Enums\Courses\Classroom::B->value => 'Kelas B',
                        ]),
                ])
                ->action(function (array $data) {
                    try {
                        $schedules = $this->record->schedules()->where('classroom', $data['classroom'])->get();

                        if ($schedules->isEmpty()) {
                            Notification::make()
                                ->title('Belum ada jadwal untuk ruang kelas ini')
                                ->danger()
                                ->send();

                            return;
                        }

                        foreach ($schedules as $schedule) {
                            $schedule->attendance()->delete();
                            $schedule->delete();
                        }

                        Notification::make()
                            ->title('Jadwal berhasil dihapus')
                            ->success()
                            ->send();

                        return redirect()->route('filament.admin.resources.schedules.view', $this->record);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal menghapus')
                            ->body('Terjadi kesalahan saat menghapus jadwal')
                            ->danger()
                            ->send();

                        return;
                    }
                }),
            Actions\Action::make('addScheduleForClassroomA')
                ->label('Tambah Jadwal Kelas A')
                ->modalHeading('Tambah Jadwal Kelas A')
                ->modalDescription('Jadwal otomatis membuat 14x pertemuan sesuai dengan hari dan jam yang dipilih')
                ->modalWidth('2xl')
                ->form([
                    DatePicker::make('startDate')
                        ->label('Tanggal')
                        ->helperText('Pilih hari dan tanggal untuk pertemuan pertama saja, jadwal akan dibuat otomatis untuk pertemuan selanjutnya setiap minggu')
                        ->native(false)
                        ->required(),
                    Section::make('Jam Pertemuan')
                        ->columns(2)
                        ->description('Pilih jam pertemuan untuk 14x pertemuan selanjutnya')
                        ->schema([
                            TimePicker::make('start')
                                ->label('Jam Mulai')
                                ->native(false)
                                ->required(),
                            TimePicker::make('end')
                                ->label('Jam Selesai')
                                ->native(false)
                                ->required(),
                        ]),
                ])
                ->action(function (array $data) {
                    try {
                        $classroom = \App\Enums\Courses\Classroom::A->value;
                        $startDate = $data['startDate'];
                        $start_time = $data['start'];
                        $end_time = $data['end'];

                        $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $startDate . ' ' . $start_time);

                        \DB::transaction(function () use ($start, $start_time, $end_time, $classroom) {
                            for ($i = 0; $i < 14; $i++) {
                                $schedule = \App\Models\Schedule::create([
                                    'lecturer_course_id' => $this->record->id,
                                    'classroom' => $classroom,
                                    'date' => $start->copy()->addWeek($i)->format('Y-m-d'),
                                    'start' => $start_time,
                                    'end' => $end_time,
                                    'class' => $classroom,
                                    'semester' => $this->record->semester,
                                    'academic_year' => $this->record->academic_year,
                                ]);

                                $expired_at = $start->copy()->addWeek($i)->addMinutes(20)->format('Y-m-d H:i:s');

                                \App\Models\Attendance::create([
                                    'attendable_id' => $this->record->user_id,
                                    'attendable_type' => '\App\Models\Lecturer',
                                    'schedule_id' => $schedule->id,
                                    'expired_at' => $expired_at,
                                ]);

                                foreach ($this->record->course->studentCourses as $studentCourse) {
                                    \App\Models\Attendance::create([
                                        'attendable_id' => $studentCourse->student->id,
                                        'attendable_type' => '\App\Models\Student',
                                        'schedule_id' => $schedule->id,
                                        'expired_at' => $expired_at,
                                    ]);
                                }
                            }
                        });

                        Notification::make()
                            ->title('Jadwal berhasil ditambahkan untuk Kelas A')
                            ->body('Jadwal berhasil ditambahkan sebanyak 14x pertemuan')
                            ->success()
                            ->send();

                        return redirect()->route('filament.admin.resources.schedules.view', $this->record);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Terjadi kesalahan')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }
                })
                ->hidden(fn() => $this->record->schedules()->where('classroom', \App\Enums\Courses\Classroom::A->value)->exists()),
            Actions\Action::make('addScheduleForClassroomB')
                ->label('Tambah Jadwal Kelas B')
                ->modalHeading('Tambah Jadwal Kelas B')
                ->modalDescription('Jadwal otomatis membuat 14x pertemuan sesuai dengan hari dan jam yang dipilih')
                ->modalWidth('2xl')
                ->form([
                    DatePicker::make('startDate')
                        ->label('Tanggal')
                        ->helperText('Pilih hari dan tanggal untuk pertemuan pertama saja, jadwal akan dibuat otomatis untuk pertemuan selanjutnya setiap minggu')
                        ->native(false)
                        ->required(),
                    Section::make('Jam Pertemuan')
                        ->columns(2)
                        ->description('Pilih jam pertemuan untuk 14x pertemuan selanjutnya')
                        ->schema([
                            TimePicker::make('start')
                                ->label('Jam Mulai')
                                ->native(false)
                                ->required(),
                            TimePicker::make('end')
                                ->label('Jam Selesai')
                                ->native(false)
                                ->required(),
                        ]),
                ])
                ->action(function (array $data) {
                    try {
                        $classroom = \App\Enums\Courses\Classroom::B->value;
                        $startDate = $data['startDate'];
                        $start_time = $data['start'];
                        $end_time = $data['end'];

                        $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $startDate . ' ' . $start_time);

                        \DB::transaction(function () use ($start, $start_time, $end_time, $classroom) {
                            for ($i = 0; $i < 14; $i++) {
                                $schedule = \App\Models\Schedule::create([
                                    'lecturer_course_id' => $this->record->id,
                                    'classroom' => $classroom,
                                    'date' => $start->copy()->addWeek($i)->format('Y-m-d'),
                                    'start' => $start_time,
                                    'end' => $end_time,
                                    'class' => $classroom,
                                    'semester' => $this->record->semester,
                                    'academic_year' => $this->record->academic_year,
                                ]);

                                $expired_at = $start->copy()->addWeek($i)->addMinutes(20)->format('Y-m-d H:i:s');

                                \App\Models\Attendance::create([
                                    'attendable_id' => $this->record->user_id,
                                    'attendable_type' => '\App\Models\Lecturer',
                                    'schedule_id' => $schedule->id,
                                    'expired_at' => $expired_at,
                                ]);

                                foreach ($this->record->course->studentCourses as $studentCourse) {
                                    \App\Models\Attendance::create([
                                        'attendable_id' => $studentCourse->student->id,
                                        'attendable_type' => '\App\Models\Student',
                                        'schedule_id' => $schedule->id,
                                        'expired_at' => $expired_at,
                                    ]);
                                }
                            }
                        });

                        Notification::make()
                            ->title('Jadwal berhasil ditambahkan untuk Kelas B')
                            ->body('Jadwal berhasil ditambahkan sebanyak 14x pertemuan')
                            ->success()
                            ->send();

                        return redirect()->route('filament.admin.resources.schedules.view', $this->record);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Terjadi kesalahan')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }
                })
                ->hidden(fn() => $this->record->schedules()->where('classroom', \App\Enums\Courses\Classroom::B->value)->exists()),
            Actions\Action::make('editSchedule')
                ->label('Edit Jadwal')
                ->modalHeading('Edit Jadwal')
                ->modalWidth('2xl')
                ->fillForm([
                    'classroom' => $this->record->schedules->isNotEmpty() ? $this->record->schedules->first()->classroom : null,
                    'startDate' => $this->record->schedules->isNotEmpty() ? $this->record->schedules->first()->date : null,
                    'start' => $this->record->schedules->isNotEmpty() ? $this->record->schedules->first()->start : null,
                    'end' => $this->record->schedules->isNotEmpty() ? $this->record->schedules->first()->end : null,
                ])
                ->form([
                    Select::make('classroom')
                        ->label('Ruang Kelas')
                        ->options([
                            \App\Enums\Courses\Classroom::A->value => 'Kelas A',
                            \App\Enums\Courses\Classroom::B->value => 'Kelas B',
                        ]),
                    DatePicker::make('startDate')
                        ->label('Tanggal')
                        ->helperText('Pilih hari dan tanggal untuk pertemuan pertama saja, jadwal akan dibuat otomatis untuk pertemuan selanjutnya setiap minggu')
                        ->native(false)
                        ->required(),
                    Section::make('Jam Pertemuan')
                        ->columns(2)
                        ->description('Pilih jam pertemuan untuk 14x pertemuan selanjutnya')
                        ->schema([
                            TimePicker::make('start')
                                ->label('Jam Mulai')
                                ->native(false)
                                ->required(),
                            TimePicker::make('end')
                                ->label('Jam Selesai')
                                ->native(false)
                                ->required(),
                        ]),
                ])
                ->action(function (array $data) {
                    try {
                        $classroom = $data['classroom'];
                        $startDate = $data['startDate'];
                        $start_time = $data['start'];
                        $end_time = $data['end'];

                        $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $startDate . ' ' . $start_time);

                        $schedules = $this->record->schedules()->where('classroom', $classroom)->get();

                        if ($schedules->isEmpty()) {
                            Notification::make()
                                ->title('Ruang Kelas tidak ditemukan')
                                ->danger()
                                ->send();

                            return;
                        }

                        foreach ($schedules as $schedule) {
                            $schedule->attendance()->delete();
                            $schedule->delete();
                        }

                        \DB::transaction(function () use ($start, $start_time, $end_time, $classroom) {

                            for ($i = 0; $i < 14; $i++) {
                                $schedule = \App\Models\Schedule::create([
                                    'lecturer_course_id' => $this->record->id,
                                    'classroom' => $classroom,
                                    'date' => $start->copy()->addWeek($i)->format('Y-m-d'),
                                    'start' => $start_time,
                                    'end' => $end_time,
                                    'class' => $classroom,
                                    'semester' => $this->record->semester,
                                    'academic_year' => $this->record->academic_year,
                                ]);

                                $expired_at = $start->copy()->addWeek($i)->addMinutes(20)->format('Y-m-d H:i:s');

                                \App\Models\Attendance::create([
                                    'attendable_id' => $this->record->user_id,
                                    'attendable_type' => '\App\Models\Lecturer',
                                    'schedule_id' => $schedule->id,
                                    'expired_at' => $expired_at,
                                ]);

                                foreach ($this->record->course->studentCourses as $studentCourse) {
                                    \App\Models\Attendance::create([
                                        'attendable_id' => $studentCourse->student->id,
                                        'attendable_type' => '\App\Models\Student',
                                        'schedule_id' => $schedule->id,
                                        'expired_at' => $expired_at,
                                    ]);
                                }
                            }
                        });

                        Notification::make()
                            ->title('Jadwal berhasil diubah')
                            ->body('Jadwal berhasil diubah sebanyak 14x pertemuan')
                            ->success()
                            ->send();

                        return redirect()->route('filament.admin.resources.schedules.view', $this->record);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Terjadi kesalahan')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }
                })
                ->hidden(fn() => !$this->record->schedules()->exists()),

        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Dosen & Mata Kuliah')
                    ->schema([
                        Infolists\Components\TextEntry::make('lecturer.lecturerProfile.npp')
                            ->label('Kode Dosen'),
                        Infolists\Components\TextEntry::make('lecturer.lecturerProfile.name')
                            ->label('Nama Dosen'),
                        Infolists\Components\TextEntry::make('course.code')
                            ->label('Kode Mata Kuliah'),
                        Infolists\Components\TextEntry::make('course.name')
                            ->label('Mata Kuliah'),
                        Infolists\Components\TextEntry::make('course.credits')
                            ->label('SKS'),
                        Infolists\Components\TextEntry::make('course.type')
                            ->label('Tipe')
                            ->formatStateUsing(function ($record) {
                                if ($record->course->type === Type::Mandatory->value) {
                                    return 'Mata Kuliah Wajib';
                                } else {
                                    return 'Mata Kuliah Pilihan';
                                }
                            }),
                        Infolists\Components\TextEntry::make('semester')
                            ->label('Semester'),
                        Infolists\Components\TextEntry::make('academic_year')
                            ->label('Tahun Akademik'),
                    ])
                    ->columns(2),
            ]);
    }
}
