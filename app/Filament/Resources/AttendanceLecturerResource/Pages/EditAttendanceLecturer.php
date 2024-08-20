<?php

namespace App\Filament\Resources\AttendanceLecturerResource\Pages;

use App\Filament\Resources\AttendanceLecturerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceLecturer extends EditRecord
{
    protected static string $resource = AttendanceLecturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
