<?php

use App\Http\Controllers\CommitteeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Role-Based Routes
|--------------------------------------------------------------------------
|
| Here is where you can register role-based routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Superadmin Routes
Route::prefix('admin')->middleware(['auth', 'role:superadmin'])->group(function () {
    // Member Management routes are defined in web.php

    // Committee Management
    Route::get('/committees', [CommitteeController::class, 'adminIndex'])->name('admin.committees.index');
    Route::get('/committees/create', [CommitteeController::class, 'create'])->name('admin.committees.create');
    Route::post('/committees', [CommitteeController::class, 'store'])->name('admin.committees.store');
    Route::get('/committees/{committee}/edit', [CommitteeController::class, 'edit'])->name('admin.committees.edit');
    Route::put('/committees/{committee}', [CommitteeController::class, 'update'])->name('admin.committees.update');
    Route::delete('/committees/{committee}', [CommitteeController::class, 'destroy'])->name('admin.committees.destroy');
    Route::post('/committees/{committee}/members', [CommitteeController::class, 'addMember'])->name('admin.committees.members.add');
    Route::delete('/committees/{committee}/members/{user}', [CommitteeController::class, 'removeMember'])->name('admin.committees.members.remove');
});

// Event and Announcement Management routes are now defined in web.php

// Payment Management routes are defined in web.php

// Member Routes for Committees
Route::prefix('omcms')->middleware(['auth'])->group(function () {
    Route::get('/committees', [CommitteeController::class, 'index'])->name('committees.index');
    Route::get('/committees/{committee}', [CommitteeController::class, 'show'])->name('committees.show');
});
