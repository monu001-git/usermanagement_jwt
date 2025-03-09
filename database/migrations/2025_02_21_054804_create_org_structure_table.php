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
        Schema::create('org_structures', function (Blueprint $table) {
            $table->id();
        //seo    
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keyword')->nullable();
            $table->text('body_script')->nullable();
            $table->text('head_script')->nullable();

        //common detail           
            $table->string('name',255);
            $table->string('email',255)->unique()->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('header_logo', 255)->nullable();
            $table->string('header_logo_title', 255)->nullable();
            $table->string('footer_logo', 255)->nullable();
            $table->string('footer_logo_title', 255)->nullable();
            $table->string('favicon', 255)->nullable();
            $table->string('favicon_title', 255)->nullable();
            $table->text('address')->nullable();

        //socal media
            $table->string('instagram', 255)->nullable();
            $table->string('instagram_title', 255)->nullable();
            $table->string('facebook', 255)->nullable();
            $table->string('facebook_title', 255)->nullable();
            $table->string('youtube', 255)->nullable();
            $table->string('youtube_title', 255)->nullable();
          
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('org_structures');
    }
};
