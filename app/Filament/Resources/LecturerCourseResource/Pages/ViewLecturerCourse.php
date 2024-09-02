<?php

namespace App\Filament\Resources\LecturerCourseResource\Pages;

use App\Enums\Courses\Type;
use App\Filament\Resources\LecturerCourseResource;
use App\Models\LecturerCourse;
use App\States\AttendanceStatus\Absent;
use App\States\AttendanceStatus\Excused;
use App\States\AttendanceStatus\Pending;
use App\States\AttendanceStatus\Present;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewLecturerCourse extends ViewRecord
{
    protected static string $resource = LecturerCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Buat Barcode')
                ->label('Buat Barcode')
                ->action(function () {
                    Notification::make()
                        ->title('Get Will Soon')
                        ->body('Fitur ini akan segera hadir.')
                        ->warning()
                        ->send();
                }),
            Actions\Action::make('attendance')
                ->label('Lakukan Absensi')
                ->modalHeading('Lakukan Absensi')
                ->form([
                    Forms\Components\Select::make('absent')
                        ->label('')
                        ->options([
                            Present::$name => 'Hadir',
                            Absent::$name => 'Tidak Hadir',
                            Excused::$name => 'Izin',
                        ])
                ])
                ->action(function (array $data, $record) {

                    try {
                        $date = \Carbon\Carbon::parse($record->date);
                        $start = \Carbon\Carbon::parse($record->start);

                        $startDate = $date->setTimeFrom($start)->subMinutes(10);

                        if (now() < $startDate) {
                            Notification::make()
                                ->title('Anda terlalu cepat absen')
                                ->body('Anda dapat melakukan absensi 10 menit sebelum jadwal dimulai.')
                                ->danger()
                                ->send();

                            return;
                        } elseif (now() > $record->attendanceLecturer->expired_at) {
                            Notification::make()
                                ->title('Anda terlambat absen')
                                ->body('Anda tidak bisa melakukan absensi karena sudah melewati batas waktu absen.')
                                ->danger()
                                ->send();

                            return;
                        } else {
                            \DB::transaction(function () use ($data, $record) {
                                $record->attendanceLecturer->update([
                                    'status' => $data['absent'],
                                ]);
                            });

                            Notification::make()
                                ->title('Absensi berhasil')
                                ->body('Absensi berhasil disimpan.')
                                ->success()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Absensi gagal')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn($record) => $record->attendanceLecturer->status === Pending::$name),
            Actions\Action::make('addExpire')
                ->label('Tambah Batas Absen')
                ->modalHeading('Tambah Batas Absen')
                ->form([
                    Forms\Components\TextInput::make('expired_at')
                        ->label('Menit Batas Absen')
                        ->helperText(' *Masukkan dalam menit, contoh 30 = 30 menit')
                        ->minValue(1)
                        ->required()
                ])
                ->action(function (array $data, $record) {
                    try {
                        $date = \Carbon\Carbon::parse($record->date);
                        $expiredAt = \Carbon\Carbon::parse($record->attendanceLecturer->expired_at);

                        $expiredDate = $date->setTimeFrom($expiredAt);
                        $addExpired = $expiredDate->addMinutes((int) $data['expired_at']);

                        \DB::transaction(function () use ($addExpired, $record) {
                            $record->attendanceLecturer->update([
                                'expired_at' => $addExpired,
                            ]);

                            $record->attendanceStudents->each(function ($attendanceStudent) use ($addExpired) {
                                $attendanceStudent->update([
                                    'expired_at' => $addExpired,
                                ]);
                            });
                        });

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal menambah batas absen')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('changeSchedule')
                ->label('Ganti Jadwal')
                ->modalHeading('Ganti Jadwal Pembelajaran')
                ->form([
                    Forms\Components\DatePicker::make('date')
                        ->label('Tanggal')
                        ->helperText('Pilih hari dan tanggal untuk pertemuan pengganti')
                        ->native(false)
                        ->required(),
                    Forms\Components\Section::make('Jam Pertemuan')
                        ->columns(2)
                        ->description('Pilih jam pertemuan pengganti')
                        ->schema([
                            Forms\Components\TimePicker::make('start')
                                ->label('Jam Mulai')
                                ->native(false)
                                ->required(),
                            Forms\Components\TimePicker::make('end')
                                ->label('Jam Selesai')
                                ->native(false)
                                ->required(),
                        ]),
                ])
                ->action(function (array $data, $record) {
                    try {
                        \DB::transaction(function () use ($data, $record) {
                            $record->date = $data['date'];
                            $record->start = $data['start'];
                            $record->end = $data['end'];
                            $record->extras = [
                                'original_start' => $record->start,
                                'original_end' => $record->end,
                                'original_date' => $record->date,
                            ];
                            $record->is_reschedule = true;

                            dd($record); // Check if the data is correct, but don't already save it
                            $record->save();
                        });

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal mengganti jadwal')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn($record) => $record->attendanceLecturer->status === Pending::$name),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('lecturerCourse.course.name')
                            ->label('Mata Kuliah'),
                        Infolists\Components\TextEntry::make('date')
                            ->label('Tanggal Pembelajaran')
                            ->formatStateUsing(fn($record) => \Carbon\Carbon::parse($record->date)->locale('id')->isoFormat('dddd, D MMMM Y')),
                        Infolists\Components\TextEntry::make('start')
                            ->label('Jam Mulai')
                            ->formatStateUsing(fn($record) => \Carbon\Carbon::parse($record->start)->format('H:i')),
                        Infolists\Components\TextEntry::make('end')
                            ->label('Jam Selesai')
                            ->formatStateUsing(fn($record) => \Carbon\Carbon::parse($record->end)->format('H:i')),
                        Infolists\Components\TextEntry::make('id')
                            ->label('Batas Absen')
                            ->formatStateUsing(fn($record) => \Carbon\Carbon::parse($record->attendanceLecturer->expired_at)->format('H:i')),
                        Infolists\Components\TextEntry::make('classroom')
                            ->label('Ruang Kelas'),
                        Infolists\Components\TextEntry::make('attendanceLecturer.status')
                            ->label('Status Absensi')
                            ->badge()
                            ->color(fn(string $state, $record): string => match ($state) {
                                Absent::$name => 'danger',
                                Excused::$name => 'warning',
                                Present::$name => 'success',
                                Pending::$name => 'gray'
                            })
                    ])
            ]);
    }
}
