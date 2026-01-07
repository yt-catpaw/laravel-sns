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

    public function confirm(Request $request)
    {
        $planType = $request->input('plan_type');
        $cardName = $request->input('card_name');
        $cardNumber = $request->input('card_number');
        $exp = $request->input('exp');

        $plans = $this->plans();
        $selectedPlan = collect($plans)->firstWhere('type', $planType) ?? $plans[0];

        $cardLast4 = substr(preg_replace('/\D/', '', (string) $cardNumber), -4);

        return view('payment.confirm', [
            'plans' => $plans,
            'selectedPlan' => $selectedPlan,
            'cardName' => $cardName,
            'cardLast4' => $cardLast4 ? '**** **** **** ' . $cardLast4 : '入力なし',
            'exp' => $exp,
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
            ]);

            return response()->json(['clientSecret' => $intent->client_secret]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
