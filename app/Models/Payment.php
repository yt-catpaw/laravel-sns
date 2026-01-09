<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $user_id
 * @property string $stripe_payment_intent_id
 * @property int $amount
 * @property string $currency
 * @property string $status
 * @property string|null $plan_type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\Payment query()
 * @mixin \Eloquent
 */
class Payment extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'stripe_payment_intent_id',
        'amount',
        'currency',
        'status',
        'plan_type',
    ];
}
