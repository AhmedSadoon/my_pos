<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(['prefix' => LaravelLocalization::setLocale(),'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]], function()
{

    Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function(){

        Route::get('/', [App\Http\Controllers\Dashboard\WelcomeController::class,'index'])->name('welcome');

       Route::resource('/users', App\Http\Controllers\Dashboard\UserController::class);


       Route::resource('/categories', App\Http\Controllers\Dashboard\CategoryController::class);

       Route::resource('/products', App\Http\Controllers\Dashboard\ProductController::class);
       Route::resource('/clients', App\Http\Controllers\Dashboard\ClientController::class)->except('show');


    });

});








