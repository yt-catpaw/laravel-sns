<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeObject;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $secret = config('services.stripe.webhook_secret');
        if (!$secret) {
            return response()->json(['error' => 'Webhook secret not configured.'], 500);
        }

        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (!$signature) {
            return response()->json(['error' => 'Missing signature.'], 400);
        }

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload.'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature.'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $metadata = $this->normalizeMetadata($paymentIntent->metadata ?? null);
                $userId = $this->normalizeUserId($metadata['user_id'] ?? null);
                $planType = $metadata['plan_type'] ?? null;

                if (!empty($paymentIntent->id)) {
                    Payment::updateOrCreate(
                        ['stripe_payment_intent_id' => $paymentIntent->id],
                        [
                            'user_id' => $userId,
                            'amount' => $paymentIntent->amount ?? 0,
                            'currency' => $paymentIntent->currency ?? 'jpy',
                            'status' => $paymentIntent->status ?? 'succeeded',
                            'plan_type' => $planType,
                        ]
                    );
                }
                Log::info('Stripe payment_intent.succeeded', [
                    'id' => $paymentIntent->id ?? null,
                    'amount' => $paymentIntent->amount ?? null,
                    'currency' => $paymentIntent->currency ?? null,
                    'metadata' => $metadata,
                ]);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $metadata = $this->normalizeMetadata($paymentIntent->metadata ?? null);
                $userId = $this->normalizeUserId($metadata['user_id'] ?? null);
                $planType = $metadata['plan_type'] ?? null;

                if (!empty($paymentIntent->id)) {
                    Payment::updateOrCreate(
                        ['stripe_payment_intent_id' => $paymentIntent->id],
                        [
                            'user_id' => $userId,
                            'amount' => $paymentIntent->amount ?? 0,
                            'currency' => $paymentIntent->currency ?? 'jpy',
                            'status' => $paymentIntent->status ?? 'failed',
                            'plan_type' => $planType,
                        ]
                    );
                }
                Log::warning('Stripe payment_intent.payment_failed', [
                    'id' => $paymentIntent->id ?? null,
                    'amount' => $paymentIntent->amount ?? null,
                    'currency' => $paymentIntent->currency ?? null,
                    'metadata' => $metadata,
                    'last_payment_error' => $paymentIntent->last_payment_error->message ?? null,
                ]);
                break;
            default:
                Log::info('Stripe webhook received', [
                    'type' => $event->type,
                ]);
        }

        return response()->json(['received' => true]);
    }

    private function normalizeMetadata($metadata): array
    {
        if ($metadata instanceof StripeObject) {
            return $metadata->toArray();
        }
        if (is_array($metadata)) {
            return $metadata;
        }

        return [];
    }

    private function normalizeUserId($userId): ?int
    {
        if ($userId === null || $userId === '') {
            return null;
        }
        if (!is_numeric($userId)) {
            return null;
        }

        return (int) $userId;
    }
}
