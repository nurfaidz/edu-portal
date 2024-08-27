<?php

namespace App\Filament\Resources\AttendanceSummaryResource\Pages;

use App\Filament\Resources\AttendanceSummaryResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendanceSummary extends ViewRecord
{
    protected static string $resource = AttendanceSummaryResource::class;

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
                Infolists\Components\Section::make('Informasi Dosen')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('npp')
                            ->label('NPP'),
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nama Dosen'),
                    ])
            ]);
    }
}
