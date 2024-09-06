<?php

namespace App\Filament\Resources\AdminLecturerAttendanceSummaryResource\Pages;

use App\Filament\Resources\AdminLecturerAttendanceSummaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminLecturerAttendanceSummaries extends ListRecords
{
    protected static string $resource = AdminLecturerAttendanceSummaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
