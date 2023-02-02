<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id("categoryId");
            $table->string("name");
            $table->timestamps();
        });

        // $data = array(
        //     ['name' => 'śniadanie'],
        //     ['name' => 'drugie śniadanie'],
        //     ['name' => 'przekąska'],
        //     ['name' => 'obiad'],
        //     ['name' => 'przystawka'],
        //     ['name' => 'deser'],
        //     ['name' => 'kolacja'],
        // );
        
        $data = array(
            ['name' => 'obiad'],
            ['name' => 'śniadanie'],
            ['name' => 'kolacja'],
            ['name' => 'przystawka'],
            ['name' => 'przekąska'],
            ['name' => 'deser'],
            ['name' => 'drugie śniadanie'],
        );

        foreach($data as $row) {
            $category = new Category();
            $category->name = $row['name'];
            $category->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
