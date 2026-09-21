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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 255)->default('');
            $table->string('locale', 10)->default('lv');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('body_class')->nullable();
            $table->mediumText('header_html')->nullable();
            $table->mediumText('content_html');
            $table->mediumText('footer_html')->nullable();
            $table->timestamps();

            $table->unique(['slug', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
