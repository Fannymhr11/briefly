<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BriefController;
use App\Http\Controllers\BriefFileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Marketing\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\User\BriefController as UserBriefController;
use Illuminate\Support\Facades\Route;

/* ---------- Publik ---------- */
Route::get('/', fn () => auth()->check() ? redirect()->to(auth()->user()->dashboardUrl()) : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/* ---------- File brief (otorisasi per role di controller) ---------- */
Route::middleware('auth')->group(function () {
    Route::get('/briefs/{brief}/preview', [BriefFileController::class, 'preview'])->name('briefs.preview');
    Route::get('/briefs/{brief}/download', [BriefFileController::class, 'download'])->name('briefs.download');
});

/** Halaman yang dimiliki setiap role: profile. */
$profileRoutes = function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::get('history', [HistoryController::class, 'index'])->name('history');
};

/* ---------- ADMIN ---------- */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () use ($profileRoutes) {
    Route::get('dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('briefs', [BriefController::class, 'index'])->name('briefs');
    Route::get('briefs/{brief}', [BriefController::class, 'show'])->name('briefs.show');
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks');
    Route::get('tasks/{task}', [TaskController::class, 'index'])->name('tasks.show');
    $profileRoutes();
});

/* ---------- USER ---------- */
Route::prefix('user')->name('user.')->middleware(['auth', 'role:user'])->group(function () use ($profileRoutes) {
    Route::get('dashboard', [DashboardController::class, 'user'])->name('dashboard');
    Route::get('briefs/create', [UserBriefController::class, 'create'])->name('briefs.create');
    Route::post('briefs', [UserBriefController::class, 'store'])->name('briefs.store');
    Route::get('briefs', [UserBriefController::class, 'index'])->name('briefs.index');
    Route::get('briefs/{brief}', [UserBriefController::class, 'show'])->name('briefs.show');
    $profileRoutes();
});

/* ---------- MARKETING COMMUNICATION ---------- */
Route::prefix('marketing')->name('marketing.')->middleware(['auth', 'role:marketing'])->group(function () use ($profileRoutes) {
    Route::get('dashboard', [DashboardController::class, 'marketing'])->name('dashboard');
    Route::get('review', [ReviewController::class, 'index'])->name('review');
    Route::get('briefs', [BriefController::class, 'index'])->name('briefs');
    Route::get('briefs/{brief}', [BriefController::class, 'show'])->name('briefs.show');
    Route::post('briefs/{brief}/approve', [ReviewController::class, 'approve'])->name('briefs.approve');
    Route::post('briefs/{brief}/reject', [ReviewController::class, 'reject'])->name('briefs.reject');
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks');
    Route::get('tasks/{task}', [TaskController::class, 'index'])->name('tasks.show');
    $profileRoutes();
});

/* ---------- TIM KREATIF ---------- */
Route::prefix('creative')->name('creative.')->middleware(['auth', 'role:creative'])->group(function () use ($profileRoutes) {
    Route::get('dashboard', [DashboardController::class, 'creative'])->name('dashboard');
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks');
    Route::get('tasks/{task}', [TaskController::class, 'index'])->name('tasks.show');
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
    $profileRoutes();
});
