<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Post;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_views', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('post_id')
                ->constrained((new Post)->getTable())
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained((new User)->getTable())
                ->cascadeOnDelete();

            $table->string('session_token', 120)->nullable();

            $table->index(['post_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('session_token');
        });
    }
};
