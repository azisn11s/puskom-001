<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth
Route::post('login', [LoginController::class, 'login']);

// Should authenticated
Route::middleware(['auth:sanctum'])->group(function(){

    Route::get('user', [UserController::class, 'me']);

    // Admin
    Route::group(['prefix'=> 'admin'], function(){
        
        // Role
        Route::apiResource('roles', RoleController::class);
        Route::post('role-features/{role}', [RoleController::class, 'addFeatureAbility']);

        // Feature
        Route::apiResource('features', FeatureController::class);

        // User
        Route::get('users/export-excel', [UserController::class, 'export'])->name('user.export');
        Route::put('users/{user}/change-password', [UserController::class, 'changePassword']);
        Route::patch('users/{user}/default-password', [UserController::class, 'defaultPassword']);
        Route::apiResource('users', UserController::class);

    });
    

    // Dashboard
    Route::group(['prefix'=> 'dashboard'], function(){   

        Route::group(['prefix'=> 'counter'], function(){   
            Route::get('company', [DashboardController::class, 'counterCompany']);
            Route::get('employee', [DashboardController::class, 'counterEmployee']);
            Route::get('doctor', [DashboardController::class, 'counterDoctor']);
            Route::get('team', [DashboardController::class, 'counterTeam']);
        });
    });

    
});