<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingredients_recipes', function (Blueprint $table) {
            $table->foreignId("ingredientId")->references('ingredientId')->on('ingredients')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId("recipeId")->references('recipeId')->on('recipes')->onUpdate('cascade')->onDelete('cascade');
            $table->string("amount");
            $table->foreignId("unitId")->references('id')->on('units')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['ingredientId', 'recipeId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingredients_recipes');
    }
};
