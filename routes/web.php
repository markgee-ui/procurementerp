<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuantitySurveyorController;
use App\Http\Controllers\ProjectManagerController;
use App\Http\Controllers\OfficeProjectManagerController;
use App\Http\Controllers\ReportController;
/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Auth)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::prefix('procurement')->name('procurement.')->middleware('role:procurement')->group(function () {

        // GET /procurement/create
        Route::get('/create', [ProcurementController::class, 'create'])
            ->name('create');

        // POST /procurement/store
        Route::post('/store', [ProcurementController::class, 'store'])
            ->name('store');
        Route::get('/suppliers', [ProcurementController::class, 'supplierIndex'])->name('supplier.index');
        Route::get('/suppliers/{supplier}/edit', [ProcurementController::class, 'editSupplier'])->name('supplier.edit');
        Route::get('/suppliers/{supplier}', [ProcurementController::class, 'show'])->name('supplier.show');
        Route::put('/suppliers/{supplier}', [ProcurementController::class, 'updateSupplier'])->name('supplier.update');
        Route::delete('/suppliers/{supplier}', [ProcurementController::class, 'destroySupplier'])->name('supplier.destroy');
        Route::get('/products', [ProcurementController::class, 'productIndex'])->name('product.index');
        Route::get('/products/{product}/edit', [ProcurementController::class, 'edit'])->name('product.edit');
        Route::put('/products/{product}', [ProcurementController::class, 'update'])->name('product.update');
        Route::delete('/products/{product}', [ProcurementController::class, 'destroy'])->name('product.destroy');
        Route::get('/order/create/{supplier}', [ProcurementController::class, 'createPurchaseOrder'])->name('order.create');
        Route::get('/orders/create', [ProcurementController::class, 'createSelectSupplier'])->name('order.create_select_supplier');
        Route::post('/order/store', [ProcurementController::class, 'storePurchaseOrder'])->name('order.store');
       Route::get('/order/show/{purchaseOrder}', [ProcurementController::class, 'showPurchaseOrder'])->name('order.show');
       Route::get('/order/print/{purchaseOrder}', [ProcurementController::class, 'printPurchaseOrder'])->name('order.print');
       Route::get('/orders', [ProcurementController::class, 'indexPurchaseOrder'])->name('order.index');
       Route::get('/orders/{purchaseOrder}/edit', [ProcurementController::class, 'editPurchaseOrder'])->name('order.edit');
        Route::put('/orders/{purchaseOrder}', [ProcurementController::class, 'updatePurchaseOrder'])->name('order.update');
        Route::delete('/orders/{purchaseOrder}', [ProcurementController::class, 'destroyPurchaseOrder'])->name('order.destroy');
        Route::get('/order/download/{purchaseOrder}', [ProcurementController::class, 'downloadPurchaseOrder'])->name('order.download');
        Route::get('/requisitions', [ProcurementController::class, 'requisitionsIndex'])->name('requisition.index');
    Route::get('/requisitions/{requisition}', [ProcurementController::class, 'requisitionAction'])->name('requisition.action');
    Route::post('/requisitions/{requisition}/initiate-po', [ProcurementController::class, 'initiatePurchaseOrder'])->name('requisition.initiate_po');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
     Route::get('/service-orders', [ProcurementController::class, 'indexServiceOrders'])->name('service-order.index');
    Route::get('/service-orders/{serviceOrder}', [ProcurementController::class, 'showServiceOrder'])->name('service-order.show');
    Route::get('/service-orders/{serviceOrder}/edit', [ProcurementController::class, 'editServiceOrder'])->name('service-order.edit');
    Route::put('/service-orders/{serviceOrder}', [ProcurementController::class, 'updateServiceOrder'])->name('service-order.update');
    Route::delete('/service-orders/{serviceOrder}', [ProcurementController::class, 'destroyServiceOrder'])->name('service-order.destroy');
    
    // Your existing create/store routes
    Route::get('/suppliers/{supplier}/service-order/create', [ProcurementController::class, 'createServiceOrder'])->name('service-order.create');
    Route::post('/service-order/store', [ProcurementController::class, 'storeServiceOrder'])->name('service-order.store');
    
    });

     // In routes/web.php

 Route::prefix('qs')->name('qs.')->middleware('role:qs')->group(function () {
    
    // QS Dashboard (The original /qs route)
    Route::get('/', [QuantitySurveyorController::class, 'index'])->name('index'); 

    // --- BoQ Routes ---
    
    // Show the list of all created BoQs (New Index Page)
    Route::get('/boq', [QuantitySurveyorController::class, 'indexBoq'])->name('boq.index'); 

    // Show the BoQ creation form
    Route::get('/boq/create', [QuantitySurveyorController::class, 'createBoq'])->name('boq.create'); 

    // Store a new BoQ
    Route::post('/boq', [QuantitySurveyorController::class, 'storeBoq'])->name('boq.store');

    // Show single BoQ (View)
    Route::get('/boq/{boq}', [QuantitySurveyorController::class, 'showBoq'])->name('boq.show'); // Requires showBoq method

    // Show BoQ edit form
    Route::get('/boq/{boq}/edit', [QuantitySurveyorController::class, 'editBoq'])->name('boq.edit'); // Requires editBoq method
    Route::put('/boq/{boq}', [QuantitySurveyorController::class, 'updateBoq'])->name('boq.update');

    // Delete BoQ
    Route::delete('/boq/{boq}', [QuantitySurveyorController::class, 'destroyBoq'])->name('boq.destroy'); 
    Route::get('/boq/{boq}/download', [QuantitySurveyorController::class, 'downloadBoq'])->name('boq.download');
    Route::get('requisitions', [QuantitySurveyorController::class, 'indexRequisitions'])->name('requisitions.index');
    
    // 2. Show a specific PR (to review details before approving/rejecting)
    Route::get('requisitions/{requisition}', [QuantitySurveyorController::class, 'showRequisition'])->name('requisitions.show');
    
    // 3. ACTION: Approve the PR (Moves to Stage 2: Office PM)
    Route::post('requisitions/{requisition}/approve', [QuantitySurveyorController::class, 'approveRequisition'])->name('requisitions.approve');
    
    // 4. ACTION: Reject the PR (Requires rejection notes)
    Route::post('requisitions/{requisition}/reject', [QuantitySurveyorController::class, 'rejectRequisition'])->name('requisitions.reject');
 });
    Route::prefix('pm')->name('pm.')->middleware('role:pm')->group(function () {
        Route::get('/', [ProjectManagerController::class, 'index'])->name('index');
        Route::get('/requisitions/create/{project}', [ProjectManagerController::class, 'createRequisition'])->name('requisitions.create');
        Route::post('/requisitions/store', [ProjectManagerController::class, 'storeRequisition'])->name('requisitions.store');
        Route::get('requisitions', [ProjectManagerController::class, 'indexRequisitions'])->name('requisitions.index');
        Route::get('requisitions/{requisition}', [ProjectManagerController::class, 'showRequisition'])->name('requisitions.show');
        Route::get('requisitions/{requisition}/edit', [ProjectManagerController::class, 'editRequisition'])->name('requisitions.edit');
        Route::patch('requisitions/{requisition}', [ProjectManagerController::class, 'updateRequisition'])->name('requisitions.update');
        Route::delete('requisitions/{requisition}', [ProjectManagerController::class, 'destroyRequisition'])->name('requisitions.destroy');
        Route::get('requisitions/{requisition}/pdf', [ProjectManagerController::class, 'downloadRequisitionPdf'])->name('requisitions.pdf');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        
    });
    Route::prefix('opm')->name('opm.')->middleware('role:offpm')->group(function () {
        
        // OPM Dashboard
        Route::get('/', [OfficeProjectManagerController::class, 'index'])->name('index'); 

        // --- PR APPROVAL Routes (Stage 2: OPM) ---
        // 1. Index of PRs awaiting OPM approval (Stage 2)
        Route::get('requisitions', [OfficeProjectManagerController::class, 'indexRequisitions'])->name('requisitions.index');
        
        // 2. Show a specific PR (to review details before approving/rejecting)
        Route::get('requisitions/{requisition}', [OfficeProjectManagerController::class, 'showRequisition'])->name('requisitions.show');
        
        // 3. ACTION: Approve the PR (Moves to Stage 3: Procurement/Final)
        Route::post('requisitions/{requisition}/approve', [OfficeProjectManagerController::class, 'approveRequisition'])->name('requisitions.approve');
        
        // 4. ACTION: Reject the PR (Requires rejection notes)
        Route::post('requisitions/{requisition}/reject', [OfficeProjectManagerController::class, 'rejectRequisition'])->name('requisitions.reject');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
    Route::get('/reports/export/{type}', [ReportController::class, 'exportCSV'])
        ->name('reports.export')
        ->where('type', 'requisitions|orders');
});

/*
|--------------------------------------------------------------------------
| Default Redirect
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});
