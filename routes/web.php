<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RecuperarContrasenaController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PortalPacienteController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware(['guest', 'bitacora'])->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/contrasena/recuperar', [RecuperarContrasenaController::class, 'solicitar'])
        ->name('password.request');
    Route::post('/contrasena/recuperar', [RecuperarContrasenaController::class, 'enviarEnlace'])
        ->name('password.email');
    Route::get('/contrasena/restablecer/{token}', [RecuperarContrasenaController::class, 'formularioRestablecer'])
        ->name('password.reset');
    Route::post('/contrasena/restablecer', [RecuperarContrasenaController::class, 'restablecer'])
        ->name('password.update');

    Route::get('/registro', [PortalPacienteController::class, 'formularioRegistro'])
        ->name('pacientes.registro');
    Route::post('/registro', [PortalPacienteController::class, 'registrar'])
        ->name('pacientes.registro.store');
});

Route::middleware(['auth', 'bitacora'])->group(function (): void {
    Route::get('/inicio', fn () => view('inicio'))->name('inicio');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/citas/disponibilidad', [CitaController::class, 'disponibilidad'])
        ->middleware('permiso:citas.consultar_disponibilidad')
        ->name('citas.disponibilidad');
    Route::get('/citas/registrar', [CitaController::class, 'create'])
        ->middleware('permiso:citas.registrar')
        ->name('citas.create');
    Route::get('/pacientes/buscar-por-ci', [CitaController::class, 'buscarPacientePorCi'])
        ->middleware('permiso:citas.registrar')
        ->name('pacientes.buscar-ci');
    Route::post('/citas', [CitaController::class, 'store'])
        ->middleware('permiso:citas.registrar')
        ->name('citas.store');
    Route::post('/citas/reservar', [CitaController::class, 'reservarEnLinea'])
        ->middleware('permiso:citas.reservar_en_linea')
        ->name('citas.reservar');

    Route::get('/citas/agenda', [CitaController::class, 'agenda'])
        ->middleware('permiso:agenda.consultar')
        ->name('citas.agenda');
    Route::get('/pacientes/{paciente}/historial-citas', [CitaController::class, 'historialPaciente'])
        ->middleware('permiso:citas.consultar_historial')
        ->name('citas.historial-paciente');

    Route::get('/citas/{cita}/editar', [CitaController::class, 'edit'])
        ->middleware('permiso:citas.reprogramar')
        ->name('citas.edit');
    Route::put('/citas/{cita}', [CitaController::class, 'update'])
        ->middleware('permiso:citas.reprogramar')
        ->name('citas.update');
    Route::post('/citas/{cita}/cancelar', [CitaController::class, 'cancelar'])
        ->middleware('permiso:citas.cancelar')
        ->name('citas.cancelar');
    Route::patch('/citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])
        ->middleware('permiso:citas.controlar_asistencia')
        ->name('citas.estado');
    Route::get('/citas/{cita}', [CitaController::class, 'show'])
        ->middleware('permiso:agenda.consultar')
        ->name('citas.show');

    Route::get('/reportes', [ReporteController::class, 'index'])
        ->middleware('permiso:reportes.generar')
        ->name('reportes.index');

    Route::get('/mi-perfil', [PortalPacienteController::class, 'perfil'])
        ->middleware('permiso:pacientes.gestionar_perfil')
        ->name('portal.perfil');
    Route::put('/mi-perfil', [PortalPacienteController::class, 'actualizarPerfil'])
        ->middleware('permiso:pacientes.gestionar_perfil')
        ->name('portal.perfil.actualizar');
    Route::get('/mis-citas', [PortalPacienteController::class, 'misCitas'])
        ->middleware('permiso:citas.consultar_mis_citas')
        ->name('portal.citas');
    Route::get('/mi-historial-medico', [PortalPacienteController::class, 'miHistorialMedico'])
        ->middleware('permiso:historial_medico.consultar')
        ->name('portal.historial-medico');

    Route::resource('usuarios', UserController::class)->middleware('administrador');
    Route::resource('roles', RoleController::class)->middleware('administrador');
    Route::resource('especialidades', EspecialidadController::class)->middleware('administrador');
    Route::resource('medicos', MedicoController::class)->middleware('administrador');
    Route::resource('horarios', HorarioController::class)->middleware('administrador');
    Route::resource('pacientes', PacienteController::class)->middleware('permiso:pacientes.gestionar');
    Route::get('/bitacora', [BitacoraController::class, 'index'])
        ->middleware(['administrador', 'permiso:bitacoras.consultar'])
        ->name('bitacora.index');
});
