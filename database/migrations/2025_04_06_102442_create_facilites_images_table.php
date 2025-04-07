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
        Schema::create('facilites_images', function (Blueprint $table) {
            $table->id();
            $table->string('facilitesimage_title', 255);
            $table->string('facilites_image', 255)->nullable();
            $table->unsignedBigInteger('facilites_details_id');
            $table->foreign('facilites_details_id')->references('id')->on('facilites_details')->onDelete('cascade');
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilites_images');
    }
};
