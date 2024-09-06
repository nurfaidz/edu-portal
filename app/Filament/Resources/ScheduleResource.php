<?php

namespace App\Filament\Resources;

use App\Enums\Roles\Role;
use App\Filament\Resources\ScheduleResource\Pages;
use App\Filament\Resources\ScheduleResource\RelationManagers;
use App\Models\LecturerCourse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScheduleResource extends Resource
{
    protected static ?string $model = LecturerCourse::class;

    protected static ?string $navigationLabel = 'Jadwal';

    protected static ?string $label = 'Jadwal';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lecturer.lecturerProfile.name')
                    ->label('Dosen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Mata Kuliah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->searchable(),
                Tables\Columns\TextColumn::make('academic_year')
                    ->label('Tahun Akademik')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SchedulesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'view' => Pages\ViewSchedule::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === Role::Superadmin->value;
    }
}
