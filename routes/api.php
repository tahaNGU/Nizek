<?php
use App\Http\Controllers\PriceController;
use Illuminate\Support\Facades\Route;

Route::post("/import/excel",[PriceController::class,"importExcel"]);
Route::post("/price-change-report",[PriceController::class,"report"]);
Route::post("/report",[PriceController::class,"customReport"]);
