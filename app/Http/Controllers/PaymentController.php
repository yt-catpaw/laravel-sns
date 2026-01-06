<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    private function plans(): array
    {
        return [
            [
                'type' => 'one_time',
                'title' => '単発購入',
                'price' => '¥3,000',
                'detail' => '1回きりの決済。追加料金なし。',
            ],
            [
                'type' => 'subscription',
                'title' => '月額サブスク',
                'price' => '¥1,200 / 月',
                'detail' => '毎月自動更新。いつでも解約可能。',
            ],
        ];
    }
}
