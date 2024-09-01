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
                        if (now() < $record->start) {
                            Notification::make()
                                ->title('Anda terlalu cepat absen')
                                ->body('Anda tidak bisa melakukan absensi karena belum memasuki batas waktu absen.')
                                ->danger()
                                ->send();

                            return;
                        } elseif (now() > $record->end) {
                            Notification::make()
                                ->title('Anda terlambat absen')
                                ->body('Anda tidak bisa melakukan absensi karena sudah melewati batas waktu absen.')
                                ->danger()
                                ->send();

                            return;
                        } else {
                            \DB::transaction(function () use ($data, $record) {
                                $record->update([
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
                }),
            Actions\Action::make('changeSchedule')
                ->label('Ganti Jadwal')
                ->modalHeading('Ganti Jadwal Pembelajaran')
                ->action(function () {
                    Notification::make()
                        ->title('Get Will Soon')
                        ->body('Fitur ini akan segera hadir.')
                        ->warning()
                        ->send();
                }),
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
