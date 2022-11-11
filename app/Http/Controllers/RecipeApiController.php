<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\CookingStep;
use App\Models\IngredientRecipe;
use App\Models\Ingredient;
use App\Models\RecipeCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RecipeApiController extends Controller
{
    public function addRecipe(Request $req){
        DB::transaction(function() use ($req){
            $id = User::where('api_token', '=', $req->token)->first()->id;
            
            $recipeId = Recipe::create(['name' => $req->name, 'userId' => $id])->id;

            foreach($req->steps as $number => $step){
                if($step['step'] !== null){
                    CookingStep::create(['stepId' => $number, 'recipeId' => $recipeId, 'step' => $step['step']]);
                }
            }
            foreach($req->categories as $category){
                if($category['categoryId'] !== null){
                    RecipeCategory::create(['categoryId' => $category['categoryId'], 'recipeId' => $recipeId]);
                }
            }
            foreach($req->ingredients as $ingredient){
                if($ingredient['ingredient'] !== null){
                    $ing = Ingredient::where('name', '=', $ingredient['ingredient'])->first();
                    if($ing !== null) {
                        $ing = $ing->ingredientId;
                    } else {
                        $ing = Ingredient::insertGetId(['name' => $ingredient['ingredient']]);
                    }
                    IngredientRecipe::create([
                        'ingredientId' => $ing, 
                        'recipeId' => $recipeId, 
                        'amount' => $ingredient['quantity'], 
                        'unitId' => $ingredient['unit']
                    ]);
                }
                
            }
        });

        return Response(['message' => 'Successfully added new recipe']);
    }

    public function getAllRecipes(Request $req){
        return Recipe::all()->toJson();
    }

    public function getSingleRecipe(Request $req){
        $recipe = Recipe::where('recipeId', '=', $req->id)->first(); //returns recipe with given id
        $author = User::where('id', '=', $recipe->userId)->first(); //returns user that added that recipe
        $cookingSteps = CookingStep::where('recipeId', '=', $req->id)->get(); //return cooking steps
        $ingredientsRecipesIds = IngredientRecipe::where('recipeId', '=', $req->id)->get()->pluck('ingredientId'); //returns ids of all needed ingredients
        $ingredientsRecipes = IngredientRecipe::where('recipeId', '=', $req->id)->get(); //returns all needed ingredients and its amount
        $ingredients = Ingredient::select(["ingredients.ingredientId", "ingredients.name", "ingredients_recipes.amount", "ingredients_recipes.recipeId"])
        ->whereIn('ingredients.ingredientId', $ingredientsRecipesIds)
        ->where('ingredients_recipes.recipeId', '=', $req->id)
        ->join("ingredients_recipes", "ingredients.ingredientId", "=", "ingredients_recipes.ingredientId")
        ->get(); //joins ingredients and ingredients_recipes and returns ingredients name, id, recipeId, amount

        return Response([
            "recipe" => $recipe, 
            "cookingSteps" => $cookingSteps, 
            "ingredients" => $ingredients, 
            "author" => $author
        ]);
    }
}
