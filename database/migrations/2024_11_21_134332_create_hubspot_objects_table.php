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
        Schema::create('hubspot_objects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('hub_id');

            $table->tinyInteger('type');
            $table->string('hubspot_id')->index();

            $table->json('properties')->default('{}');
            $table->magellanPoint('location')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubspot_objects');
    }
};
