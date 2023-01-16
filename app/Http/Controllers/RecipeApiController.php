<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\CookingStep;
use App\Models\IngredientRecipe;
use App\Models\Ingredient;
use App\Models\RecipeCategory;
use App\Models\FavouriteRecipe;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecipeApiController extends Controller
{
    public function addRecipe(Request $req){
        DB::transaction(function() use ($req){
            $id = User::where('api_token', '=', $req->token)->first()->id;
            
            $recipeId = Recipe::insertGetId(['name' => $req->name, 'userId' => $id]);

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
            
        if ($req->image) {
            Storage::put('images/' . $recipeId . '.txt', $req->image);
        }
        });

        return Response(['message' => 'Successfully added new recipe']);
    }

    public function getAllRecipes(Request $req){
        return Recipe::paginate(1);
    }

    public function getUserRecipes(Request $req){
        $token = $req->token;

        $userId = User::where('api_token', '=', $token)->first()->id;

        return Recipe::where('userId', '=', $userId)->get();
    }

    public function getSingleRecipe(Request $req){
        if($req->token){
            $userId = User::where('api_token', '=', $req->token)->first()->id;
        }
        $recipe = Recipe::where('recipeId', '=', $req->id)->first(); //returns recipe with given id
        $author = User::where('id', '=', $recipe->userId)->first(); //returns user that added that recipe
        $cookingSteps = CookingStep::where('recipeId', '=', $req->id)->get(); //return cooking steps
        $ingredientsRecipesIds = IngredientRecipe::where('recipeId', '=', $req->id)->get()->pluck('ingredientId'); //returns ids of all needed ingredients
        $ingredientsRecipes = IngredientRecipe::where('recipeId', '=', $req->id)->get(); //returns all needed ingredients and its amount
        $ingredients = Ingredient::select([
            "ingredients.ingredientId", 
            "ingredients.name", 
            "ingredients_recipes.amount", 
            "units.name as unit", 
            "ingredients_recipes.recipeId"
            ])
        ->whereIn('ingredients.ingredientId', $ingredientsRecipesIds)
        ->where('ingredients_recipes.recipeId', '=', $req->id)
        ->join("ingredients_recipes", "ingredients.ingredientId", "=", "ingredients_recipes.ingredientId")
        ->join("units", "ingredients_recipes.unitId", "=", "units.id")
        ->get(); //joins ingredients and ingredients_recipes and returns ingredients name, id, recipeId, amount
        
        $favourite = false;

        if($req->token){
            if(FavouriteRecipe::where('userId', '=', $userId)->where('recipeId', '=', $req->id)->first()){
                $favourite = true;
            }
        }

        if(Storage::get('images/' . $req->id . '.txt')) {
            $img = Storage::get('images/' . $req->id . '.txt');
        } else {
            $img = Storage::get('images/no_image_available.txt');
        }

        return Response([
            "recipe" => $recipe, 
            "cookingSteps" => $cookingSteps, 
            "ingredients" => $ingredients, 
            "author" => $author,
            "image" => $img,
            "favourite" => $favourite
        ]);
    }

    public function getSingleRecipeImage($id){
        if(Storage::get('images/' . $id . '.txt')){
            return Response(["image" => Storage::get('images/' . $id . '.txt')]);
        } else {
            return Response(["image" => Storage::get('images/no_image_available.txt')]);
        }
        
    }

    public function addRecipeToFavourite(Request $req){
        $userId = User::where('api_token', '=', $req->token)->first()->id;
        $recipeId = $req->recipeId;

        if(FavouriteRecipe::firstOrCreate(['userId'=>$userId, 'recipeId'=>$recipeId])){
            return Response(["message" => "Successfully added to favourites"]);
        } else {
            return Response(["message" => "Failed to add to favourites"]);
        }
    }

    public function removeFromFavourite(Request $req){
        $userId = User::where('api_token', '=', $req->token)->first()->id;

        if(FavouriteRecipe::where('userId', '=', $userId)->where('recipeId', '=', $req->recipeId)->forceDelete()){
            return Response(["message" => "Successfully removed from favourites"]);
        } else {
            return Response(["message" => "Failed to remove from favourites"]);
        }
    }

    public function getFavouriteRecipes(Request $req){
        $userId = User::where('api_token', '=', $req->token)->first()->id;
        $favIds = FavouriteRecipe::where('userId', '=', $userId)->pluck('recipeId');

        return Recipe::whereIn('recipeId', $favIds)->get();
    }

    public function getRecipesSearch(Request $req){
        return Recipe::where('name', 'LIKE', '%'.$req->searchString.'%')->paginate(1);
    }
}
