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
        Schema::create('event_videos', function (Blueprint $table) {
            $table->id();
            $table->string('image_name',255);
            $table->string('image_path',255)->nullable();
            $table->string('url',255)->nullable();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_videos');
    }
};
