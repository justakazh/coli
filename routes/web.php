<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkflowsController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\ScansController;
use App\Http\Controllers\ScopesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\TasksController;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard');

    Route::get('/terminal',[TerminalController::class, 'index'])->name('terminal');
    Route::post('/terminal/start',[TerminalController::class, 'start'])->name('terminal.start');
    Route::post('/terminal/stop',[TerminalController::class, 'stop'])->name('terminal.stop');

    Route::get('/terminal/frame',[TerminalController::class, 'frame'])->name('terminal.frame');

    Route::get('/change-profile',[AuthController::class, 'changeProfile'])->name('change-profile');
    Route::post('/change-profile',[AuthController::class, 'profileUpdate'])->name('profile.update');

    // Scopes
    Route::delete('/scopes/bulk/delete', [ScopesController::class, 'bulkDelete'])->name('scopes.bulk-delete');
    Route::get('/scopes', [ScopesController::class, 'index'])->name('scopes.index');
    Route::get('/scopes/search', [ScopesController::class, 'search'])->name('scopes.search');
    Route::get('/scopes/create', [ScopesController::class, 'create'])->name('scopes.create');
    Route::post('/scopes/create', [ScopesController::class, 'store'])->name('scopes.store');
    Route::get('/scopes/{id}/edit', [ScopesController::class, 'edit'])->name('scopes.edit');
    Route::post('/scopes/{id}', [ScopesController::class, 'update'])->name('scopes.update');
    Route::delete('/scopes/{id}', [ScopesController::class, 'delete'])->name('scopes.delete');
    Route::get('/scopes/{id}/view', [ScopesController::class, 'view'])->name('scopes.view');

    // Scans
    Route::post('/scans/bulk/create', [ScansController::class, 'bulkCreate'])->name('scans.bulk-create');
    Route::post('/scans/bulk/store', [ScansController::class, 'bulkStore'])->name('scans.bulk-store');
    Route::post('/scans/bulk/run', [ScansController::class, 'bulkRun'])->name('scans.bulk-run');
    Route::post('/scans/bulk/stop', [ScansController::class, 'bulkStop'])->name('scans.bulk-stop');
    Route::delete('/scans/bulk/delete', [ScansController::class, 'bulkDelete'])->name('scans.bulk-delete');
    Route::get('/scans', [ScansController::class, 'index'])->name('scans.index');
    Route::get('/scans/search', [ScansController::class, 'search'])->name('scans.search');
    Route::get('/scans/create/{id}', [ScansController::class, 'create'])->name('scans.create');
    Route::post('/scans', [ScansController::class, 'store'])->name('scans.store');
    Route::get('/scans/{id}/run', [ScansController::class, 'run'])->name('scans.run');
    Route::delete('/scans/{id}', [ScansController::class, 'destroy'])->name('scans.delete');
    Route::get('/scans/{id}/stop', [ScansController::class, 'stop'])->name('scans.stop');
    Route::get('/scans/{id}/logs', [ScansController::class, 'logs'])->name('scans.logs');
    Route::get('/scans/{id}/review', [ScansController::class, 'review'])->name('scans.review');
    Route::get('/scans/review-result', [ScansController::class, 'reviewResult'])->name('scans.review-result');
    Route::get('/scans/{id}/output', [ScansController::class, 'output'])->name('scans.output');

    // Workflows
    Route::get('/workflows', [WorkflowsController::class, 'index'])->name('workflows.index');
    Route::get('/workflows/search', [WorkflowsController::class, 'search'])->name('workflows.search');
    Route::get('/workflows/create', [WorkflowsController::class, 'create'])->name('workflows.create');
    Route::post('/workflows/check-tools', [WorkflowsController::class, 'checkTools'])->name('workflows.check-tools');
    Route::post('/workflows', [WorkflowsController::class, 'store'])->name('workflows.store');
    Route::get('/workflows/{id}/edit', [WorkflowsController::class, 'edit'])->name('workflows.edit');
    Route::post('/workflows/{id}', [WorkflowsController::class, 'update'])->name('workflows.update');
    Route::delete('/workflows/{id}', [WorkflowsController::class, 'destroy'])->name('workflows.delete');
    Route::get('/workflows/{id}/download', [WorkflowsController::class, 'download'])->name('workflows.download');

    //Tasks
    Route::get('/tasks', [TasksController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/search', [TasksController::class, 'search'])->name('tasks.search');
    Route::get('/tasks/create', [TasksController::class, 'create'])->name('tasks.create');
    Route::post('/tasks/create', [TasksController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}/edit', [TasksController::class, 'edit'])->name('tasks.edit');
    Route::post('/tasks/{id}', [TasksController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{id}', [TasksController::class, 'destroy'])->name('tasks.delete');

    //API
    Route::post('/api/tasks/create', [TasksController::class, 'storeAPI'])->name('api.tasks.store');
    Route::get('/api/tasks/search', [TasksController::class, 'searchAPI'])->name('api.tasks.search');
    Route::get('/api/tasks', [TasksController::class, 'indexAPI'])->name('api.tasks.index');

    // File Manager
    Route::get('/file-manager',[FileManagerController::class, 'index'])->name('file-manager.index');

});


Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');    
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/captcha/refresh', [AuthController::class, 'refreshCaptcha'])->name('captcha.refresh');