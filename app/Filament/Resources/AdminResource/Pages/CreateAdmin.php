<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Enums\Roles\Role;
use App\Filament\Resources\AdminResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return \DB::transaction(function () use ($data) {
            $record = new ($this->getModel())($data);

            if ($tenant = Filament::getTenant()) {
                return $this->associateRecordWithTenant($record, $tenant);
            }

            $record->save();

            $role = \Spatie\Permission\Models\Role::where('name', Role::Admin->value)->first();
            $record->assignRole($role);

            return $record;
        });
    }
}
