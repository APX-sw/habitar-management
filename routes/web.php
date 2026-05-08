<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\ExtraChargeController;
use App\Http\Controllers\FixedChargeController;
use App\Http\Controllers\LeaseDocumentController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PaymentMethodController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('owners', OwnerController::class);
Route::resource('properties', PropertyController::class);
Route::resource('tenants', TenantController::class);
Route::get('tenants/{tenant}/pending-collections', [TenantController::class, 'pendingCollections'])->name('tenants.pending_collections');
Route::resource('leases', LeaseController::class);
Route::get('leases/{lease}/renew', [LeaseController::class, 'showRenewalForm'])->name('leases.renew');
Route::post('leases/{lease}/renew', [LeaseController::class, 'storeRenewal'])->name('leases.renew.store');
Route::get('leases/{lease}/renegotiate', [LeaseController::class, 'showRenegotiationForm'])->name('leases.renegotiate');
Route::post('leases/{lease}/renegotiate', [LeaseController::class, 'storeRenegotiation'])->name('leases.renegotiate.store');
Route::post('leases/{lease}/terminate', [LeaseController::class, 'terminate'])->name('leases.terminate');
Route::resource('extra-charges', ExtraChargeController::class);
Route::resource('fixed-charges', FixedChargeController::class);
Route::resource('lease-documents', LeaseDocumentController::class);
Route::resource('payment-methods', PaymentMethodController::class);
Route::get('leases/{lease}/documents', [LeaseDocumentController::class, 'index'])->name('leases.documents');

// Cobros de Alquiler
Route::get('collections', [CollectionController::class, 'index'])->name('collections.index');
Route::get('collections/create', [CollectionController::class, 'create'])->name('collections.create');
Route::post('collections', [CollectionController::class, 'store'])->name('collections.store');
Route::get('collections/period/{month}/{year}', [CollectionController::class, 'showPeriod'])->name('collections.show_period');
Route::get('collections/{collection}', [CollectionController::class, 'show'])->name('collections.show');
Route::put('collections/{collection}', [CollectionController::class, 'update'])->name('collections.update');
Route::post('collections/{collection}/pay', [CollectionController::class, 'pay'])->name('collections.pay');
Route::post('collections/{collection}/send', [CollectionController::class, 'sendToTenant'])->name('collections.send');
Route::post('collections/bulk-send', [CollectionController::class, 'bulkSend'])->name('collections.bulk_send');


Route::get('leases/{lease}/summary', [LeaseController::class, 'monthlySummary'])->name('leases.summary');

// Settings
Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
Route::get('settings/locations', [SettingsController::class, 'locations'])->name('settings.locations');
Route::get('settings/property-types', [SettingsController::class, 'propertyTypes'])->name('settings.property_types');
Route::get('settings/indices', [SettingsController::class, 'indices'])->name('settings.indices');

Route::post('settings/provinces', [SettingsController::class, 'storeProvince'])->name('settings.provinces.store');
Route::delete('settings/provinces/{province}', [SettingsController::class, 'destroyProvince'])->name('settings.provinces.destroy');
Route::post('settings/cities', [SettingsController::class, 'storeCity'])->name('settings.cities.store');
Route::delete('settings/cities/{city}', [SettingsController::class, 'destroyCity'])->name('settings.cities.destroy');
Route::post('settings/property-types', [SettingsController::class, 'storePropertyType'])->name('settings.property-types.store');
Route::delete('settings/property-types/{propertyType}', [SettingsController::class, 'destroyPropertyType'])->name('settings.property-types.destroy');
Route::post('settings/index-types', [SettingsController::class, 'storeIndexType'])->name('settings.index-types.store');
Route::delete('settings/index-types/{indexType}', [SettingsController::class, 'destroyIndexType'])->name('settings.index-types.destroy');
Route::post('settings/index-values', [SettingsController::class, 'storeIndexValue'])->name('settings.index-values.store');
Route::delete('settings/index-values/{indexValue}', [SettingsController::class, 'destroyIndexValue'])->name('settings.index-values.destroy');

Route::post('settings/property-types', [SettingsController::class, 'storePropertyType'])->name('settings.property-types.store');
Route::delete('settings/property-types/{type}', [SettingsController::class, 'destroyPropertyType'])->name('settings.property-types.destroy');

Route::post('settings/index-types', [SettingsController::class, 'storeIndexType'])->name('settings.index-types.store');
Route::delete('settings/index-types/{index}', [SettingsController::class, 'destroyIndexType'])->name('settings.index-types.destroy');
Route::post('settings/index-values', [SettingsController::class, 'storeIndexValue'])->name('settings.index-values.store');
Route::delete('settings/index-values/{value}', [SettingsController::class, 'destroyIndexValue'])->name('settings.index-values.destroy');

// API
Route::get('api/provinces/{province}/cities', [SettingsController::class, 'getCities']);
Route::get('api/index-types/{index}/values', [SettingsController::class, 'getIndexValues']);
Route::get('api/properties/{property}', [PropertyController::class, 'showApi']);
Route::get('api/tenants/{tenant}', [TenantController::class, 'showApi']);
