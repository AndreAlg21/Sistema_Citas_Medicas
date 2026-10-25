<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/inicio', function () {
        return view('inicio');
    })->name('inicio');

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

    Route::resource('usuarios', UserController::class)
        ->middleware('administrador');

    Route::resource('roles', RoleController::class)
        ->middleware('administrador');

    Route::resource('especialidades', EspecialidadController::class)
        ->middleware('administrador');

    Route::resource('medicos', MedicoController::class)
        ->middleware('administrador');

    Route::resource('horarios', HorarioController::class)
        ->middleware('administrador');

    Route::resource('pacientes', PacienteController::class)
        ->middleware('administrador');
});
