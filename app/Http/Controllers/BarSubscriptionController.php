<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BarSubscriptionController extends Controller
{
    public function checkout(Request $request, Bar $bar)
    {
        $this->authorize('update', $bar);

        $billingUser = $bar->billingUser();
        if (! $billingUser) {
            abort(404);
        }

        if (! $request->user()->is_admin && $request->user()->id !== $billingUser->id) {
            abort(403);
        }

        if ($bar->hasActiveSubscription()) {
            return redirect()->route('bars.show', $bar);
        }

        if (! config('cashier.secret') || ! config('cashier.key')) {
            Log::error('Stripe keys not configured', [
                'has_secret' => (bool) config('cashier.secret'),
                'has_key' => (bool) config('cashier.key'),
            ]);

            abort(500, 'Stripe keys not configured.');
        }

        $priceId = config('services.stripe.bar_price_id');
        if (! $priceId) {
            Log::error('Stripe price id not configured');
            abort(500, 'Stripe price id not configured.');
        }

        try {
            /** @var \Laravel\Cashier\Checkout $checkoutResponse */
            $checkoutResponse = $billingUser
                ->newSubscription($bar->subscriptionName(), $priceId)
                ->checkout([
                    'success_url' => route('bars.show', $bar, ['subscribed' => 1]),
                    'cancel_url' => route('bars.show', $bar),
                ]);

            if ($request->header('X-Inertia')) {
                return Inertia::location($checkoutResponse->url);
            }

            return $checkoutResponse;
        } catch (\Throwable $e) {
            Log::error('Stripe checkout failed', [
                'bar_id' => $bar->id,
                'user_id' => $billingUser->id,
                'message' => $e->getMessage(),
            ]);

            abort(500, 'Stripe checkout failed.');
        }
    }

    public function portal(Request $request, Bar $bar)
    {
        $this->authorize('update', $bar);

        $billingUser = $bar->billingUser();
        if (! $billingUser) {
            abort(404);
        }

        if (! $request->user()->is_admin && $request->user()->id !== $billingUser->id) {
            abort(403);
        }

        if (! config('cashier.secret')) {
            Log::error('Stripe secret not configured');
            abort(500, 'Stripe secret not configured.');
        }

        return redirect()->away(
            $billingUser->billingPortalUrl(route('bars.show', $bar))
        );
    }
}

