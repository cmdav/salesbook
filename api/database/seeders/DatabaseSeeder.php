<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        \App\Models\Organization::factory(1)->create();
        // \App\Models\User::factory()->create([
        //     'first_name' => 'Test',
        //     'email' => 'admin@gmail.com',
        //     'password'=>'test123',
        //     'organization_code'=>'123456',
    
        // ]);
        // \App\Models\User::factory(20)->create();
        //  \App\Models\SupplierOrganization::factory(5)->create();
        //  \App\Models\Supplier::factory(5)->create();
    }
}
