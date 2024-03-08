<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// unprotected route
Route::group(['prefix'=>'v1'], function(){
    
    route::post('login', App\Http\Controllers\Auth\AuthController::class);


    route::post('send-user-email', App\Http\Controllers\Email\SendUserEmailController::class);//reset, resend,invitation

    route::put('email-verification/{hash}', App\Http\Controllers\Email\EmailVerificationController::class);

   // route::post('forgot-password', App\Http\Controllers\Email\ForgotPasswordController::class); //send reset link
    
    route::resource('users', App\Http\Controllers\Users\UserController::class)->only('store');

   

});

// protected route
Route::middleware('auth:sanctum')->group(function() {

    Route::group(['prefix'=>'v1'], function(){
        route::post('log-out', App\Http\Controllers\Auth\LogOutController::class);
        
        //inventory
        route::resource('currencies', App\Http\Controllers\Inventory\CurrencyController::class);
        route::resource('measurements', App\Http\Controllers\Inventory\MeasurementController::class);
        route::resource('organizations', App\Http\Controllers\Inventory\OrganizationController::class);
        route::resource('sales', App\Http\Controllers\Inventory\SaleController::class);
        route::resource('stores', App\Http\Controllers\Inventory\StoreController::class);
        route::resource('inventories', App\Http\Controllers\Inventory\InventoryController::class);
        //products
        route::resource('product-categories', App\Http\Controllers\Product\ProductCategoryController::class);
        route::get('all-products', App\Http\Controllers\Product\AllProductController::class);
        route::resource('products', App\Http\Controllers\Product\ProductController::class);

        route::get('all-product-sub-categories-by-category-id/{id}', App\Http\Controllers\Product\AllProductSubCategoryController::class);
        route::resource('product-sub-categories', App\Http\Controllers\Product\ProductSubCategoryController::class);
        // supplier
        route::resource('supplier-organizations', App\Http\Controllers\Supply\SupplierOrganizationController::class);
        route::get('all-supplier-products', App\Http\Controllers\Supply\AllSupplierProductController::class);
        route::resource('supplier-products', App\Http\Controllers\Supply\SupplierProductController::class);
        route::resource('product-supplied-to-companies', App\Http\Controllers\Supply\ProductSuppliedToCompanyController::class);
        route::resource('product-requests', App\Http\Controllers\Supply\ProductRequestsController::class);
    
        //app users
        route::resource('customers', App\Http\Controllers\Users\CustomerController::class);
        route::resource('suppliers', App\Http\Controllers\Users\SupplierController::class);
        route::resource('users', App\Http\Controllers\Users\UserController::class)->only('index','show');

        Route::get('search-users/{searchCriteria}', App\Http\Controllers\Users\SearchUserController::class);

    });
    




});