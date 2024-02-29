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
        \App\Models\Organization::factory(1)->create([
            'organization_code'=>'123457',
        ]);
        \App\Models\User::factory()->create([
            'first_name' => 'Test',
            'email' => 'admin@gmail.com',
            'password'=>'test123',
            'organization_code'=>'123456',
            'type_id' => 2,
    
        ]);
        \App\Models\User::factory()->create([
            'first_name' => 'Test',
            'email' => 'admin2@gmail.com',
            'password'=>'test123',
            'organization_code'=>'123457',
            'type_id' => 2,
    
        ]);

        \App\Models\User::factory()->create([
            'first_name' => 'Test',
            'email' => 'supplier@gmail.com',
            'password'=>'test123',
            'organization_code'=>'123457',
            'type_id' => 1,
    
        ]);
        \App\Models\User::factory(25)->create([
        
            'role_id' => 1,
    
        ]);
        \App\Models\User::factory(25)->create();
         \App\Models\SupplierOrganization::factory(5)->create();
        \App\Models\Supplier::factory(30)->create();
        // create measurement
        $measurements = [
            ["name" => "litre", "unit" => "l"],
            ["name" => "weight", "unit" => "kg"],
            ["name" => "height", "unit" => "h"]
        ];
        
        foreach ($measurements as $measurement) {
            \App\Models\Measurement::factory()->create([
                'measurement_name' => $measurement['name'],
                'unit' => $measurement['unit'],
                'created_by'=>'admin'
            ]);
        }
        //create currency
        $currencies = [
            ["name" => "dollar", "symbol" => "$"],
           
        ];
        
        foreach ($currencies as $currency) {
            \App\Models\Currency::factory()->create([
                'currency_name' => $currency['name'],
                'currency_symbol' => $currency['symbol'],
                'created_by'=>'admin'
            ]);
        }
        
      //create product
      \App\Models\ProductCategory::factory(5)->create();
      \App\Models\ProductSubCategory::factory(30)->create();
      \App\Models\Product::factory(30)->create();
      \App\Models\SupplierProduct::factory(3)->create();
      \App\Models\Store::factory()->count(10)->create();
      \App\Models\Sale::factory()->count(10)->create();
      \App\Models\Inventory::factory(3)->create();
      

     
     
        
    }
}
