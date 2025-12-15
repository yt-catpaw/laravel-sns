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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('tweet', 255);
            $table->string('image_path')->nullable();
            $table->foreignId('user_id')
                ->constrained((new User)->getTable())
                ->cascadeOnDelete();
        });
    }
};
