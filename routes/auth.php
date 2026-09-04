<?php

use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\Portal\AnnouncementsPage;
use App\Livewire\Portal\CalendarPage;
use App\Livewire\Portal\DashboardPage;
use App\Livewire\Portal\LinkChildPage;
use App\Livewire\Portal\ProfilePage;
use App\Livewire\Portal\SchedulePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class)->name('register');
    Route::get('/forgot-password', ForgotPasswordPage::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPasswordPage::class)->name('password.reset');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:student,parent,super_admin,school_admin,principal,teacher,admin_staff'])
    ->prefix('portal')
    ->name('portal.')
    ->group(function () {
        Route::get('/', DashboardPage::class)->name('dashboard');
        Route::get('/jadwal', SchedulePage::class)->name('schedule');
        Route::get('/kalender', CalendarPage::class)->name('calendar');
        Route::get('/pengumuman', AnnouncementsPage::class)->name('announcements');
        Route::get('/profil', ProfilePage::class)->name('profile');

        Route::get('/tautkan-anak', LinkChildPage::class)
            ->middleware('role:parent')
            ->name('link-child');
    });
