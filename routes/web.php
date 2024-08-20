<?php

use App\Http\Controllers\AutoDiscountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CreateNewController;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\DiscountingController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\FinalQuotationController;
use App\Http\Controllers\generatePDFcontroller;
use App\Http\Controllers\GetDataFromModelController;
use App\Http\Controllers\PushToCRMController;
use App\Http\Controllers\RateCardController;
use App\Http\Controllers\RenderHTMLController;
use App\Http\Controllers\SavedQuotationController;
use App\Http\Controllers\SavePricesController;
use App\Http\Controllers\StorePricesFromAPI;
use App\Http\Controllers\SetSessionController;
use App\Http\Controllers\StoreInTableController;
use App\Http\Controllers\UsersController;

// Route::get("/save-prices", [SavePricesController::class, "index"]);
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
        Route::get('{any?}', [DefaultController::class, 'index']);
        Route::post('/', [EstimateController::class, "index"])->name("Estimate");
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
            Route::post('Update', [StoreInTableController::class, 'handleRequest'])->name('UpdateEstimate');
            Route::post('Save', [StoreInTableController::class, 'handleRequest'])->name('SaveEstimate');
        });
    });
});
