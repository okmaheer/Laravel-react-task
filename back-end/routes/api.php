<?php

use App\Http\Controllers\LeadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// Group routes related to leads management under a common prefix
Route::prefix('leads')->group(function () {
    
    // Fetch leads with pagination, sorting, filtering, and search
    Route::get('/', [LeadController::class, 'index'])
        ->name('leads.index');

    // Create a new lead
    Route::post('/', [LeadController::class, 'store'])
        ->name('leads.store');

    // Fetch the details of a specific lead for editing
    Route::get('/edit/{id}', [LeadController::class, 'edit'])
        ->name('leads.edit');

    // Update an existing lead
    Route::put('/{id}', [LeadController::class, 'update'])
        ->name('leads.update');

    // Delete a specific lead
    Route::delete('/{id}', [LeadController::class, 'destroy'])
        ->name('leads.destroy');
});

// Fetch the list of lead statuses (used in dropdowns)
Route::get('/lead-statuses', [LeadController::class, 'getLeadStatus'])
    ->name('lead-statuses');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

