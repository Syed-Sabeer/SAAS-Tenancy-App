<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    'tenant.init',
])->group(function () {
    Route::middleware('guest:tenant')->group(function () {
        Route::get('/login', 'Tenant\\Auth\\LoginController@showLoginForm')->name('tenant.login');
        Route::post('/login', 'Tenant\\Auth\\LoginController@login')->name('tenant.login.submit');
    });

    Route::middleware('auth:tenant')->group(function () {
        Route::post('/logout', 'Tenant\\Auth\\LogoutController@logout')->name('tenant.logout');
        Route::get('/', 'Tenant\\DashboardController@index')->name('tenant.home');
        Route::get('/dashboard', 'Tenant\\DashboardController@index')->name('tenant.dashboard');

        Route::middleware('tenant.role:company_admin')->group(function () {
            Route::get('/users', 'Tenant\\UserController@index')->name('tenant.users.index');
            Route::get('/users/create', 'Tenant\\UserController@create')->name('tenant.users.create');
            Route::post('/users', 'Tenant\\UserController@store')->name('tenant.users.store');
            Route::get('/users/{user}/edit', 'Tenant\\UserController@edit')->name('tenant.users.edit');
            Route::match(['put', 'patch'], '/users/{user}', 'Tenant\\UserController@update')->name('tenant.users.update');
            Route::delete('/users/{user}', 'Tenant\\UserController@destroy')->name('tenant.users.destroy');
        });

        Route::get('/profile', 'Tenant\\ProfileController@show')->name('tenant.profile.show');
        Route::match(['put', 'patch'], '/profile', 'Tenant\\ProfileController@update')->name('tenant.profile.update');
    });
});
