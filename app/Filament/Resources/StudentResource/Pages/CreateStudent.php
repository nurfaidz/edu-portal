<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Enums\Roles\Role;
use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return \DB::transaction(function () use ($data) {
            $record = new ($this->getModel())($data);

            if ($tenant = Filament::getTenant()) {
                return $this->associateRecordWithTenant($record, $tenant);
            }

            $record->save();

            $role = \Spatie\Permission\Models\Role::where('name', Role::Student->value)->first();
            $record->assignRole($role);

            return $record;
        });
    }
}
