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

        Route::get('/acquisition-processes/{process}/reporte-participantes', [AcquisitionProcessController::class, 'generateParticipantesReport'])
    ->name('process.report.participantes');

        Route::get('/acquisition-processes/{process}/reporte-no-participantes', [AcquisitionProcessController::class, 'generateNoParticipantesReport'])
    ->name('process.report.no-participantes');
    });

    // Rutas para Administrador, Analista y Supervisor
    Route::middleware(['can:viewAny,' . \App\Models\AuditLog::class])->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs.index');
        Route::get('/audit-logs/pdf', [AuditLogController::class, 'generatePdf'])->name('audit.logs.pdf');
        // Ruta para generar presupuesto consolidado (asumiendo que es en el controlador de proceso)
        Route::get('/acquisition-processes/{process}/presupuesto-consolidado', [AcquisitionProcessController::class, 'generatePresupuestoConsolidado'])->name('acquisition-processes.presupuesto.consolidado');
    });

    // Reemplazar la sección de rutas de requests con esto:

// Rutas para Usuarios (Dependencias) - Flujo Principal
Route::middleware(['can:create,' . \App\Models\Request::class])->group(function () {
    // Ver procesos abiertos disponibles para la dependencia
    Route::get('/my-requests/open-processes', [RequestController::class, 'listOpenProcesses'])->name('requests.open.processes');
    
    // Crear solicitud para un proceso específico
    Route::get('/requests/create-for-process/{process}', [RequestController::class, 'createForProcess'])->name('requests.create.for.process');
    
    // PASO 1: Mostrar formulario de rubros (participación)
    Route::get('/requests/create-details/{process}', [RequestController::class, 'createDetails'])->name('requests.create.details');
    
    // PASO 2: Guardar solicitud inicial y mostrar vista previa
    Route::post('/requests/store-details/{process}', [RequestController::class, 'storeDetails'])->name('requests.store.details');
    
    // PASO 3: Mostrar vista previa con PDFs
    Route::get('/requests/{request}/preview', [RequestController::class, 'preview'])->name('requests.preview');
    // Ver y editar sus propias solicitudes
    Route::get('/my-requests', [RequestController::class, 'myRequests'])->name('requests.my.index');
    Route::get('/requests/{request}/edit-details', [RequestController::class, 'editDetails'])->name('requests.edit.details');
    Route::put('/requests/{request}/update-details', [RequestController::class, 'updateDetails'])->name('requests.update.details');
    
    // Confirmar envío final (transición a estado "submitted")
    Route::post('/requests/{request}/submit', [RequestController::class, 'submit'])->name('requests.submit');
    
    // Ruta para mostrar el formulario de no participación
    Route::get('/requests/confirm-non-participation-form/{process}', [RequestController::class, 'showConfirmNonParticipationForm'])->name('requests.show.confirm.non.participation');
    Route::post('/requests/confirm-non-participation/{acquisitionProcess}', [RequestController::class, 'confirmNonParticipation'])->name('requests.confirm.non.participation');
    
    // Generar PDFs (vista previa y definitivos)
    Route::get('/requests/{request}/comprobante', [RequestController::class, 'generateComprobante'])->name('requests.comprobante');
    Route::get('/requests/{request}/presupuesto-individual', [RequestController::class, 'generatePresupuestoIndividual'])->name('requests.presupuesto.individual');

    // NUEVAS RUTAS para PDFs definitivos
    Route::get('/requests/{request}/presupuesto-definitivo', [RequestController::class, 'downloadPresupuestoDefinitivo'])->name('requests.presupuesto.definitivo');
    Route::get('/requests/{request}/comprobante-definitivo', [RequestController::class, 'downloadComprobanteDefinitivo'])->name('requests.comprobante.definitivo');

    // Ruta para ver el comprobante de NO participación definitivo
    Route::get('/requests/{request}/comprobante-no-participacion', [RequestController::class, 'downloadComprobanteNoParticipacion'])->name('requests.comprobante.no.participacion');

});

    // Rutas para Analista y Supervisor (ver solicitudes de todos)
    Route::middleware(['can:viewAny,' . \App\Models\Request::class])->group(function () {
        Route::resource('requests', RequestController::class)->except(['create', 'store']); // Excluir create/store para analista/supervisor
    });

});