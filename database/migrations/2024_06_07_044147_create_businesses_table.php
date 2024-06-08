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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image_url');
            $table->string('url');
            $table->string('phone', 20);
            $table->string('phone_country_code', 5);

            // Address
            $table->text('address1');
            $table->text('address2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('zip_code', 10);
            $table->string('country', 2);
            $table->geometry('coordinates', subtype: 'point', srid: 4326);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
