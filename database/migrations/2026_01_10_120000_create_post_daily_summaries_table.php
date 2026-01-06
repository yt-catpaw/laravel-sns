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
        Schema::create('post_daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->date('date')->comment('集計対象日');
            $table->unsignedInteger('posts_count')->default(0);
            $table->unsignedInteger('likes_received')->default(0);
            $table->unsignedInteger('comments_received')->default(0);

            $table->foreignId('user_id')
                ->constrained((new User)->getTable())
                ->cascadeOnDelete();

            $table->unique(['user_id', 'date']);
            $table->index(['date', 'user_id']);
        });
    }
};
