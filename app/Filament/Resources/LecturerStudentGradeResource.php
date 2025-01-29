<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LecturerStudentGradeResource\Pages;
use App\Filament\Resources\LecturerStudentGradeResource\RelationManagers;
use App\Models\LecturerCourse;
use App\Models\StudentCourse;
use App\Models\StudentGrade;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LecturerStudentGradeResource extends Resource
{
    protected static ?string $model = LecturerCourse::class;

    protected static ?string $navigationLabel = 'Nilai Mahasiswa';

    protected static ?string $label = 'Nilai Mahasiswa';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

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
                Tables\Columns\TextColumn::make('course.code')
                    ->label('Kode Mata Kuliah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Mata Kuliah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.credits')
                    ->label('SKS'),
                Tables\Columns\TextColumn::make('course.semester')
                    ->label('Semester'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentCoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLecturerStudentGrades::route('/'),
            'edit' => Pages\EditLecturerStudentGrade::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $monthDay = date('m-d');

        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->where(function ($query) use ($monthDay) {
                if ($monthDay >= '02-01' && $monthDay <= '08-31') {
                    $query->whereRaw('MOD(semester, 2) = 0')->where('academic_year', now()->year);
                } else {
                    $query->whereRaw('MOD(semester, 2) <> 0')->where('academic_year', now()->year);
                }
            });

    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === \App\Enums\Roles\Role::Lecturer->value;
    }
}
