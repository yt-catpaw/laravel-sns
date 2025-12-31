<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Comment;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('post_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained((new User)->getTable())
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained((new Comment)->getTable())
                ->cascadeOnDelete();

            $table->string('body', 280);

            $table->index(['post_id', 'parent_id', 'created_at']);
            $table->index('parent_id');
        });
    }
};
