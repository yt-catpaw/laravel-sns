<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function payment_intent_succeededで決済レコードを作成する(): void
    {
        $secret = 'whsec_test_123';
        config(['services.stripe.webhook_secret' => $secret]);

        $user = User::factory()->create();

        $payload = $this->buildPayload(
            type: 'payment_intent.succeeded',
            paymentIntentId: 'pi_test_success',
            userId: $user->id,
            planType: 'one_time',
            status: 'succeeded'
        );

        $response = $this->postWebhook($payload, $secret);

        $response->assertOk();
        $this->assertDatabaseHas('payments', [
            'stripe_payment_intent_id' => 'pi_test_success',
            'user_id' => $user->id,
            'amount' => 3000,
            'currency' => 'jpy',
            'status' => 'succeeded',
            'plan_type' => 'one_time',
        ]);
    }

    #[Test]
    public function payment_intent_failedで決済レコードを作成する(): void
    {
        $secret = 'whsec_test_123';
        config(['services.stripe.webhook_secret' => $secret]);

        $user = User::factory()->create();

        $payload = $this->buildPayload(
            type: 'payment_intent.payment_failed',
            paymentIntentId: 'pi_test_failed',
            userId: $user->id,
            planType: 'subscription',
            status: 'failed'
        );

        $response = $this->postWebhook($payload, $secret);

        $response->assertOk();
        $this->assertDatabaseHas('payments', [
            'stripe_payment_intent_id' => 'pi_test_failed',
            'user_id' => $user->id,
            'amount' => 3000,
            'currency' => 'jpy',
            'status' => 'failed',
            'plan_type' => 'subscription',
        ]);
    }

    private function postWebhook(string $payload, string $secret)
    {
        $timestamp = time();
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);
        $header = "t={$timestamp},v1={$signature}";

        return $this->call(
            'POST',
            '/api/stripe/webhook',
            [],
            [],
            [],
            [
                'HTTP_STRIPE_SIGNATURE' => $header,
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload
        );
    }

    private function buildPayload(
        string $type,
        string $paymentIntentId,
        int $userId,
        string $planType,
        string $status
    ): string {
        $payload = [
            'id' => 'evt_test_webhook',
            'object' => 'event',
            'api_version' => '2023-10-16',
            'created' => time(),
            'data' => [
                'object' => [
                    'id' => $paymentIntentId,
                    'object' => 'payment_intent',
                    'amount' => 3000,
                    'currency' => 'jpy',
                    'status' => $status,
                    'metadata' => [
                        'user_id' => (string) $userId,
                        'plan_type' => $planType,
                    ],
                ],
            ],
            'livemode' => false,
            'type' => $type,
        ];

        return json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}
