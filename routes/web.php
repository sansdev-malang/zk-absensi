<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\WorkCodeController;
use App\Http\Controllers\ZktecoController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('attendances', AttendanceController::class);
    Route::resource('devices', DeviceController::class);
    Route::resource('work-codes', WorkCodeController::class);
    Route::get('shifts', [\App\Http\Controllers\ShiftController::class, 'index'])->name('shifts.index');
    Route::resource('schedules', \App\Http\Controllers\ScheduleController::class)->except(['show', 'edit', 'update']);
    
    // Bonus Schemes
    Route::resource('bonus-schemes', \App\Http\Controllers\BonusSchemeController::class);
    Route::post('bonus-schemes/{bonusScheme}/rules', [\App\Http\Controllers\BonusSchemeController::class, 'storeRule'])->name('bonus-schemes.rules.store');
    Route::delete('bonus-rules/{bonusRule}', [\App\Http\Controllers\BonusSchemeController::class, 'destroyRule'])->name('bonus-rules.destroy');
    
    // Reports
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/recalculate', [\App\Http\Controllers\ReportController::class, 'recalculate'])->name('reports.recalculate');
    
    // ZKTeco Custom routes
    Route::post('/zkteco/sync', [ZktecoController::class, 'syncAttendance'])->name('zkteco.sync');
    Route::post('/zkteco/sync-users', [ZktecoController::class, 'syncUsers'])->name('zkteco.sync-users');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
