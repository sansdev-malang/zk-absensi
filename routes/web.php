<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DeviceController;

use App\Http\Controllers\ZktecoController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('/tutorial', 'tutorial')->name('tutorial');
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/update-shift', [UserController::class, 'updateShift'])->name('users.update-shift');
    Route::resource('attendances', AttendanceController::class);
    Route::resource('devices', DeviceController::class);
    Route::post('devices/{device}/clear-logs', [DeviceController::class, 'clearLogs'])->name('devices.clear-logs');

    if (app()->environment('local')) {
        Route::post('zkteco/sync-attendance', [ZktecoController::class, 'syncAttendance'])->name('zkteco.sync-attendance');
        Route::post('zkteco/sync-users', [ZktecoController::class, 'syncUsers'])->name('zkteco.sync-users');
    }

    Route::resource('shifts', \App\Http\Controllers\ShiftController::class);
    Route::resource('schedules', \App\Http\Controllers\ScheduleController::class)->except(['show', 'edit', 'update']);
    Route::post('schedules/auto-generate', [\App\Http\Controllers\ScheduleController::class, 'autoGenerate'])->name('schedules.auto-generate');
    
    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    // Bonus Schemes
    Route::resource('bonus-schemes', \App\Http\Controllers\BonusSchemeController::class);
    Route::post('bonus-schemes/{bonusScheme}/rules', [\App\Http\Controllers\BonusSchemeController::class, 'storeRule'])->name('bonus-schemes.rules.store');
    Route::delete('bonus-rules/{bonusRule}', [\App\Http\Controllers\BonusSchemeController::class, 'destroyRule'])->name('bonus-rules.destroy');
    
    // Laporan & Bonus
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/summary', [\App\Http\Controllers\ReportController::class, 'summary'])->name('reports.summary');
    Route::get('reports/summary/print', [\App\Http\Controllers\ReportController::class, 'printSummary'])->name('reports.summary.print');
    Route::post('reports/recalculate', [\App\Http\Controllers\ReportController::class, 'recalculate'])->name('reports.recalculate');
    

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
