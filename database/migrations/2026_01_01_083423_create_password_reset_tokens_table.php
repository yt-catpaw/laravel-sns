<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('email', 254);
            $table->string('token', 64);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();

            $table->index('email');
            $table->unique('token');
        });
    }

};
