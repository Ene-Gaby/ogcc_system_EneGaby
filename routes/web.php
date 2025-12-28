<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    DependencyController,
    AcquisitionProcessController,
    RubroController,
    RequestController,
    RequestDetailController,
    AuditLogController,
    PasswordController
};

// Ruta principal post-login
Route::get('/', function () {
    return view('home'); // Asumiendo que creas una vista 'home.blade.php'
})->middleware(['auth'])->name('home');

// Definir rutas de autenticación
Auth::routes();

// Agrupar rutas que requieren autenticación
Route::middleware(['auth'])->group(function () {
    
    Route::get('/password/change', [PasswordController::class, 'change'])->name('password.change');
    Route::post('/password/update', [PasswordController::class, 'update'])->name('password.update');

    // Rutas para Administrador
    Route::middleware(['can:viewAny,' . \App\Models\User::class])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('dependencies', DependencyController::class);
    });

    // Rutas para Administrador y Analista (Fase Previa)
    Route::middleware(['can:viewAny,' . \App\Models\AcquisitionProcess::class])->group(function () {
        Route::resource('acquisition-processes', AcquisitionProcessController::class);
        Route::resource('rubros', RubroController::class);
    });

    // Rutas para Administrador, Analista y Supervisor
    Route::middleware(['can:viewAny,' . \App\Models\AuditLog::class])->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs.index');
        // Ruta para generar presupuesto consolidado (asumiendo que es en el controlador de proceso)
        Route::get('/acquisition-processes/{process}/presupuesto-consolidado', [AcquisitionProcessController::class, 'generatePresupuestoConsolidado'])->name('acquisition-processes.presupuesto.consolidado');
    });

    // Rutas para Usuarios (Dependencias) - Flujo Principal
    Route::middleware(['can:create,' . \App\Models\Request::class])->group(function () {
        // Ver procesos abiertos disponibles para la dependencia
        Route::get('/my-requests/open-processes', [RequestController::class, 'listOpenProcesses'])->name('requests.open.processes');
        // Crear solicitud para un proceso específico
        Route::get('/requests/create/{process}', [RequestController::class, 'createForProcess'])->name('requests.create.for.process');
        Route::post('/requests/store-for-process/{process}', [RequestController::class, 'storeForProcess'])->name('requests.store.for.process');
        // Ver y editar sus propias solicitudes
        Route::get('/my-requests', [RequestController::class, 'myRequests'])->name('requests.my.index');
        Route::get('/requests/{request}/edit-details', [RequestController::class, 'editDetails'])->name('requests.edit.details');
        Route::put('/requests/{request}/update-details', [RequestController::class, 'updateDetails'])->name('requests.update.details');
        // Confirmar participación o no participación
        Route::post('/requests/{request}/confirm-participation', [RequestController::class, 'confirmParticipation'])->name('requests.confirm.participation');
        Route::post('/requests/{request}/confirm-non-participation', [RequestController::class, 'confirmNonParticipation'])->name('requests.confirm.non.participation');
        // Generar PDFs (solo si tienen permiso de ver la solicitud)
        Route::get('/requests/{request}/comprobante', [RequestController::class, 'generateComprobante'])->name('requests.comprobante');
        Route::get('/requests/{request}/presupuesto-individual', [RequestController::class, 'generatePresupuestoIndividual'])->name('requests.presupuesto.individual');
    });

    // Rutas para Analista y Supervisor (ver solicitudes de todos)
    Route::middleware(['can:viewAny,' . \App\Models\Request::class])->group(function () {
        Route::resource('requests', RequestController::class)->except(['create', 'store']); // Excluir create/store para analista/supervisor
    });
});