<?php

namespace App\Filament\Resources\LecturerCourseResource\RelationManagers;

use App\States\AttendanceStatus\Absent;
use App\States\AttendanceStatus\Excused;
use App\States\AttendanceStatus\Pending;
use App\States\AttendanceStatus\Present;
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
                    ->label('NIM'),
                Tables\Columns\TextColumn::make('student.studentProfile.name')
                    ->label('Nama Mahasiswa'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Kehadiran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Present::$name => 'success',
                        Absent::$name => 'danger',
                        Excused::$name => 'warning',
                        Pending::$name => 'gray'
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
