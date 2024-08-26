<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceSummaryResource\Pages;
use App\Filament\Resources\AttendanceSummaryResource\RelationManagers;
use App\Models\Attendance;
use App\Models\Lecturer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceSummaryResource extends Resource
{
    protected static ?string $model = Lecturer::class;

    protected static ?string $label = 'Daftar Absensi Dosen';

    protected static ?string $navigationLabel = 'Absensi Dosen';

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
                    ->label('NPP'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Dosen'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceSummaries::route('/'),
            'create' => Pages\CreateAttendanceSummary::route('/create'),
            'view' => Pages\ViewAttendanceSummary::route('/{record}'),
            'edit' => Pages\EditAttendanceSummary::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === \App\Enums\Roles\Role::Admin->value;
    }
}
