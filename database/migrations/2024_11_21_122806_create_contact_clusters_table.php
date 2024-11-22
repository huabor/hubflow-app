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
        Schema::create('contact_clusters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id');

            $table->tinyInteger('type');
            $table->string('name');
            $table->string('color')->default('#5e93d2');
            $table->json('filter')->default('[]');

            $table->tinyInteger('refresh_status')->default(0);
            $table->timestamp('refreshed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_clusters');
    }
};
