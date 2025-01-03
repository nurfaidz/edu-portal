<?php

namespace App\Filament\Resources;

use App\Enums\Courses\Type;
use App\Enums\Roles\Role;
use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationLabel = 'Mata Kuliah';

    protected static ?string $label = 'Mata Kuliah';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Data Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Mata Kuliah')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Mata Kuliah')
                            ->required(),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode')
                            ->maxLength(6)
                            ->minLength(6)
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('semester')
                            ->label('Semester')
                            ->minValue(1)
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('credits')
                            ->label('SKS')
                            ->minValue(1)
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                Type::Mandatory->value => 'Wajib',
                                Type::Elective->value => 'Pilihan',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Mata Kuliah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('credits')
                    ->label('SKS'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(function ($record) {
                        return match ($record->type) {
                            Type::Mandatory->value => 'Wajib',
                            Type::Elective->value => 'Pilihan',
                        };
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'view' => Pages\ViewCourse::route('/{record}'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === Role::Superadmin->value;
    }
}
