<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScansController;
use App\Http\Controllers\ScopesController;
use App\Http\Controllers\WorkflowsController;
use App\Http\Controllers\ExplorerController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConsoleController;
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');


Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Scans
    Route::get('/scans', [ScansController::class, 'index'])->name('scans');
    Route::post('/scans/create', [ScansController::class, 'create'])->name('scans.create');
    Route::delete('/scans/destroy/{id}', [ScansController::class, 'destroy'])->name('scans.destroy');
    Route::post('/scans/action/{id}', [ScansController::class, 'action'])->name('scans.action');
    Route::get('/scans/track/{id}', [TrackController::class, 'index'])->name('scans.track');
    // Workflows
    Route::get('/workflows', [WorkflowsController::class, 'index'])->name('workflows');
    Route::delete('/workflows/destroy/{id}', [WorkflowsController::class, 'destroy'])->name('workflows.destroy');
    Route::get('/workflows/create/script', [WorkflowsController::class, 'createScript'])->name('workflows.create.script');
    Route::post('/workflows/store/script', [WorkflowsController::class, 'storeScript'])->name('workflows.store.script');
    Route::get('/workflows/edit/script/{id}', [WorkflowsController::class, 'editScript'])->name('workflows.edit.script');
    Route::post('/workflows/update/script/{id}', [WorkflowsController::class, 'updateScript'])->name('workflows.update.script');
    Route::get('/workflows/create/diagram', [WorkflowsController::class, 'createDiagram'])->name('workflows.create.diagram');
    Route::post('/workflows/store/diagram', [WorkflowsController::class, 'storeDiagram'])->name('workflows.store.diagram');
    Route::post('/workflows/update/diagram/{id}', [WorkflowsController::class, 'updateDiagram'])->name('workflows.update.diagram');
    Route::get('/workflows/edit/diagram/{id}', [WorkflowsController::class, 'editDiagram'])->name('workflows.edit.diagram');
    Route::get('/workflows/download/{id}', [WorkflowsController::class, 'download'])->name('workflows.download');
    
    
    //explorer
    Route::get('/explorer/{id}', [ExplorerController::class, 'index'])->name('explorer');
    Route::get('/explorer/view/{id}', [ExplorerController::class, 'view'])->name('explorer.view');
    Route::get('/explorer/download/{id}', [ExplorerController::class, 'download'])->name('explorer.download');
    Route::get('/explorer/export/{id}', [ExplorerController::class, 'export'])->name('explorer.export');
    
    //profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');


    //terminal
    Route::get('/console', [ConsoleController::class, 'index'])->name('console');
    Route::post('/console', [ConsoleController::class, 'action'])->name('console.action');

    
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});