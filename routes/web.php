<?php

use App\Http\Controllers\BarController;
use App\Http\Controllers\BarSettingsController;
use App\Http\Controllers\BeerController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\PublicRecommendationController;
use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Public routes
Route::get('/', [MarketingController::class, 'index'])->name('home');
Route::get('/b/{slug}', [PublicRecommendationController::class, 'show'])->name('public.bar.show');
Route::post('/b/{slug}/recommend', [PublicRecommendationController::class, 'recommend'])->name('public.bar.recommend');

// Legal pages
Route::get('/mentions-legales', function () {
    return Inertia::render('Legal/Mentions');
})->name('legal.mentions');

Route::get('/politique-de-confidentialite', function () {
    return Inertia::render('Legal/Confidentialite');
})->name('legal.confidentialite');

Route::get('/cgu', function () {
    return Inertia::render('Legal/CGU');
})->name('legal.cgu');

Route::get('/contact', function () {
    return Inertia::render('Legal/Contact');
})->name('legal.contact');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // If user has bars, redirect to first bar
        $bar = $user->bars()->first();
        if ($bar) {
            return redirect()->route('bars.show', $bar);
        }
        
        // Otherwise, show bars index
        return redirect()->route('bars.index');
    })->name('dashboard');

    // Bars
    Route::get('/bars', [BarController::class, 'index'])->name('bars.index');
    Route::get('/bars/{bar}', [BarController::class, 'show'])->name('bars.show');

    // Bar settings
    Route::get('/bars/{bar}/settings', [BarSettingsController::class, 'edit'])->name('bars.settings.edit');
    Route::match(['put', 'post'], '/bars/{bar}/settings', [BarSettingsController::class, 'update'])->name('bars.settings.update');

    // QR Code
    Route::get('/bars/{bar}/qr-code', [QrCodeController::class, 'show'])->name('bars.qr-code.show');
    Route::get('/bars/{bar}/qr-code/download', [QrCodeController::class, 'download'])->name('bars.qr-code.download');

    // Beers
    Route::get('/bars/{bar}/beers', [BeerController::class, 'index'])->name('bars.beers.index');
    Route::get('/bars/{bar}/beers/create', [BeerController::class, 'create'])->name('bars.beers.create');
    Route::post('/bars/{bar}/beers', [BeerController::class, 'store'])->name('bars.beers.store');
    Route::get('/bars/{bar}/beers/{beer}/edit', [BeerController::class, 'edit'])->name('bars.beers.edit');
    Route::put('/bars/{bar}/beers/{beer}', [BeerController::class, 'update'])->name('bars.beers.update');
    Route::delete('/bars/{bar}/beers/{beer}', [BeerController::class, 'destroy'])->name('bars.beers.destroy');
});

require __DIR__.'/settings.php';
