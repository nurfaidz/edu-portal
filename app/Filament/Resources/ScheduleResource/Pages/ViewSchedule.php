<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Enums\Courses\Type;
use App\Filament\Resources\ScheduleResource;
use App\Models\LecturerCourse;
use Filament\Actions;
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
                ->action(function() {
                    try {
                        if ($this->record->schedules()->exists()) {
                            $this->record->schedules()->delete();

                            Notification::make()
                                ->title('Berhasil menghapus')
                                ->body('Jadwal berhasil dihapus')
                                ->success()
                                ->send();

                            return redirect()->route('filament.admin.resources.schedules.view', $this->record);
                        }

                        Notification::make()
                            ->title('Belum ada jadwal')
                            ->danger()
                            ->send();

                        return;

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal menghapus')
                            ->body('Terjadi kesalahan saat menghapus jadwal')
                            ->danger()
                            ->send();

                        return;
                    }
                }),
            Actions\Action::make('addSchedule')
                ->label('Tambah Jadwal')
                ->modalHeading('Tambah Jadwal')
                ->modalDescription('Jadwal otomatis membuat 14x pertemuan sesuai dengan hari dan jam yang dipilih')
                ->modalWidth('2xl')
                ->fillForm(fn (LecturerCourse $record) => [
                    'lecturer_id' => $record->lecturer_id,
                    'course_id' => $record->course_id,
                ])
                ->form([
                    Select::make('classroom')
                            ->label('Ruang Kelas')
                            ->options([
                                \App\Enums\Courses\Classroom::A->value => 'Kelas A',
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

                        \DB::transaction(function () use ($start, $start_time, $end_time, $classroom) {
                            for ($i = 0; $i < 14; $i++) {
                                $schedule = \App\Models\Schedule::create([
                                    'lecturer_course_id' => $this->record->id,
                                    'classroom' => $classroom,
                                    'date' => $start->copy()->addWeek($i)->format('Y-m-d'),
                                    'start' => $start_time,
                                    'end' => $end_time,
                                ]);

                                $expired_at = $start->copy()->addWeek($i)->addMinutes(20)->format('Y-m-d H:i:s');

                                \App\Models\Attendance::create([
                                    'schedule_id' => $schedule->id,
                                    'expired_at' => $expired_at,
                                ]);
                            }
                        });

                        Notification::make()
                            ->title('Jadwal berhasil ditambahkan')
                            ->body('Jadwal berhasil ditambahkan sebanyak 14x pertemuan')
                            ->success()
                            ->send();

                        return redirect()->route('filament.admin.resources.schedules.view', $this->record);
                    } catch (\Exception $e) {
                        dd($e->getMessage(), \Carbon\Carbon::parse('07:00:00')->addMinutes(15));
                        Notification::make()
                            ->title('Terjadi kesalahan')
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
                    ])
                    ->columns(2),
                ]);
    }
}
