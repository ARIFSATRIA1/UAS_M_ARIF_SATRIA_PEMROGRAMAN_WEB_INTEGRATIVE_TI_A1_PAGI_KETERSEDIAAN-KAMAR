<?php
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HospitalController;


Route::delete('/patients/{id}', [HospitalController::class, 'dischargePatient']);


Route::get('/patients', [HospitalController::class, 'checkAvailability']);
Route::get('/rooms/available', [HospitalController::class, 'checkAvailability']) ;
Route::post('/patients/admit', [HospitalController::class, 'admitPatient']);
Route::post('/patients/discharge/{id}', [HospitalController::class, 'dischargePatient']);
