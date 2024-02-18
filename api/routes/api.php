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

        route::resource('tasks', App\Http\Controllers\Task\TaskController::class)->only('index','show','store','update','destroy');
    });
});