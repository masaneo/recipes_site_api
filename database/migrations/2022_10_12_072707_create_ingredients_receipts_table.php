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
        Schema::create('ingredients_receipts', function (Blueprint $table) {
            $table->integer("ingredientId");
            $table->integer("receiptId");
            $table->string("amount");
            $table->timestamps();

            $table->primary(['ingredientId', 'receiptId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingredients_receipts');
    }
};
