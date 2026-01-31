<?php

use App\Livewire\Campaigns;
use App\Livewire\Donations;
use App\Livewire\Donors;
use App\Livewire\GivebutterSync;
use App\Livewire\ProcessorMappings;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Users;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->guest()) {
        return redirect()->route('login');
    }

    if (auth()->user()->mustChangePassword()) {
        return redirect()->route('password.change');
    }

    return redirect()->route('dashboard');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'password.change.required', 'two-factor.required'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('password/change', \App\Livewire\Auth\ChangePassword::class)
        ->name('password.change');
});

Route::middleware(['auth', 'password.change.required', 'two-factor.required'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('donations', Donations\Index::class)->name('donations.index');

    Route::get('donors', Donors\Index::class)->name('donors.index');
    Route::get('donors/create', Donors\Create::class)->name('donors.create');
    Route::get('donors/{donor}/edit', Donors\Edit::class)->name('donors.edit');

    Route::get('campaigns', Campaigns\Index::class)->name('campaigns.index');
    Route::get('campaigns/create', Campaigns\Create::class)->name('campaigns.create');
    Route::get('campaigns/{campaign}/edit', Campaigns\Edit::class)->name('campaigns.edit');

    Route::get('givebutter-sync', GivebutterSync\Index::class)->name('givebutter-sync.index');
    Route::get('processor-mappings', ProcessorMappings\Index::class)->name('processor-mappings.index');
    Route::get('processor-mappings/create', ProcessorMappings\Create::class)->name('processor-mappings.create');
    Route::get('processor-mappings/{processorMapping}/edit', ProcessorMappings\Edit::class)->name('processor-mappings.edit');

    Route::middleware('can:manageUsers')->group(function () {
        Route::get('users', Users\Index::class)->name('users.index');
        Route::get('users/create', Users\Create::class)->name('users.create');
        Route::get('users/{user}', Users\Show::class)->name('users.show');
        Route::get('users/{user}/edit', Users\Edit::class)->name('users.edit');
    });

    Route::get('settings/two-factor', TwoFactor::class)->name('two-factor.show');
});
