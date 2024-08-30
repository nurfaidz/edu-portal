<?php

namespace App\Filament\Resources\LecturerCourseResource\Pages;

use App\Enums\Courses\Type;
use App\Filament\Resources\LecturerCourseResource;
use App\Models\LecturerCourse;
use App\States\AttendanceStatus\Absent;
use App\States\AttendanceStatus\Excused;
use App\States\AttendanceStatus\Present;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
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
                ->modalHeading('Lakukan Absensi'),
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
                            ->color(fn (string $state, $record): string => match ($state) {
                                Absent::$name => 'danger',
                                Excused::$name => 'warning',
                                Present::$name => 'success',
                                $record->attendanceLecturer->expired_at < now() && is_null($record->attendanceLecturer->status) => 'secondary',
                            })
                    ])
            ]);
    }
}
