<?php

namespace App\Filament\Resources\AttendanceLecturerResource\Pages;

use App\Filament\Resources\AttendanceLecturerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceLecturers extends ListRecords
{
    protected static string $resource = AttendanceLecturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
