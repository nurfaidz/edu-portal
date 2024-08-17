<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Resources\Pages\ViewRecord;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Mahasiswa')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Username'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email'),
                        Infolists\Components\Group::make()
                            ->relationship('studentProfile')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Nama Mahasiswa'),
                                Infolists\Components\TextEntry::make('nim')
                                    ->label('Nomor Induk Mahasiswa'),
                            ]),
                    ])
            ]);
    }
}
