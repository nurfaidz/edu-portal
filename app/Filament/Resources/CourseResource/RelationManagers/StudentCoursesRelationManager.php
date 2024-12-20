<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentCoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'studentCourses';

    protected static ?string $title = 'Mahasiswa yang Terdaftar akan ditampilkan untuk tahun akademik saat ini';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.studentProfile.name')
                    ->label('Nama Mahasiswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.studentProfile.nim')
                    ->label('NIM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.studentProfile.class')
                    ->label('Kelas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->searchable(),
                Tables\Columns\TextColumn::make('academic_year')
                    ->label('Tahun Akademik')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('filterStudent')
                    ->form([
                        Forms\Components\TextInput::make('semester')
                            ->label('Semester')
                            ->minValue(1)
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('academic_year')
                            ->label('Tahun Akademik')
                            ->default(date('Y'))
                            ->minLength(4)
                            ->maxLength(4)
                            ->numeric()
                            ->required(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(isset($data['semester']), function (Builder $query) use ($data) {
                                return $query->where('semester', $data['semester']);
                            })
                            ->when(isset($data['academic_year']), function (Builder $query) use ($data) {
                                return $query->where('academic_year', $data['academic_year']);
                            });
                    }),
            ])
            ->deferLoading()
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Hapus Mahasiswa dari Mata Kuliah ini'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
