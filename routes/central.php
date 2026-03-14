<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('central.home');

Route::middleware('guest:enterprise')->group(function () {
	Route::get('/login', 'Central\\Auth\\EnterpriseLoginController@showLoginForm')->name('enterprise.login');
	Route::post('/login', 'Central\\Auth\\EnterpriseLoginController@login')->name('enterprise.login.submit');
});

Route::middleware(['auth:enterprise', 'enterprise'])->group(function () {
	Route::post('/logout', 'Central\\Auth\\EnterpriseLogoutController@logout')->name('enterprise.logout');

	Route::get('/dashboard', 'Central\\DashboardController@index')->name('enterprise.dashboard');

	Route::get('/companies', 'Central\\CompanyController@index')->name('companies.index');
	Route::get('/companies/create', 'Central\\CompanyController@create')->name('companies.create');
	Route::post('/companies', 'Central\\CompanyController@store')->name('companies.store');
	Route::get('/companies/{company}', 'Central\\CompanyController@show')->name('companies.show');
	Route::get('/companies/{company}/edit', 'Central\\CompanyController@edit')->name('companies.edit');
	Route::match(['put', 'patch'], '/companies/{company}', 'Central\\CompanyController@update')->name('companies.update');
	Route::delete('/companies/{company}', 'Central\\CompanyController@destroy')->name('companies.destroy');

	Route::post('/companies/{company}/provision', 'Central\\TenantProvisionController@store')->name('companies.provision');
	Route::get('/companies/{company}/provision-status', 'Central\\TenantProvisionController@show')->name('companies.provision-status');
});
