<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained((new User)->getTable())
                ->nullOnDelete();
            $table->string('stripe_payment_intent_id')->unique();
            $table->integer('amount');
            $table->string('currency', 10);
            $table->string('status');
            $table->string('plan_type')->nullable();
        });
    }
};
