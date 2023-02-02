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
        Schema::create('cooking_steps', function (Blueprint $table) {
            $table->unsignedBigInteger("stepId");
            $table->foreignId("recipeId")->references('recipeId')->on('recipes')->onUpdate('cascade')->onDelete('cascade');
            $table->string("step");
            $table->timestamps();

            $table->primary(['stepId', 'recipeId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cooking_steps');
    }
};
