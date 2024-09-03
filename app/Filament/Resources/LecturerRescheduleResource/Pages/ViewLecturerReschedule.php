<?php

namespace App\Filament\Resources\LecturerRescheduleResource\Pages;

use App\Filament\Resources\LecturerRescheduleResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Validation\Rules\In;

class ViewLecturerReschedule extends ViewRecord
{
    protected static string $resource = LecturerRescheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Jadwal Baru')
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
                        Infolists\Components\TextEntry::make('attendanceLecturer.expired_at')
                            ->label('Batas Absen')
                            ->formatStateUsing(fn($record) => \Carbon\Carbon::parse($record->attendanceLecturer->expired_at)->format('H:i')),
                        Infolists\Components\TextEntry::make('classroom')
                            ->label('Ruang Kelas'),
                        Infolists\Components\TextEntry::make('attendanceLecturer.status')
                            ->label('Status Absensi')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                \App\States\AttendanceStatus\Absent::$name => 'danger',
                                \App\States\AttendanceStatus\Excused::$name => 'warning',
                                \App\States\AttendanceStatus\Present::$name => 'success',
                                \App\States\AttendanceStatus\Pending::$name => 'gray'
                            }),
                        Infolists\Components\TextEntry::make('reschedule_note')
                            ->label('Reschedule'),
                    ]),
                Infolists\Components\Section::make('Informasi Jadwal Lama')
                    ->schema([
                        Infolists\Components\TextEntry::make('extras')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $payload = json_decode($record->extras, true);

                                return implode(', ', [
                                    "Jam Mulai: " . $payload['original_start'],
                                    "Jam Selesai: " . $payload['original_end'],
                                    "Tanggal Pembelajaran: " . $payload['original_date'],
                                ]);
                            })
                    ]),
            ]);
    }
}
