<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\UserType;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->timestamps();
        });

        $data = array(
            ['name' => 'user'],
            ['name' => 'admin'],
        );

        foreach($data as $row) {
            $user_type = new UserType();
            $user_type->name = $row['name'];
            $user_type->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_type');
    }
};
