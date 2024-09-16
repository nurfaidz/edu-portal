<?php

namespace App\Filament\Resources\AdminLecturerAttendanceSummaryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LecturerCourseRelationManager extends RelationManager
{
    protected static string $relationship = 'lecturerCourses';

    protected static ?string $title = 'Mata Kuliah';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->where('academic_year', now()->year);
            })
            ->columns([
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Mata Kuliah'),
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester'),
                Tables\Columns\TextColumn::make('academic_year')
                    ->label('Tahun Akademik'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
