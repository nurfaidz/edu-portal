<?php

namespace App\Filament\Resources;

use App\Enums\Roles\Role;
use App\Filament\Resources\AdminLecturerAttendanceSummaryResource\Pages;
use App\Filament\Resources\AdminLecturerAttendanceSummaryResource\RelationManagers;
use App\Models\Lecturer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminLecturerAttendanceSummaryResource extends Resource
{
    protected static ?string $model = Lecturer::class;

    protected static ?string $navigationLabel = 'Absensi Dosen';

    protected static ?string $label = 'Absensi Dosen';

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
                Tables\Columns\TextColumn::make('npp')
                    ->label('NIP'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LecturerCourseRelationManager::make(),
            RelationManagers\AttendancesRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminLecturerAttendanceSummaries::route('/'),
            'view' => Pages\ViewAdminLecturerAttendanceSummary::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === Role::Admin->value;
    }
}
