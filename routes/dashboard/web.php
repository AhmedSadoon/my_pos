<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => LaravelLocalization::setLocale(),'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]], function()
{

    Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function(){

        Route::get('/', [App\Http\Controllers\Dashboard\WelcomeController::class,'index'])->name('welcome');

       Route::resource('/users', App\Http\Controllers\Dashboard\UserController::class);


    });

});








