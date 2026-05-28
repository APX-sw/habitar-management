<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\ExtraChargeController;
use App\Http\Controllers\FixedChargeController;
use App\Http\Controllers\LeaseDocumentController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PropertyDocumentController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerReportController;
use App\Http\Controllers\ExpenseDocumentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmployeeSalarySettlementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AbsenceReasonController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\ObjectiveController;

// Auth routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/workspace', [WorkspaceController::class, 'index'])->name('workspace.index');
    Route::post('/objectives/status/{objective}', [ObjectiveController::class, 'updateStatus'])->name('objectives.update_status');
    Route::post('/objectives/notes/{objective}', [ObjectiveController::class, 'updateNotes'])->name('objectives.update_notes');
    Route::post('/objectives/{objective}/comments', [ObjectiveController::class, 'storeComment'])->name('objectives.comments.store');
    Route::post('/objectives/employee-create', [ObjectiveController::class, 'employeeStore'])->name('objectives.employee_store');

    // Gestión de Usuarios y Roles
    Route::resource('users', UserController::class)->middleware('can:users.read');
    Route::resource('roles', RoleController::class)->middleware('can:roles.read');

    // Gestión de Recursos Humanos: Legajos y Sueldos
    Route::middleware('can:rrhh_employees.read')->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::post('employees/{employee}/documents', [EmployeeController::class, 'storeDocument'])->name('employees.documents.store');
        Route::delete('employee-documents/{document}', [EmployeeController::class, 'destroyDocument'])->name('employees.documents.destroy');
        
        // Sueldos
        Route::get('salaries', [EmployeeSalarySettlementController::class, 'index'])->name('salaries.index');
        Route::get('salaries/create', [EmployeeSalarySettlementController::class, 'create'])->name('salaries.create');
        Route::post('salaries/store-period', [EmployeeSalarySettlementController::class, 'storePeriod'])->name('salaries.store_period');
        Route::get('salaries/period/{month}/{year}', [EmployeeSalarySettlementController::class, 'showPeriod'])->name('salaries.show_period');
        Route::post('salaries/advance', [EmployeeSalarySettlementController::class, 'storeAdvance'])->name('salaries.advance');
        Route::post('salaries/{employee}', [EmployeeSalarySettlementController::class, 'store'])->name('salaries.store');
        Route::post('salaries/pay/{settlement}', [EmployeeSalarySettlementController::class, 'pay'])->name('salaries.pay');
        Route::get('salaries/receipt/{payment}', [EmployeeSalarySettlementController::class, 'receipt'])->name('salaries.receipt');
    });

    // Motivos de ausencia (Configuración)
    Route::middleware('can:cfg_absence_reasons.read')->group(function () {
        Route::resource('absence-reasons', AbsenceReasonController::class)->except(['create', 'edit', 'show']);
    });

    // Control de Asistencia y Reportes (Admin)
    Route::middleware('can:rrhh_office.read')->group(function () {
        Route::get('attendances/office', [AttendanceController::class, 'office'])->name('attendances.office');
    });

    Route::middleware('can:rrhh_attendances.read')->group(function () {
        Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    });

    // Objetivos (Admin)
    Route::middleware('can:rrhh_objectives.read')->group(function () {
        Route::resource('objectives', ObjectiveController::class);
        Route::delete('objective-comments/{comment}', [ObjectiveController::class, 'destroyComment'])->name('objectives.comments.destroy');
    });

    // Autogestión de Asistencia para Empleados
    Route::post('attendances/check-in', [AttendanceController::class, 'checkIn'])->name('attendances.check_in');
    Route::post('attendances/check-out', [AttendanceController::class, 'checkOut'])->name('attendances.check_out');
    Route::post('attendances/absence', [AttendanceController::class, 'reportAbsence'])->name('attendances.absence');

    // Reportes
    Route::middleware('can:reports.read')->group(function () {
        Route::get('reports', [OwnerReportController::class, 'index'])->name('reports.index');
        Route::get('reports/create', [OwnerReportController::class, 'create'])->name('reports.create');
        Route::post('reports', [OwnerReportController::class, 'store'])->name('reports.store');
        Route::get('reports/{report}', [OwnerReportController::class, 'show'])->name('reports.show');
        Route::get('reports/{report}/owner/{owner_id}', [OwnerReportController::class, 'showIndividual'])->name('reports.show_individual');
        Route::get('public/reports/{report}/owner/{owner_id}', [OwnerReportController::class, 'showPublic'])->name('reports.show_public');
        Route::get('reports/{report}/batch', [OwnerReportController::class, 'showBatch'])->name('reports.show_batch');
        Route::post('reports/{report}/owner/{owner_id}/send-email', [OwnerReportController::class, 'sendEmail'])->name('reports.send_email');
        Route::delete('reports/{report}', [OwnerReportController::class, 'destroy'])->name('reports.destroy');
    });

    // Propietarios
    Route::resource('owners', OwnerController::class)->middleware('can:owners.read');

    // Propiedades
    Route::resource('properties', PropertyController::class)->middleware('can:properties.read');
    Route::middleware('can:properties.read')->group(function () {
        Route::post('property-documents', [PropertyDocumentController::class, 'store'])->name('property-documents.store');
        Route::delete('property-documents/{propertyDocument}', [PropertyDocumentController::class, 'destroy'])->name('property-documents.destroy');
        Route::get('properties/{property}/documents', [PropertyDocumentController::class, 'index'])->name('properties.documents');
    });

    // Inquilinos
    Route::resource('tenants', TenantController::class)->middleware('can:tenants.read');
    Route::get('tenants/{tenant}/pending-collections', [TenantController::class, 'pendingCollections'])->name('tenants.pending_collections')->middleware('can:tenants.read');

    // Contratos
    Route::middleware('can:leases.read')->group(function () {
        Route::resource('leases', LeaseController::class);
        Route::get('leases/{lease}/renew', [LeaseController::class, 'showRenewalForm'])->name('leases.renew');
        Route::post('leases/{lease}/renew', [LeaseController::class, 'storeRenewal'])->name('leases.renew.store');
        Route::get('leases/{lease}/renegotiate', [LeaseController::class, 'showRenegotiationForm'])->name('leases.renegotiate');
        Route::post('leases/{lease}/renegotiate', [LeaseController::class, 'storeRenegotiation'])->name('leases.renegotiate.store');
        Route::post('leases/{lease}/terminate', [LeaseController::class, 'terminate'])->name('leases.terminate');
        Route::resource('lease-documents', LeaseDocumentController::class);
        Route::get('leases/{lease}/documents', [LeaseDocumentController::class, 'index'])->name('leases.documents');
        Route::get('leases/{lease}/summary', [LeaseController::class, 'monthlySummary'])->name('leases.summary');
    });
    Route::resource('extra-charges', ExtraChargeController::class)->middleware('can:extra_charges.read');
    Route::resource('fixed-charges', FixedChargeController::class)->middleware('can:fixed_charges.read');

    // Cobros
    Route::middleware('can:collections.read')->group(function () {
        Route::get('collections', [CollectionController::class, 'index'])->name('collections.index');
        Route::get('collections/create', [CollectionController::class, 'create'])->name('collections.create');
        Route::post('collections', [CollectionController::class, 'store'])->name('collections.store');
        Route::get('collections/period/{month}/{year}', [CollectionController::class, 'showPeriod'])->name('collections.show_period');
        Route::get('collections/{collection}', [CollectionController::class, 'show'])->name('collections.show');
        Route::put('collections/{collection}', [CollectionController::class, 'update'])->name('collections.update');
        Route::post('collections/{collection}/pay', [CollectionController::class, 'pay'])->name('collections.pay');
        Route::post('collections/{collection}/send', [CollectionController::class, 'sendToTenant'])->name('collections.send');
        Route::get('collections/{collection}/receipt/{payment}', [CollectionController::class, 'paymentReceipt'])->name('collections.payment_receipt');
        Route::post('collections/{collection}/receipt/{payment}/send', [CollectionController::class, 'sendReceiptToTenant'])->name('collections.send_receipt');
        Route::post('collections/{collection}/receipt/{payment}/transfer', [CollectionController::class, 'transferPaymentToOwner'])->name('collections.transfer_payment');
        Route::post('collections/bulk-send', [CollectionController::class, 'bulkSend'])->name('collections.bulk_send');
    });

    // Caja
    Route::middleware('can:cash_register.read')->group(function () {
        Route::get('cash-register', [CashRegisterController::class, 'index'])->name('cash_register.index');
        Route::post('cash-register/transfer', [CashRegisterController::class, 'transfer'])->name('cash_register.transfer');
        Route::post('cash-register/adjust', [CashRegisterController::class, 'adjust'])->name('cash_register.adjust');
    });

    // Gastos
    Route::middleware('can:expenses.read')->group(function () {
        Route::resource('expenses', ExpenseController::class)->except(['show', 'edit']);
        Route::put('expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::get('expenses/{expense}/documents', [ExpenseDocumentController::class, 'index'])->name('expenses.documents');
        Route::post('expense-documents', [ExpenseDocumentController::class, 'store'])->name('expense-documents.store');
        Route::delete('expense-documents/{expenseDocument}', [ExpenseDocumentController::class, 'destroy'])->name('expense-documents.destroy');
    });

    // Rendiciones
    Route::middleware('can:settlements.read')->group(function () {
        Route::resource('settlements', SettlementController::class)->except(['edit', 'update', 'destroy']);
        Route::post('settlements/bulk', [SettlementController::class, 'bulkStore'])->name('settlements.bulk_store');
        Route::post('settlements/{settlement}/send', [SettlementController::class, 'sendToOwner'])->name('settlements.send_to_owner');
        Route::post('settlements/{settlement}/pay', [SettlementController::class, 'pay'])->name('settlements.pay');
        Route::post('settlements/{settlement}/carry-over', [SettlementController::class, 'carryOver'])->name('settlements.carry_over');
        Route::post('settlements/{settlement}/extra-fees', [SettlementController::class, 'addExtraFee'])->name('settlements.extra-fees.add');
        Route::delete('settlements/{settlement}/extra-fees/{extraFee}', [SettlementController::class, 'removeExtraFee'])->name('settlements.extra-fees.remove');
    });

    // Configuración (requiere al menos un permiso de configuración)
    Route::middleware('can:cfg_locations.read')->group(function () {
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('settings/locations', [SettingsController::class, 'locations'])->name('settings.locations');
        Route::post('settings/provinces', [SettingsController::class, 'storeProvince'])->name('settings.provinces.store');
        Route::delete('settings/provinces/{province}', [SettingsController::class, 'destroyProvince'])->name('settings.provinces.destroy');
        Route::post('settings/cities', [SettingsController::class, 'storeCity'])->name('settings.cities.store');
        Route::delete('settings/cities/{city}', [SettingsController::class, 'destroyCity'])->name('settings.cities.destroy');

        Route::get('settings/property-types', [SettingsController::class, 'propertyTypes'])->name('settings.property_types');
        Route::post('settings/property-types', [SettingsController::class, 'storePropertyType'])->name('settings.property-types.store');
        Route::delete('settings/property-types/{propertyType}', [SettingsController::class, 'destroyPropertyType'])->name('settings.property-types.destroy');

        Route::get('settings/indices', [SettingsController::class, 'indices'])->name('settings.indices');
        Route::post('settings/index-types', [SettingsController::class, 'storeIndexType'])->name('settings.index-types.store');
        Route::delete('settings/index-types/{indexType}', [SettingsController::class, 'destroyIndexType'])->name('settings.index-types.destroy');
        Route::post('settings/index-values', [SettingsController::class, 'storeIndexValue'])->name('settings.index-values.store');
        Route::delete('settings/index-values/{indexValue}', [SettingsController::class, 'destroyIndexValue'])->name('settings.index-values.destroy');

        Route::get('settings/accounts', [AccountController::class, 'index'])->name('settings.accounts');
        Route::post('settings/accounts', [AccountController::class, 'store'])->name('settings.accounts.store');
        Route::post('settings/accounts/{account}/toggle', [AccountController::class, 'toggleActive'])->name('settings.accounts.toggle');

        Route::get('settings/categories', [TransactionCategoryController::class, 'index'])->name('settings.categories');
        Route::post('settings/categories', [TransactionCategoryController::class, 'store'])->name('settings.categories.store');
        Route::delete('settings/categories/{category}', [TransactionCategoryController::class, 'destroy'])->name('settings.categories.destroy');

        Route::get('settings/agency-bank-accounts', [SettingsController::class, 'agencyBankAccounts'])->name('settings.agency_bank_accounts');
        Route::post('settings/agency-bank-accounts', [SettingsController::class, 'storeAgencyBankAccount'])->name('settings.agency_bank_accounts.store');
        Route::post('settings/agency-bank-accounts/{account}/default', [SettingsController::class, 'setDefaultAgencyBankAccount'])->name('settings.agency_bank_accounts.default');
        Route::delete('settings/agency-bank-accounts/{account}', [SettingsController::class, 'destroyAgencyBankAccount'])->name('settings.agency_bank_accounts.destroy');

        Route::get('settings/contact', [SettingsController::class, 'contact'])->name('settings.contact');
        Route::post('settings/contact', [SettingsController::class, 'storeContact'])->name('settings.contact.store');
    });
});

// API
Route::get('api/provinces/{province}/cities', [SettingsController::class, 'getCities']);
Route::get('api/index-types/{index}/values', [SettingsController::class, 'getIndexValues']);
Route::get('api/properties/{property}', [PropertyController::class, 'showApi']);
Route::get('api/tenants/{tenant}', [TenantController::class, 'showApi']);
