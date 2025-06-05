<?php
use App\Http\Controllers\StockStreamController;
use Illuminate\Support\Facades\Route;

Route::post("/import/excel",[StockStreamController::class,"importExcel"]);
Route::post("/price-change-report",[StockStreamController::class,"report"]);
Route::post("/report",[StockStreamController::class,"customReport"]);
