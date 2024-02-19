<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// unprotected route
Route::group(['prefix'=>'v1'], function(){
    
    route::post('login', App\Http\Controllers\Auth\AuthController::class);

    route::resource('email-verification', App\Http\Controllers\Email\EmailVerificationController::class)->only('store','update');//post to resend email, put to update email

    route::post('forgot-password', App\Http\Controllers\Email\ForgotPasswordController::class); //send reset link
    
    route::resource('users', App\Http\Controllers\Users\UserController::class)->only('store');

   

});

// protected route
Route::middleware('auth:sanctum')->group(function() {

    Route::group(['prefix'=>'v1'], function(){

        route::post('log-out', App\Http\Controllers\Auth\LogOutController::class);

       
    });

    route::resource('currencys', App\Http\Controllers\Inventory\CurrencyController::class);
    route::resource('measurements', App\Http\Controllers\Inventory\MeasurementController::class);
    route::resource('organizations', App\Http\Controllers\Inventory\OrganizationController::class);
    route::resource('sales', App\Http\Controllers\Inventory\SaleController::class);
    route::resource('stores', App\Http\Controllers\Inventory\StoreController::class);
    
    route::resource('product-categories', App\Http\Controllers\Product\ProductCategoryController::class);
    route::resource('products', App\Http\Controllers\Product\ProductController::class);
    route::resource('products-sub-categories', App\Http\Controllers\Product\ProductSubCategoryController::class);
    
    route::resource('supplier-organizations', App\Http\Controllers\Supply\SupplierOrganizationController::class);
    route::resource('supplier-products', App\Http\Controllers\Supply\SupplierProductController::class);
   

    route::resource('customers', App\Http\Controllers\Users\CustomerController::class);
    route::resource('suppliers', App\Http\Controllers\Users\SupplierController::class);
    




});