<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ZktecoSyncController;
use App\Http\Middleware\VerifySyncToken;

Route::middleware([VerifySyncToken::class])->group(function () {
    Route::post('/sync/attendance', [ZktecoSyncController::class, 'syncAttendance']);
    Route::post('/sync/users', [ZktecoSyncController::class, 'syncUsers']);
});
