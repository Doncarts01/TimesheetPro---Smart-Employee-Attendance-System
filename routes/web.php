<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\employeeController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\GoogleGeoAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;






Route::get("admin/dashboard", [adminController::class, 'AdminDashboard'])->middleware(['auth', 'verified'])->name('dashboard');





Route::middleware('auth')->prefix('admin')->group(function () {
    Route::controller(adminController::class)->group(function () {
        Route::get('/logout', 'AdminDestory')->name('admin_logout');
        Route::get('/profile', 'AdminProfile')->name('admin_profile');
        Route::post('/profile/store', 'AdminProfileStore')->name('admin_profile_store');
        Route::get('/change/password', 'ChangePassword')->name('change_password');
        Route::get('/settings', 'adminSettings')->name('admin_settings');
        Route::get('/location-settings', 'adminLocationSettings')->name('admin_location_settings');
        Route::post('/update/settings', 'adminUpdateSettings')->name('admin_update_settings');
        Route::post('/update/location-settings', 'adminUpdateLoationSettings')->name('admin_update_location_settings');
        Route::get('/manage/timesheet', 'ManageTimesheet')->name('admin_manage_timesheet');
        Route::post('/update/password', 'UpdatePassword')->name('admin_update_password');
    });


    Route::controller(employeeController::class)->group(function () {
        Route::get('/manage/employees', 'adminManageEmployees')->name('admin_manage_employees');
        Route::post('/store/employees', 'adminStoreEmployees')->name('admin_store_employees');
        Route::post('/update/employees', 'adminUpdateEmployees')->name('admin_update_employees');
        Route::get('/delete/employees/{id}', 'adminDeleteEmployees')->name('admin_delete_employees');

    });

});



// Route::get('/clock-geo-validate', [GoogleGeoAuthController::class, 'validateClockingRequest'])
//     ->name('clock.validate'); 

// // 2. Route for the Google Redirect (Called internally by handleClockingRequest)
// Route::get('/auth/google/redirect', [GoogleGeoAuthController::class, 'redirectToGoogle'])
//     ->name('google.redirect');

// // 3. Route for the Google Callback (Completes OAuth, final validation, and clocking)
// // This URL must be configured in your Google Cloud Project.
// Route::get('/auth/google/callback', [GoogleGeoAuthController::class, 'handleGoogleCallback'])
//     ->name('google.callback');






// In routes/web.php
Route::get('/', [GoogleGeoAuthController::class, 'Home'])->name('home');
Route::get('/clock/scan/{token}', [GoogleGeoAuthController::class, 'show'])->name('clock.scan');
Route::post('/clock/validate', [GoogleGeoAuthController::class, 'validateClockingRequest'])->name('clock.validate');
Route::get('/auth/google/redirect', [GoogleGeoAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleGeoAuthController::class, 'handleGoogleCallback'])->name('google.callback');



require __DIR__ . '/auth.php';
