<?php

namespace App\Filament\Resources\AttendanceLecturerResource\Pages;

use App\Filament\Resources\AttendanceLecturerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendanceLecturer extends ViewRecord
{
    protected static string $resource = AttendanceLecturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
