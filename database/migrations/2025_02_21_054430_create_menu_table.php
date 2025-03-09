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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name',150); 
            $table->string('url',255)->nullable()->default(null); 
            $table->string('slug',255)->nullable();
            $table->integer('content_id')->nullable()->default(1); 
            $table->unsignedBigInteger('parent_id')->nullable(); 
            $table->enum('menu_place', ['header', 'footer', 'both'])->nullable()->default(null);
            $table->enum('link_type', ['internal', 'external'])->nullable()->default(null);
            $table->integer('order')->nullable()->default(0);
            $table->boolean('status')->default(0)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
