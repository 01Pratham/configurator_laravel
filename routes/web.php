<?php

use App\Http\Controllers\Ajax\EstimateActionsController;
use App\Http\Controllers\Ajax\RenderHTMLController;
use App\Http\Controllers\Ajax\SetSessionController;
use App\Http\Controllers\Pages\DashboardController;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\GetDataFromModelController;
use App\Http\Controllers\StorePricesFromAPI;
use App\Http\Controllers\Discounting\AutoDiscountController;
use App\Http\Controllers\Discounting\DiscountingController;
use App\Http\Controllers\Estimate\CreateNewController;
use App\Http\Controllers\Estimate\EstimateController;
use App\Http\Controllers\FinalQuotation\FinalQuotationController;
use App\Http\Controllers\FinalQuotation\generatePDFcontroller;
use App\Http\Controllers\FinalQuotation\PushToCRMController;
use App\Http\Controllers\Pages\RateCardController;
use App\Http\Controllers\Pages\SavedQuotationController;
use App\Http\Controllers\Pages\UsersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\StoreActivityLogs;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\admin\TablesActionController;
use App\Http\Controllers\admin\TablesDataController;
use Illuminate\Http\Request;

Route::prefix("Admin")->group(function () {
    Route::get("/Dashboard", [AdminDashboard::class, "index"])->name("AdminDashboard");
    Route::get("/Table/{table_name}", [TablesDataController::class, "index"])->name("TableData");
    Route::post("/Table/{table_name}/Create", [TablesActionController::class, "Create"])->name("AdminTableActionCreate");
    // Route::post("/Action/{table_name}/{id}/{action}", [TablesActionController::class, "call_func_by_action"])->name("AdminTableAction");
    Route::get("/Action/{table_name}/{id}/Render", [TablesActionController::class, "Render"])->name("AdminTableRenderAction");
    Route::post("/Action/{table_name}/{id}/Update", [TablesActionController::class, "Update"])->name("AdminTableUpdateAction");
    Route::post("/Action/{table_name}/{id}/Delete", [TablesActionController::class, "Delete"])->name("AdminTableDeleteAction");
});

Route::middleware([StoreActivityLogs::class])->group(function () {

    Route::get("/save-prices", [StorePricesFromAPI::class, "index"]);

    Route::get('/', [DefaultController::class, 'index']);
    Route::get('/home', [DefaultController::class, 'index']);

    Route::get('/Session/{_key}/{_value}', [SetSessionController::class, 'index'])->name("SetSession");

    Auth::routes();

    Route::middleware(['web', "check.session"])->group(function () {
        Route::get('/Dashboard', [DashboardController::class, 'index'])->name('Dashboard');
        Route::get('/SavedEstimates/{_id?}', [SavedQuotationController::class, 'index'])->name('SavedEstimates');
        Route::get('/CreateNew/{_id?}', [CreateNewController::class, 'index'])->name('CreateNew');
        Route::get('/Users', [UsersController::class, 'index'])->name('Users');

        Route::prefix("RateCard")->group(function () {
            Route::get('/', [RateCardController::class, 'RateCards'])->name('AllRateCards');
            Route::get('Id/{_id}', [RateCardController::class, 'RateCard'])->name('RateCard');
        });
        /* Estimate Routes */
        Route::prefix("Estimate")->group(function () {
            Route::get("Delete/{_id}", [EstimateActionsController::class, "Delete"])->name("DeleteEstimate");
            Route::get("Clone/{_id}", [EstimateActionsController::class, "Clone"])->name("CloneEstimate");
            Route::get("Share/{_id}", [EstimateActionsController::class, "Share"])->name("ShareEstimate");
            Route::get("Share/{user_id}/{_id}", [EstimateActionsController::class, "ShareToUser"])->name("ShareEstimateToUser");
            Route::get('{any?}', [DefaultController::class, 'index']);
            Route::post('/', [EstimateController::class, "index"])->name("Estimate");
            Route::post("/serialize-data", function (Request $request) {
                return response()->json($request->all());
            })->name("serialize-data");
            Route::post("FinalQuotation", [FinalQuotationController::class, "index"])->name("FinalQuotation");
            Route::post("Discounting", [DiscountingController::class, "index"])->name("Discounting");
            Route::post("AutoDiscount", [AutoDiscountController::class, "index"])->name("AutomaticDiscount");
        })->name("EstimateRoutes");

        /* AJAX calls */
        Route::prefix("Ajax")->group(function () {
            Route::get('{any?}', [DefaultController::class, 'index']);
            Route::post("AssociativeProductAjax", [GetDataFromModelController::class, "AssociativeProduct"])->name("AssociativeProductAjax");
            Route::post("GetProductUnit", [GetDataFromModelController::class, "getUnit"])->name("GetProductUnit");
            Route::post("ProductAjax", [RenderHTMLController::class, "ProductAjax"])->name("ProductAjax");
            Route::post("VirtualMachine", [RenderHTMLController::class, "VirtualMachine"])->name("VirtualMachineAjax");
            Route::post("BlockStorage", [RenderHTMLController::class, "BlockStorage"])->name("BlockStorageAjax");
            Route::post("Estimate", [RenderHTMLController::class, "Estimate"])->name("EstimateAjax");
            Route::post("GeneratePDF", [generatePDFcontroller::class, "GeneratePDF"])->name("GeneratePDFAjax");
            Route::post("DeletePDF", [generatePDFcontroller::class, "deletePDF"])->name("DeletePDFAjax");
            Route::post("PushToCRM", [PushToCRMController::class, "index"])->name("Push");
        });

        Route::prefix("Save")->group(function () {
            Route::prefix("Estimate")->group(function () {
                Route::post("Update", [EstimateActionsController::class, "UpdateEstimate"])->name("UpdateEstimate");
                Route::post("Insert", [EstimateActionsController::class, "CreateEstimate"])->name("InsertEstimate");
            });
        });
    });
});
