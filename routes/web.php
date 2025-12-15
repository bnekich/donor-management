<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Livewire\Donors;
use App\Livewire\Campaigns;
use App\Livewire\Individuals;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('donors', Donors\Index::class)->name('donors.index');
    Route::get('donors/create', Donors\Create::class)->name('donors.create');
    Route::get('donors/{donor}/edit', Donors\Edit::class)->name('donors.edit');

    Route::get('individuals', Individuals\Index::class)->name('individuals.index');
    Route::get('individuals/create', Individuals\Create::class)->name('individuals.create');
    //Route::get('individuals/{individual}/edit', Individuals\Edit::class)->name('individuals.edit');

    Route::get('campaigns', Campaigns\Index::class)->name('campaigns.index');
    Route::get('campaigns/create', Campaigns\Create::class)->name('campaigns.create');
    Route::get('campaigns/{campaign}/edit', Campaigns\Edit::class)->name('campaigns.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
