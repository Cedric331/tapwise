<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class StripeWebhookController extends CashierController
{
    public function handleWebhook(Request $request)
    {
        $webhookSecret = config('cashier.webhook.secret');
        if (empty($webhookSecret)) {
            Log::error('Stripe webhook secret not configured');

            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        Log::info('Stripe webhook received', [
            'method' => $request->method(),
            'path' => $request->path(),
            'has_signature' => $request->hasHeader('Stripe-Signature'),
        ]);

        try {
            return parent::handleWebhook($request);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json(['error' => $e->getMessage()], 200);
        }
    }

    protected function handleInvoicePaymentFailed(array $payload)
    {
        $invoice = $payload['data']['object'];
        $stripeCustomerId = $invoice['customer'] ?? null;

        if ($stripeCustomerId) {
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if ($user) {
                Log::warning('Payment failed for user', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'invoice_id' => $invoice['id'] ?? null,
                    'amount' => isset($invoice['amount_due']) ? $invoice['amount_due'] / 100 : null,
                ]);
            }
        }

        return parent::handleInvoicePaymentFailed($payload);
    }

    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        $subscription = $payload['data']['object'];
        $stripeCustomerId = $subscription['customer'] ?? null;

        if ($stripeCustomerId) {
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if ($user) {
                Log::info('Subscription deleted for user', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'subscription_id' => $subscription['id'] ?? null,
                ]);
            }
        }

        return parent::handleCustomerSubscriptionDeleted($payload);
    }

    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        $invoice = $payload['data']['object'];
        $stripeCustomerId = $invoice['customer'] ?? null;

        if ($stripeCustomerId) {
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if ($user) {
                Log::info('Subscription invoice paid', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'invoice_id' => $invoice['id'] ?? null,
                    'billing_reason' => $invoice['billing_reason'] ?? null,
                ]);
            }
        }

        return parent::handleInvoicePaymentSucceeded($payload);
    }
}

