<?php

namespace App\Filament\Resources\LecturerResource\Pages;

use App\Filament\Resources\LecturerResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\LecturerResource\Pages;

class ViewLecturer extends ViewRecord
{
    protected static string $resource = LecturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Dosen')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Username'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email'),
                        Infolists\Components\Group::make()
                            ->relationship('lecturerProfile')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Nama Dosen'),
                                Infolists\Components\TextEntry::make('npp')
                                    ->label('NPP'),
                            ]),
                    ])
            ]);
    }
}
