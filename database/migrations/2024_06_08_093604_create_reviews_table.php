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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('rating');
            $table->string('comment');
            $table->tinyInteger('helpful')->default(0);
            $table->tinyInteger('thanks')->default(0);
            $table->tinyInteger('love_this')->default(0);
            $table->tinyInteger('oh_no')->default(0);
            $table->morphs('reviewable');
            $table->morphs('author');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
