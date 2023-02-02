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
        Schema::create('recipes_categories', function (Blueprint $table) {
            $table->foreignId("categoryId")->references('categoryId')->on('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId("recipeId")->references('recipeId')->on('recipes')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['categoryId', 'recipeId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipes_categories');
    }
};
