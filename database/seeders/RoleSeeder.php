<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        collect(['waban', 'umum', 'koorlap', 'admin'])->each(function (string $role): void {
            Role::firstOrCreate(['name' => $role]);
        });
    }
}
