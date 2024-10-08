<?php

namespace App\Filament\Resources\AttendanceLecturerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceStudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attendanceStudents';

    protected static ?string $title = 'Daftar Hadir Mahasiswa';

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
            ->columns([
                Tables\Columns\TextColumn::make('student.studentProfile.nim')
                    ->label('NIM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.studentProfile.name')
                    ->label('Nama Mahasiswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Kehadiran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        \App\States\AttendanceStatus\Present::$name => 'success',
                        \App\States\AttendanceStatus\Absent::$name => 'danger',
                        \App\States\AttendanceStatus\Excused::$name => 'warning',
                        \App\States\AttendanceStatus\Pending::$name => 'gray'
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
