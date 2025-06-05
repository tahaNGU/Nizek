<?php

use App\Http\Controllers\StockStreamController;
use Illuminate\Support\Facades\Route;

Route::post("/import/excel", [StockStreamController::class, "importExcel"]);
Route::get("/report", [StockStreamController::class, "report"]);
Route::get("/custom/report", [StockStreamController::class, "customReport"]);
