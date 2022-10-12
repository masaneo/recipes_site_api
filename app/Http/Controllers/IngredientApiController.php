<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;

class IngredientApiController extends Controller
{
    public function addIngredient(Request $req){
        if(!Ingredient::where('name', '=', $req->name)->first()){
            Ingredient::create(['name' => $req->name]);
            return Response(['message' => 'Successfully added new ingredient']);
        }

        return Response(['message' => 'Ingredient already exists in database']);
    }

    public function getIngredients(Request $req){
        return Ingredient::all();
    }
}
