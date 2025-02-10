<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CitySettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseClassificationController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\ExpenseController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DocumentationController;

Route::get('/', function () {
    return view('welcome');
});

// Desabilita o registro de novos usuários
Auth::routes(['register' => false]);

// Rotas que requerem apenas autenticação
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/home', '/dashboard');

    // Perfil do Usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Relatórios
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // Categorias (somente visualização)
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}/children', [CategoryController::class, 'getChildren'])->name('categories.children');
    Route::get('categories/parents/available', [CategoryController::class, 'getAvailableParents'])->name('categories.parents.available');

    // Classificação de Despesas (somente visualização)
    Route::get('expense-classifications', [ExpenseClassificationController::class, 'index'])->name('expense-classifications.index');

    // Receitas
    Route::resource('revenues', RevenueController::class);
    Route::get('revenues/categories/blocos/{fonte}', [RevenueController::class, 'getBlocos'])->name('revenues.blocos');
    Route::get('revenues/categories/grupos/{bloco}', [RevenueController::class, 'getGrupos'])->name('revenues.grupos');
    Route::get('revenues/categories/acoes/{grupo}', [RevenueController::class, 'getAcoes'])->name('revenues.acoes');

    // Despesas
    Route::resource('expenses', ExpenseController::class);
    Route::get('expenses/categories/blocos/{fonte}', [ExpenseController::class, 'getBlocos'])->name('expenses.blocos');
    Route::get('expenses/categories/grupos/{bloco}', [ExpenseController::class, 'getGrupos'])->name('expenses.grupos');
    Route::get('expenses/categories/acoes/{grupo}', [ExpenseController::class, 'getAcoes'])->name('expenses.acoes');

    // Documentação
    Route::get('manual', [DocumentationController::class, 'manual'])->name('documentation.manual');
    Route::get('privacy', [DocumentationController::class, 'privacy'])->name('documentation.privacy');
    Route::get('terms', [DocumentationController::class, 'terms'])->name('documentation.terms');
    Route::get('lgpd', [DocumentationController::class, 'lgpd'])->name('documentation.lgpd');
});

// Rotas protegidas por autenticação e admin
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    // Usuários
    Route::resource('users', UserController::class);
    
    // Configurações da Cidade
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('city', [CitySettingsController::class, 'edit'])->name('city.edit');
        Route::put('city', [CitySettingsController::class, 'update'])->name('city.update');
    });

    // Categorias (criação, edição e exclusão)
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');

    // Classificação de Despesas (criação, edição e exclusão)
    Route::post('expense-classifications', [ExpenseClassificationController::class, 'store'])->name('expense-classifications.store');
    Route::get('expense-classifications/create', [ExpenseClassificationController::class, 'create'])->name('expense-classifications.create');
    Route::put('expense-classifications/{expense_classification}', [ExpenseClassificationController::class, 'update'])->name('expense-classifications.update');
    Route::delete('expense-classifications/{expense_classification}', [ExpenseClassificationController::class, 'destroy'])->name('expense-classifications.destroy');
    Route::get('expense-classifications/{expense_classification}/edit', [ExpenseClassificationController::class, 'edit'])->name('expense-classifications.edit');
});

// Rotas de Auditoria (protegidas por autenticação e admin)
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('audit', [AuditLogController::class, 'index'])->name('audit.index');
    Route::get('audit/{log}', [AuditLogController::class, 'show'])->name('audit.show');
});

// Rotas para carregar categorias filhas
Route::get('/api/categories/{category}/children', [CategoryController::class, 'getChildren'])->name('api.categories.children');
Route::get('/api/categories/available-parents', [CategoryController::class, 'getAvailableParents'])->name('api.categories.parents');

// Middleware para verificar se é admin
