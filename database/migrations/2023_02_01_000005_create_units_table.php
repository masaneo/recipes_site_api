<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Unit;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->timestamps();
        });

        $data = array(
            ['name' => 'sztuka'],
            ['name' => 'plaster'],
            ['name' => 'gram'],
            ['name' => 'dekagram'],
            ['name' => 'kilogram'],
            ['name' => 'mililitr'],
            ['name' => 'litr'],
            ['name' => 'szczypta'],
            ['name' => 'łyżka'],
            ['name' => 'szklanka'],
            ['name' => 'pęczek'],
            ['name' => 'opakowanie'],
            ['name' => 'łyżeczka'],
            ['name' => 'ziarno'],
            ['name' => 'słoik'],
            ['name' => 'puszka'],
            ['name' => 'listek'],
            ['name' => 'garść'],
            ['name' => 'cm'],
        );

        foreach($data as $row) {
            $unit = new Unit();
            $unit->name = $row['name'];
            $unit->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('units');
    }
};
