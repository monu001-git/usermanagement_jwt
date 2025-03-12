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
        Schema::create('notice_boards', function (Blueprint $table) {
            $table->id();
            $table->string('title',255)->nullable();
            $table->date('date',255)->nullable();
            $table->string('pdf', 255)->nullable();
            $table->string('publiser',255)->nullable()->default(null);
            $table->enum('link_type', ['internal', 'external'])->nullable()->default(null);
            $table->integer('order')->nullable()->default(0);
            $table->boolean('status')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notice_boards');
    }
};
