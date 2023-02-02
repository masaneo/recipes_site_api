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
        Schema::create('votes', function (Blueprint $table) {
            $table->foreignId("userId")->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId("recipeId")->references('recipeId')->on('recipes')->onUpdate('cascade')->onDelete('cascade');
            $table->float("vote");
            $table->timestamps();

            $table->primary(['userId', 'recipeId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('votes');
    }
};
