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
        Schema::create('facilites_details', function (Blueprint $table) {
            $table->id();
            $table->string('facilites_title', 255);
            $table->text('facilites_description')->nullable();
            $table->unsignedBigInteger('facilites_id');
            $table->foreign('facilites_id')->references('id')->on('facilites')->onDelete('cascade');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilites_details');
    }
};
