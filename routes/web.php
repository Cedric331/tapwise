<?php

use App\Http\Controllers\BarController;
use App\Http\Controllers\BarSettingsController;
use App\Http\Controllers\BarSubscriptionController;
use App\Http\Controllers\BeerController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\PublicRecommendationController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/', [MarketingController::class, 'index'])->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/b/{slug}', [PublicRecommendationController::class, 'show'])->name('public.bar.show');
Route::post('/b/{slug}/recommend', [PublicRecommendationController::class, 'recommend'])->name('public.bar.recommend');
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('cashier.webhook');

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
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        $barId = session('current_bar_id');
        if ($barId) {
            $bar = $user->is_admin
                ? \App\Models\Bar::find($barId)
                : $user->bars()->where('bars.id', $barId)->first();

            if ($bar) {
                return redirect()->route('bars.show', $bar);
            }
        }

        $bar = $user->bars()->first();
        if ($bar) {
            return redirect()->route('bars.show', $bar);
        }

        return redirect()->route('bars.index');
    })->name('dashboard');

    // Bars
    Route::get('/bars', [BarController::class, 'index'])->name('bars.index');
    Route::post('/bars', [BarController::class, 'store'])->name('bars.store');
    Route::get('/bars/{bar}', [BarController::class, 'show'])->name('bars.show');
    Route::delete('/bars/{bar}', [BarController::class, 'destroy'])->name('bars.destroy');

    // Bar settings
    Route::get('/bars/{bar}/settings', [BarSettingsController::class, 'edit'])->name('bars.settings.edit');
    Route::match(['put', 'post'], '/bars/{bar}/settings', [BarSettingsController::class, 'update'])->name('bars.settings.update');

    // Subscriptions
    Route::post('/bars/{bar}/subscription/checkout', [BarSubscriptionController::class, 'checkout'])->name('bars.subscription.checkout');
    Route::get('/bars/{bar}/subscription/portal', [BarSubscriptionController::class, 'portal'])->name('bars.subscription.portal');

    // QR Code
    Route::get('/bars/{bar}/qr-code', [QrCodeController::class, 'show'])->name('bars.qr-code.show');
    Route::get('/bars/{bar}/qr-code/download', [QrCodeController::class, 'download'])->name('bars.qr-code.download');
    Route::put('/bars/{bar}/qr-code/status', [QrCodeController::class, 'updateStatus'])->name('bars.qr-code.status');

    // Beers
    Route::get('/bars/{bar}/beers', [BeerController::class, 'index'])->name('bars.beers.index');
    Route::get('/bars/{bar}/beers/template', [BeerController::class, 'template'])->name('bars.beers.template');
    Route::post('/bars/{bar}/beers/import/preview', [BeerController::class, 'previewImport'])->name('bars.beers.import.preview');
    Route::post('/bars/{bar}/beers/import', [BeerController::class, 'import'])->name('bars.beers.import');
    Route::get('/bars/{bar}/beers/create', [BeerController::class, 'create'])->name('bars.beers.create');
    Route::post('/bars/{bar}/beers', [BeerController::class, 'store'])->name('bars.beers.store');
    Route::get('/bars/{bar}/beers/{beer}/edit', [BeerController::class, 'edit'])->name('bars.beers.edit');
    Route::put('/bars/{bar}/beers/{beer}', [BeerController::class, 'update'])->name('bars.beers.update');
    Route::delete('/bars/{bar}/beers/{beer}', [BeerController::class, 'destroy'])->name('bars.beers.destroy');
});

require __DIR__.'/settings.php';
