<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\CookingStep;
use App\Models\IngredientRecipe;
use App\Models\RecipeCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RecipeApiController extends Controller
{
    public function addRecipe(Request $req){
        DB::transaction(function() use ($req){
            //$id = User::where('api_token', '=', $req->token)->first()->id;
            
            $id = 1; // for now, delete this later

            $recipeId = Recipe::create(['name' => $req->name, 'userId' => $id])->id;

            foreach($req->steps as $number => $step){
                CookingStep::create(['stepId' => $number, 'recipeId' => $recipeId, 'step' => $step]);
            }
            foreach($req->categories as $category){
                ReceiptCategory::create(['categoryId' => $category, 'recipeId' => $recipeId]);
            }
            foreach($req->ingredients as $ingredient){
                IngredientReceipt::create(['ingredientId' => $ingredient['ingredientId'], 'recipeId' => $recipeId, 'amount' => $ingredient['amount']]);
            }
        });

        return Response(['message' => 'Successfully added new receipt']);
    }

    public function getAllRecipes(Request $req){
        return Recipe::all();
    }
}
