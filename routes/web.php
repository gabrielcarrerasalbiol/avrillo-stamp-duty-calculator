<?php

use App\Http\Controllers\StampDutyCalculatorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StampDutyCalculatorController::class, 'index'])->name('sdlt.index');
Route::get('/calculate', fn () => redirect()->route('sdlt.index'))->name('sdlt.calculate.show');
Route::post('/calculate', [StampDutyCalculatorController::class, 'calculate'])->name('sdlt.calculate');
