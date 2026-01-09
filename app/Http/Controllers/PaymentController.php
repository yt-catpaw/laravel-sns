<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payment.index', [
            'plans' => $this->plans(),
        ]);
    }

    public function createIntent(Request $request)
    {
        $planType = $request->input('plan_type', 'one_time');
        $plan = collect($this->plans())->firstWhere('type', $planType);

        if (!$plan || !isset($plan['amount'])) {
            return response()->json(['error' => 'unsupported plan'], 422);
        }

        Stripe::setApiKey(config('services.stripe.secret', env('STRIPE_SECRET')));

        try {
            $intent = PaymentIntent::create([
                'amount' => $plan['amount'],
                'currency' => 'jpy',
                'payment_method_types' => ['card'],
                'description' => $plan['title'],
                'metadata' => [
                    'user_id' => (string) optional($request->user())->id,
                    'plan_type' => $planType,
                ],
            ]);

            return response()->json(['clientSecret' => $intent->client_secret]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function complete(Request $request)
    {
        $status = $request->query('redirect_status');
        $message = '決済結果を確認してください。';

        switch ($status) {
            case 'succeeded':
                $message = '支払いが完了しました。';
                break;
            case 'processing':
                $message = '支払いを処理しています。反映までお待ちください。';
                break;
            case 'failed':
                $message = '支払いに失敗しました。もう一度お試しください。';
                break;
        }

        return view('payment.complete', [
            'status' => $status,
            'message' => $message,
        ]);
    }

    private function plans(): array
    {
        return [
            [
                'type' => 'one_time',
                'title' => '単発購入',
                'price' => '¥3,000',
                'amount' => 3000,
                'detail' => '1回きりの決済。追加料金なし。',
            ],
            [
                'type' => 'subscription',
                'title' => '月額サブスク',
                'price' => '¥1,200 / 月',
                'amount' => 1200,
                'detail' => '毎月自動更新。いつでも解約可能。',
            ],
        ];
    }
}
