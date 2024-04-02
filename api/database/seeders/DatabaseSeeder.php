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

        // Inside DatabaseSeeder's run method

        // Create specific pages
        $pageNames = [
            'currencies', 'measurements', 'product-categories', 'product-sub-categories',
            'products', 'product-types', 'sales', 'purchases', 'stores', 'prices'
        ];

        foreach ($pageNames as $pageName) {
            \App\Models\Pages::factory()->create([
                'page_name' => $pageName,
            ]);
        }

        // Create specific roles
        $roleNames = ['Sales Manager', 'Cashier', 'Purchase Manager'];
        foreach ($roleNames as $roleName) {
            \App\Models\JobRole::factory()->create([
                'role_name' => $roleName,
            ]);
        }
       

            // Get all pages and roles
            $pages = \App\Models\Pages::all();
            $roles = \App\Models\JobRole::all();

            // Loop through each role and page to create permissions
            foreach ($roles as $role) {
                foreach ($pages as $page) {
                    \App\Models\Permission::factory()->create([
                        'page_id' => $page->id,
                        'role_id' => $role->id,
                        'read' => rand(0, 1),
                        'write' => rand(0, 1),
                        'update' => rand(0, 1),
                        'delete' => rand(0, 1),
                    ]);
                }
            }
            //admin role
            \App\Models\JobRole::factory()->create([
                'role_name' => 'Admin'
            ]);
            $adminRole= \App\Models\JobRole::where('role_name', 'Admin')->first();
            
            foreach ($pages as $page) {
                \App\Models\Permission::factory()->create([
                    'page_id' => $page->id,
                    'role_id' => $adminRole->id,
                    'read' => 1,
                    'write' => 1,
                    'update' =>1,
                    'delete' =>1,
                ]);
            }


    //      \App\Models\SupplierOrganization::factory(5)->create();
    //     \App\Models\Supplier::factory(30)->create();
    //     //  create measurement
    //     $measurements = [ ["name" => "litre", "unit" => "l"],["name" => "weight", "unit" => "kg"], ["name" => "height", "unit" => "h"]];
        
    //     foreach ($measurements as $measurement) {
    //         \App\Models\Measurement::factory()->create([
    //             'measurement_name' => $measurement['name'],
    //             'unit' => $measurement['unit'],
    //             'created_by'=>'admin'
    //         ]);
    //     }
    //     // //create currency
    //     $currencies = [["name" => "dollar", "symbol" => "$"],];
        
    //     foreach ($currencies as $currency) {
    //         \App\Models\Currency::factory()->create([
    //             'currency_name' => $currency['name'],
    //             'currency_symbol' => $currency['symbol'],
    //             'created_by'=>'admin'
    //         ]);
    //     }
        
    //  // create product
    //   \App\Models\ProductCategory::factory(5)->create();
    //   \App\Models\ProductSubCategory::factory(30)->create();
    //   \App\Models\Product::factory(2)->create();

    // //  custom supplier product
    //   $supplierId = \App\Models\User::where('email', 'supplier@gmail.com')->first()->id;
      
    //   \App\Models\SupplierProduct::factory(3)->create([
    //       'supplier_id' => $supplierId,
    //   ]);
      
      
    
   
    
    
    // \App\Models\ProductType::factory(3)->create();
    // \App\Models\Price::factory(3)->create();
    // \App\Models\Purchase::factory(3)->create();
    // \App\Models\Sale::factory()->count(10)->create();
    // \App\Models\Store::factory()->count(10)->create();
   
    //  \App\Models\SupplierProduct::factory(3)->create();
    // \App\Models\SupplyToCompany::factory(20)->create([
    //     'organization_id'  =>function () {
                
    //          return \App\Models\User::where('email','admin@gmail.com')->first()->organization_id;
    //      },
    //   ]);
    // \App\Models\SupplyToCompany::factory(20)->create();

    //   \App\Models\SupplierRequest::factory(3)->create([
    //     'organization_id'  =>function () {
                
    //          return \App\Models\User::where('email','admin@gmail.com')->first()->organization_id;
    //      },
    //      'supplier_product_id' => function () use ($supplierId) { //$supplierId is from 87
    //         return \App\Models\SupplierProduct::where('supplier_id', $supplierId)->inRandomOrder()->first()->id ?? null;
    //     },
    //   ]);
    //   \App\Models\SupplierRequest::factory(10)->create();
     
     

     
     
        
    }
}
