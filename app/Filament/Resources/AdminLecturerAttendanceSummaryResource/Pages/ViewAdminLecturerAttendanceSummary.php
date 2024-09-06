<?php

namespace App\Filament\Resources\AdminLecturerAttendanceSummaryResource\Pages;

use App\Enums\Courses\Type;
use App\Filament\Resources\AdminLecturerAttendanceSummaryResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Resources\Pages\ViewRecord;

class ViewAdminLecturerAttendanceSummary extends ViewRecord
{
    protected static string $resource = AdminLecturerAttendanceSummaryResource::class;

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
                Infolists\Components\Section::make('Informasi Dosen')
                    ->schema([
                        Infolists\Components\TextEntry::make('npp')
                            ->label('NIP'),
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nama Dosen'),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AdminLecturerAttendanceSummaryResource\Widgets\AbsentOverview::make(),
        ];
    }
}
