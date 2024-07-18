<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

// unprotected route
Route::group(['prefix'=>'v1'], function(){
   
    route::resource('all-customers', App\Http\Controllers\Users\CustomerController::class)->only('index','show');
    route::post('login', App\Http\Controllers\Auth\AuthController::class);


    route::post('send-user-email', App\Http\Controllers\Email\SendUserEmailController::class);//reset, resend,invitation

    route::put('email-verification/{hash}', App\Http\Controllers\Email\EmailVerificationController::class);

    route::get('dashboard-stat', App\Http\Controllers\Product\DashboardStatController::class);

   // route::post('forgot-password', App\Http\Controllers\Email\ForgotPasswordController::class); //send reset link
    
    route::resource('users', App\Http\Controllers\Users\UserController::class)->only('store');
    route::resource('contact-forms', App\Http\Controllers\Users\ContactFormController::class);
    //testing route
   
});

// protected route
Route::middleware('auth:sanctum')->group(function() {

    Route::group(['prefix'=>'v1'], function(){
        route::post('log-out', App\Http\Controllers\Auth\LogOutController::class);
        route::resource('sale-users', App\Http\Controllers\Users\SaleUserController::class)->only('store','update');
        
        //resource
       // route::resource('currencies', App\Http\Controllers\Inventory\CurrencyController::class);
        route::resource('currencies', App\Http\Controllers\Inventory\CurrencyController::class);
        route::resource('measurements', App\Http\Controllers\Inventory\MeasurementController::class);
        route::resource('organizations', App\Http\Controllers\Inventory\OrganizationController::class);
        route::resource('sales', App\Http\Controllers\Inventory\SaleController::class);
        route::resource('stores', App\Http\Controllers\Inventory\StoreController::class);
        route::resource('prices', App\Http\Controllers\Inventory\PriceController::class);
        route::resource('price-notifications', App\Http\Controllers\Inventory\PriceNotificationController::class);
        route::resource('purchases', App\Http\Controllers\Inventory\PurchaseController::class);
        //route::resource('inventories', App\Http\Controllers\Inventory\InventoryController::class);
        route::resource('product-categories', App\Http\Controllers\Product\ProductCategoryController::class);
        route::resource('products', App\Http\Controllers\Product\ProductController::class);
        route::resource('product-types', App\Http\Controllers\Product\ProductTypeController::class);
        route::resource('product-sub-categories', App\Http\Controllers\Product\ProductSubCategoryController::class);
        route::resource('supplier-organizations', App\Http\Controllers\Supply\SupplierOrganizationController::class);
        route::resource('supplier-products', App\Http\Controllers\Supply\SupplierProductController::class);
        route::resource('product-supplied-to-companies', App\Http\Controllers\Supply\ProductSuppliedToCompanyController::class);
        route::resource('product-requests', App\Http\Controllers\Supply\ProductRequestsController::class);
        route::resource('customers', App\Http\Controllers\Users\CustomerController::class);
        route::resource('suppliers', App\Http\Controllers\Users\SupplierController::class);
        route::resource('auth-supplier-products', App\Http\Controllers\Supply\AuthSupplierProductController::class)->only('index');
        route::resource('business-branches', App\Http\Controllers\Security\BusinessBranchController::class);
        route::get('list-business-branches', [App\Http\Controllers\Security\BusinessBranchController::class, 'listing']);
        Route::resource('countries', App\Http\Controllers\Security\CountryController::class);
        Route::resource('states', App\Http\Controllers\Security\StateController::class);

        //get endpoint
        route::get('get-price-by-product-type/{id}', App\Http\Controllers\Inventory\PriceByProductTypeController::class);

        //need modification
        route::get('auto-generate-system-selling-price', function(){ return 100; });

        //need modification
        // route::get('last-batch-number', function(){

        //     $lastBatchNumber = \App\Models\Purchase::select('batch_no')->orderBy('created_at', 'desc')->first();
        //     if($lastBatchNumber){
        //         return response()->json(['data'=>$lastBatchNumber], 200);
        //     }
        //     return [];
            
        // });


        Route::get('last-batch-number', function(){
            $branchName = Auth::user()->branches->name;
            $branchId = Auth::user()->branch_id;
            $branchPrefix = strtoupper(substr($branchName, 0, 3));
            
            $lastBatchRecord = Purchase::select('batch_no')
                ->where('branch_id', $branchId)
                ->orderBy('created_at', 'desc')
                ->first();
        
            if ($lastBatchRecord) {
                // Extract the last part of the batch number and increment it
                $lastBatchNumber = $lastBatchRecord->batch_no;
                $lastNumber = (int) substr($lastBatchNumber, -2); // Assuming the last two digits are the number part
                $newBatchNumber = sprintf('%02d', $lastNumber + 1);
                $newBatchNumber = $branchPrefix . '/' . $newBatchNumber;
            } else {
                // If no record exists, start with 01
                $newBatchNumber = $branchPrefix . '/01';
            }
        
            return response()->json(['data' => $newBatchNumber], 200);
        });
















          //list organization
          route::get('all-organizations', function(){

            $organizations = \App\Models\Organization::select('id', 'organization_name')->orderBy('created_at', 'desc')->get();
            if($organizations){
                return response()->json(['data'=>$organizations], 200);
            }
            return [];
            
        });

          //list organization
          route::get('all-subscriptions', function(){

            $subscriptions = \App\Models\Subscription::select('id', 'plan_name')->orderBy('created_at', 'desc')->get();
            if($subscriptions){
                return response()->json(['data'=>$subscriptions], 200);
            }
            return [];
            
        });

        // route::get('download-sales-receipt/{id}', function(){
        //     return 'download in process';
        // });
        route::resource('download-sales-receipts', App\Http\Controllers\Inventory\SalesRecieptController::class)->only('show');

        route::get('all-price-by-product-type/{id}', App\Http\Controllers\Inventory\AllPriceByProductTypeController::class);
        route::get('latest-product-type-price/{id}', App\Http\Controllers\Inventory\LatestPriceByProductTypeController::class);
        route::get('latest-supplier-price/{product_type_id}/{supplier_id}', App\Http\Controllers\Inventory\LatestSupplierPriceController::class);

        //need modification
        route::get('get-suppliers-by-product-type-id/{product_type_id}', App\Http\Controllers\Inventory\SupplierByProductController ::class);
    
        route::get('all-products', App\Http\Controllers\Product\AllProductController::class);
        route::get('all-product-sub-categories-by-category-id/{id}', App\Http\Controllers\Product\AllProductSubCategoryController::class);
        route::get('product-type-by-id/{id}', App\Http\Controllers\Product\ProductTypeByIdController::class);
        route::get('all-product-type-name', App\Http\Controllers\Product\ProductTypeNameByIdController::class);//use in sales page
        route::get('all-product-type', App\Http\Controllers\Product\AllProductTypeController::class);
        route::get('all-supplier-products', App\Http\Controllers\Supply\AllSupplierProductController::class);
        route::get('user-detail', App\Http\Controllers\Users\AllUserDetailController::class);
        route::get('all-job-roles', App\Http\Controllers\Security\AllJobRoleController::class);
        route::get('all-suppliers', App\Http\Controllers\Users\AllSupplierController::class);
        route::resource('auth-supplier-products', App\Http\Controllers\Supply\AuthSupplierProductController::class);
       
       
        route::get('all-pages', App\Http\Controllers\Security\AllPageController::class);
        
        // route::get('dashboard-stat', App\Http\Controllers\Product\DashboardStatController::class);
        route::post('process-csv', App\Http\Controllers\Product\CsvController::class);
    
        route::resource('users', App\Http\Controllers\Users\UserController::class)->only('index','show','update','destroy');
        
        route::resource('customers', App\Http\Controllers\Users\CustomerController::class)->only('index','show');
        route::get('customer-names', App\Http\Controllers\Users\CustomerNamesController::class);
        
        route::resource('daily-sales', App\Http\Controllers\Inventory\DailySaleController::class)->only('index');

        // Search endpoints
        route::resource('search-currency', App\Http\Controllers\Inventory\SearchCurrencyController::class)->only('show');
        route::resource('search-measurement', App\Http\Controllers\Inventory\SearchMeasurementController::class)->only('show');
        route::resource('search-product-categories', App\Http\Controllers\Product\SearchProductCategoryController::class)->only('show');
        route::resource('search-product-sub-categories', App\Http\Controllers\Product\SearchProductSubCategoryController::class)->only('show');
        route::resource('search-product-types', App\Http\Controllers\Product\SearchProductTypeController::class)->only('show');
        route::resource('search-sales', App\Http\Controllers\Inventory\SearchSaleController::class)->only('show');
        route::resource('search-stores', App\Http\Controllers\Inventory\SearchStoreController::class)->only('show');
        route::resource('search-purchases', App\Http\Controllers\Inventory\SearchPurchaseController::class)->only('show');
        route::get('search-customer/{searchCriteria}', App\Http\Controllers\Users\SearchCustomerController::class);
        route::get('search-users/{searchCriteria}', App\Http\Controllers\Users\SearchUserController::class);
        route::resource('job-roles', App\Http\Controllers\Security\JobRoleController::class);
        route::resource('pages', App\Http\Controllers\Security\PagesController::class);
        route::resource('permissions', App\Http\Controllers\Security\PermissionController::class);
        Route::resource('subscriptions', App\Http\Controllers\Security\SubscriptionController::class);
        Route::resource('subscription-statuses', App\Http\Controllers\Security\SubscriptionStatusController::class);
        
       
    });
    
    //php artisan make:import PriceImport --model=Price



});







