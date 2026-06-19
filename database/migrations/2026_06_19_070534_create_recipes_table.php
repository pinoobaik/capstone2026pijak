<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('recipe_id_json', 100)->nullable();
            $table->string('recipe_name');
            $table->text('description')->nullable();
            $table->jsonb('ingredients');
            $table->jsonb('steps');
            $table->string('category', 100)->nullable();
            $table->string('difficulty', 50)->nullable();
            $table->string('prep_time', 50)->nullable();
            $table->string('cook_time', 50)->nullable();
            $table->integer('serves')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};