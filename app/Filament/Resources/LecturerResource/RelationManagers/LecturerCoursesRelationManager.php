<?php

namespace App\Filament\Resources\LecturerResource\RelationManagers;

use App\Enums\Courses\Type;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LecturerCoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'lecturerCourses';

    protected static ?string $title = 'Mata Kuliah Dosen';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('course_id')
                            ->label('Mata Kuliah')
                            ->options(
                                \App\Models\Course::query()
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Mata Kuliah'),
                Tables\Columns\TextColumn::make('course.code')
                    ->label('Kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.credits')
                    ->label('SKS'),
                Tables\Columns\TextColumn::make('course.semester')
                    ->label('Semester'),
                Tables\Columns\TextColumn::make('course.type')
                    ->label('Tipe')
                    ->formatStateUsing(function ($record) {
                        return match ($record->course->type) {
                            Type::Mandatory->value => 'Wajib',
                            Type::Elective->value => 'Pilihan',
                        };
                    }), 
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Mata Kuliah'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Menghapus Mata Kuliah Dosen akan otomatis menghapus semua jadwal yang terkait')
                    ->action(function ($record) {
                        try {
                            if ($record->schedules()->exists()) {
                                $record->schedules()->delete();
                            }

                            $record->delete();

                            Notification::make()
                                ->title('Berhasil menghapus')
                                ->body('Mata kuliah dosen dan semua jadwal yang terkait berhasil dihapus')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal menghapus')
                                ->body('Terjadi kesalahan saat menghapus mata kuliah dosen')
                                ->danger()
                                ->send();
                        }
                    })
            ])
            ->bulkActions([
                //
            ]);
    }
}
