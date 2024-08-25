<?php

namespace App\Filament\Resources\AttendanceLecturerResource\Pages;

use App\Filament\Resources\AttendanceLecturerResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendanceLecturer extends ViewRecord
{
    protected static string $resource = AttendanceLecturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
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
                            }),
                    ])
            ]);
    }
}
