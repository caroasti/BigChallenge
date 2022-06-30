<?php

use App\Http\Controllers\CreateSubmissionController;
use App\Http\Controllers\DeletePrescriptionController;
use App\Http\Controllers\DeleteSubmissionController;
use App\Http\Controllers\GetDoctorInformationController;
use App\Http\Controllers\GetPatientInformationController;
use App\Http\Controllers\GetPrescriptionController;
use App\Http\Controllers\GetSubmissionController;
use App\Http\Controllers\ListSubmissionController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResendVerificationEmailController;
use App\Http\Controllers\TakeSubmissionController;
use App\Http\Controllers\DoneSubmissionController;
use App\Http\Controllers\UpdateDoctorInformationController;
use App\Http\Controllers\UpdatePatientInformationController;
use App\Http\Controllers\UpdateSymptomsController;
use App\Http\Controllers\UploadPrescriptionController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// @TODO: create view for email not verified yet (when y add the midlleware verified and a user attempts to access, then they will be redirected to the verification.notice named route.)

Route::middleware(['guest:sanctum'])->group(function () {
    Route::post('/login', LoginController::class);
    Route::post('/register', RegisterController::class);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', LogoutController::class);
    Route::post('/createSubmission', CreateSubmissionController::class);
    Route::post('/updatePatientInformation', UpdatePatientInformationController::class);
    Route::get('/submission', ListSubmissionController::class);
    Route::put('/submission/{submission}/patient', UpdateSymptomsController::class);
    Route::get('/getDoctorInformation/{doctorInformation:user_id}', GetDoctorInformationController::class);
    Route::get('/getPatientInformation/{patientInformation:user_id}', GetPatientInformationController::class);
    Route::get('/submission/{submission}', GetSubmissionController::class);
    Route::get('/submission/prescription/{submission}', GetPrescriptionController::class);

    Route::middleware(['role:doctor'])->group(function () {
        Route::post('/updateDoctorInformation', UpdateDoctorInformationController::class);
        Route::post('/submission/{submission}/take', TakeSubmissionController::class);
        Route::post('/submission/{submission}/done', DoneSubmissionController::class);
        Route::post('/submission/{submission}/prescription', UploadPrescriptionController::class); // dont know
        Route::delete('/submission/{submission}/prescription', DeletePrescriptionController::class);
    });

    Route::middleware(['role:patient'])->group(function () {
        Route::delete('/submission/{submission}', DeleteSubmissionController::class);
    });
});

Route::middleware(['auth:sanctum', 'signed'])->group(function () {
    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)->name('verification.verify');
});

Route::middleware(['auth:sanctum', 'throttle:6,1'])->group(function () {
    Route::post('/email/verification-notification', ResendVerificationEmailController::class)->name('verification.send');
});
