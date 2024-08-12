<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Mahasiswa';

    protected static ?string $label = 'Mahasiswa';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dosen')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nomor Induk Mahasiswa')
                        ->required(),
                    Forms\Components\TextInput::make('student_name')
                        ->label('Nama Mahasiswa')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->required(),
                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->visible(fn($livewire) => $livewire instanceof Pages\CreateStudent)
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nomor Induk Mahasiswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student_name')
                    ->label('Nama Mahasiswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'view' => Pages\ViewStudent::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->student();
    }
}
