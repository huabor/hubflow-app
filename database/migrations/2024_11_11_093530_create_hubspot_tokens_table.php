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
        Schema::create('hubspot_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');

            $table->string('token');
            $table->string('refresh_token');

            $table->integer('hubspot_user_id');
            $table->string('email')->nullable();
            $table->integer('hub_id');
            $table->string('hub_domain');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubspot_tokens');
    }
};
