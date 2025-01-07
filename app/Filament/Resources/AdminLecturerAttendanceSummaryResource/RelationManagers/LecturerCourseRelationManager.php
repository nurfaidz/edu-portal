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
                $oddDateStart = date('Y') . '-02-01';
                $oddDateEnd = date('Y') . '-08-31';
                $evenDateStart = date('Y') . '-09-01';
                $evenDateEnd = date('Y') . '-01-31';
                $now = date('Y-m-d');

                if ($now >= $oddDateStart && $now <= $oddDateEnd) {
                    return $query->where(function ($query) {
                        $query->whereRaw('MOD(semester, 2) <> 0');
                    });
                } elseif ($now >= $evenDateStart && $now <= $evenDateEnd) {
                    return $query->where(function ($query) {
                        $query->whereRaw('MOD(semester, 2) = 0');
                    });
                }

                return $query;
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
