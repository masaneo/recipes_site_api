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
use App\Models\Unit;
use App\Models\Vote;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mail;

class RecipeApiController extends Controller
{
    public function addRecipe(Request $req){
        DB::transaction(function() use ($req){
            $id = User::where('api_token', '=', $req->token)->first()->id;
            
            $recipeId = Recipe::insertGetId(['name' => $req->name, 'userId' => $id, 'created_at' => now(), 'updated_at' => now()]);

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
                        $ing = Ingredient::insertGetId(['name' => $ingredient['ingredient'], 'created_at' => now(), 'updated_at' => now()]);
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
        return Recipe::paginate(12);
    }

    public function getUserRecipes(Request $req){
        $token = $req->token;

        $userId = User::where('api_token', '=', $token)->first()->id;

        return Recipe::where('userId', '=', $userId)->paginate(12);
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
        $category = RecipeCategory::where('recipeId', '=', $req->id)->first();
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
            "favourite" => $favourite,
            "category" => $category,
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

        return Recipe::whereIn('recipeId', $favIds)->paginate(12);
    }

    public function getRecipesSearch(Request $req){
        return Recipe::where('name', 'LIKE', '%'.$req->searchString.'%')->paginate(12);
    }
    
    public function findRecipesWithIngredients(Request $req){
        $ingredients = $this->stringToArray($req->ingredientList);
        // $ingredients = $this->get_array_combination($ingredients);
        
        $ids = Ingredient::whereIn('name', $ingredients)->pluck('ingredientId');

        $recIds = IngredientRecipe::select('recipeId')->whereIn('ingredientId', $ids)->groupBy('recipeId')->pluck('recipeId');
        $results = Recipe::whereIn('recipeId', $recIds)->get();

        foreach($results as $result) {
            $result['image'] = Storage::get('images/' . $result['recipeId'] . '.txt') ? Storage::get('images/' . $result['recipeId'] . '.txt') : Storage::get('images/no_image_available.txt');
        }

        return $results;
    }

    function stringToArray($stringSeperatedCommas){
        return collect(explode(',', $stringSeperatedCommas))->map(function ($string) {
            return trim($string) != null ? trim($string) : null;
        })->filter(function ($string) {
            return trim($string) != null;
        });
    }

    // function get_array_combination($arr) {
    //     $results = array(array());
    
    //     foreach ($arr as $values)
    //         foreach ($results as $combination)
    //                 array_push($results, array_merge($combination, array($values))); // Get new values and merge to your previous combination. And push it to your results array
        
        
    //      usort($results, function($a, $b) {
    //         return count($b) - count($a);
    //      });

    //      return $results;
    // }

    public function getAllIngredientsByRecipeId(Request $req) {
        $result = IngredientRecipe::select('ingredients.ingredientId', 'ingredients.name', 'ingredients_recipes.amount', 'units.name as unit')
        ->join('ingredients', 'ingredients_recipes.ingredientId', '=', 'ingredients.ingredientId')
        ->join('units', 'ingredients_recipes.unitId', '=', 'units.id')
        ->where('ingredients_recipes.recipeId', '=', $req->recipeId)->get();

        return $result;
    }

    public function getRecipeDataToEdit(Request $req) {
        $recipe =  Recipe::where('recipeId', '=', $req->id)->first();
        $user = User::where('api_token', '=', $req->token)->first();

        if($recipe->userId == $user->id){
            $result = $this->getSingleRecipe($req);
            return $result;
        } else {
            return Response(["message" => "nie zgadza się"]);
        }
    }

    public function updateRecipe(Request $req) {
        $recipe = Recipe::where('recipeId', '=', $req->id)->first();
        $user = User::where('api_token', '=', $req->token)->first();

        if($recipe->userId == $user->id){
            $recipe->updated_at = now();
            $recipe->save();
            if($recipe->name != $req->name){
                $recipe->name = $req->name;
                $recipe->save();
            }
            foreach($req->steps as $step){
                if($step['step'] !== null) {
                    CookingStep::updateOrCreate([
                        'stepId' => $step['id'],
                        'recipeId' => $req->id
                    ],['step' => $step['step']]);
                }
            }
            foreach($req->ingredients as $ingredient){
                if($ingredient !== null && $ingredient['ingredient'] !== null){
                    $ing = Ingredient::where('name', '=', $ingredient['ingredient'])->first();
                    if($ing !== null) {
                        $ing = $ing->ingredientId;
                    } else {
                        $ing = Ingredient::insertGetId(['name' => $ingredient['ingredient'], 'created_at' => now(), 'updated_at' => now()]);
                    }
                    $unit = Unit::where('name', '=', $ingredient['unit'])->first();
                    if($ingredient['id'] != $ing) {
                        $delIng = IngredientRecipe::where('ingredientId', '=', $ingredient['id'])->where('recipeId', '=', $recipe->recipeId)->first();
                        $delIng->forceDelete();
                    }
                    IngredientRecipe::updateOrCreate([
                        'ingredientId' => $ing, 
                        'recipeId' => $recipe->recipeId
                    ],[
                        'amount' => $ingredient['quantity'], 
                        'unitId' => $unit->id
                    ]);
                }    
            }
            foreach($req->categories as $category){
                if($category['categoryId'] !== null){
                    RecipeCategory::updateOrCreate(['categoryId' => $category['categoryId'], 'recipeId' => $recipe->recipeId]);
                }
            }
            if ($req->image) {
                Storage::put('images/' . $req->recipeId . '.txt', $req->image);
            }
        } else {
            return Response(['message' => 'Coś poszło nie tak.']);
        }
    }

    public function deleteIngredientFromRecipe(Request $req){
        $user = User::where('api_token', '=', $req->token)->first();
        $recipe = Recipe::where('recipeId', '=', $req->recipeId)->first();

        if($user->id === $recipe->userId){
            $recipe->updated_at = now();
            $recipe->save();
            $ingredientRecipe = IngredientRecipe::where('recipeId', '=', $req->recipeId)->where('ingredientId', '=', $req->ingredientId)->first();
            if($ingredientRecipe) {
                $ingredientRecipe->forceDelete();
                return Response(["message" => "Usunięto"]);
            } else {
                return Response(["message" => "Nie znaleziono takiego składnika w bazie"]);
            }
        }
    }

    public function deleteStepFromRecipe(Request $req){
        $user = User::where('api_token', '=', $req->token)->first();
        $recipe = Recipe::where('recipeId', '=', $req->recipeId)->first();

        if($user->id === $recipe->userId){
            $recipe->updated_at = now();
            $recipe->save();
            $cookingStep = CookingStep::where('recipeId', '=', $req->recipeId)->where('stepId', '=', $req->stepId)->first();
            if($cookingStep) {
                $cookingStep->forceDelete();
                return Response(["message" => "Usunięto"]);
            } else {
                return Response(["message" => "Nie znaleziono takiego kroku w bazie"]);
            }
        }
    }

    public function deleteRecipe(Request $req) {
        $user = User::where('api_token', '=', $req->token)->first();
        $recipe = Recipe::where('recipeId', '=', $req->recipeId)->first();

        if($user->id === $recipe->userId){
            Recipe::where('recipeId', '=', $req->recipeId)->forceDelete();
            IngredientRecipe::where('recipeId', '=', $req->recipeId)->forceDelete();
            CookingStep::where('recipeId', '=', $req->recipeId)->forceDelete();
            RecipeCategory::where('recipeId', '=', $req->recipeId)->forceDelete();
            FavouriteRecipe::where('recipeId', '=', $req->recipeId)->forceDelete();
            Vote::where('recipeId', '=', $req->recipeId)->forceDelete();

            return Response(["message" => "Usunięto przepis"]);
        } else {
            return Response(["message" => "Nie znaleziono przepisu"]);
        }
    }

    public function getRecipesByCategory(Request $req) {
        $recipes = RecipeCategory::where('categoryId', '=', $req->categoryId)->pluck('recipeId');
        return Response([
            "recipes" => Recipe::whereIn('recipeId', $recipes)->paginate(12),
            "categoryName" => Category::where("categoryId", '=', $req->categoryId)->pluck('name')
        ]);
    }

    public function getRecipesForAdmin(Request $req) {
        $user = User::where("api_token", '=', $req->token)->first();
        $recipes = [];
        $isAdmin = $user->user_type == 1 ? true : false;

        if($isAdmin) {
            $recipes = Recipe::orderByDesc("updated_at")->paginate(12);
        }

        return Response([
            "isAdmin" => $isAdmin,
            "recipes" => $recipes,
        ]);
    }

    public function getRecipeDataAdmin(Request $req) {
        $user = User::where("api_token", '=', $req->token)->first();
        $isAdmin = $user->user_type == 1 ? true : false;

        if($isAdmin) {
            $result = $this->getSingleRecipe($req);
            return $result;
        } else {
            return Response(["message" => "nie zgadza się"]);
        }
    }

    public function updateRecipeAdmin(Request $req) {
        
        $recipe = Recipe::where('recipeId', '=', $req->id)->first();
        $user = User::where('api_token', '=', $req->token)->first();
        $isAdmin = $user->user_type == 1 ? true : false;

        if($isAdmin){
            $recipe->updated_at = now();
            $recipe->save();
            if($recipe->name != $req->name){
                $recipe->name = $req->name;
                $recipe->save();
            }
            foreach($req->steps as $step){
                if($step['step'] !== null) {
                    CookingStep::updateOrCreate([
                        'stepId' => $step['id'],
                        'recipeId' => $req->id
                    ],['step' => $step['step']]);
                }
            }
            foreach($req->ingredients as $ingredient){
                if($ingredient !== null && $ingredient['ingredient'] !== null){
                    $ing = Ingredient::where('name', '=', $ingredient['ingredient'])->first();
                    if($ing !== null) {
                        $ing = $ing->ingredientId;
                    } else {
                        $ing = Ingredient::insertGetId(['name' => $ingredient['ingredient'], 'created_at' => now(), 'updated_at' => now()]);
                    }
                    $unit = Unit::where('name', '=', $ingredient['unit'])->first();
                    if($ingredient['id'] != $ing) {
                        $delIng = IngredientRecipe::where('ingredientId', '=', $ingredient['id'])->where('recipeId', '=', $recipe->recipeId)->first();
                        $delIng->forceDelete();
                    }
                    IngredientRecipe::updateOrCreate([
                        'ingredientId' => $ing, 
                        'recipeId' => $recipe->recipeId
                    ],[
                        'amount' => $ingredient['quantity'], 
                        'unitId' => $unit->id
                    ]);
                }    
            }
            foreach($req->categories as $category){
                if($category['categoryId'] !== null){
                    RecipeCategory::updateOrCreate(['categoryId' => $category['categoryId'], 'recipeId' => $recipe->recipeId]);
                }
            }
            if ($req->image) {
                Storage::put('images/' . $req->recipeId . '.txt', $req->image);
            }
        } else {
            return Response(['message' => 'Coś poszło nie tak.']);
        }
    }

    public function deleteIngredientFromRecipeAdmin(Request $req){
        $user = User::where('api_token', '=', $req->token)->first();
        $recipe = Recipe::where('recipeId', '=', $req->recipeId)->first();
        $isAdmin = $user->user_type == 1 ? true : false;

        if($isAdmin){
            $recipe->updated_at = now();
            $recipe->save();
            $ingredientRecipe = IngredientRecipe::where('recipeId', '=', $req->recipeId)->where('ingredientId', '=', $req->ingredientId)->first();
            if($ingredientRecipe) {
                $ingredientRecipe->forceDelete();
                return Response(["message" => "Usunięto"]);
            } else {
                return Response(["message" => "Nie znaleziono takiego składnika w bazie"]);
            }
        }
    }

    public function deleteStepFromRecipeAdmin(Request $req){
        $user = User::where('api_token', '=', $req->token)->first();
        $recipe = Recipe::where('recipeId', '=', $req->recipeId)->first();
        $isAdmin = $user->user_type == 1 ? true : false;

        if($isAdmin){
            $recipe->updated_at = now();
            $recipe->save();
            $cookingStep = CookingStep::where('recipeId', '=', $req->recipeId)->where('stepId', '=', $req->stepId)->first();
            if($cookingStep) {
                $cookingStep->forceDelete();
                return Response(["message" => "Usunięto"]);
            } else {
                return Response(["message" => "Nie znaleziono takiego kroku w bazie"]);
            }
        }
    }

    public function deleteRecipeAdmin(Request $req) {
        $user = User::where('api_token', '=', $req->token)->first();
        $recipe = Recipe::where('recipeId', '=', $req->recipeId)->first();
        $isAdmin = $user->user_type == 1 ? true : false;

        if($isAdmin){
            Recipe::where('recipeId', '=', $req->recipeId)->forceDelete();
            IngredientRecipe::where('recipeId', '=', $req->recipeId)->forceDelete();
            CookingStep::where('recipeId', '=', $req->recipeId)->forceDelete();
            RecipeCategory::where('recipeId', '=', $req->recipeId)->forceDelete();
            FavouriteRecipe::where('recipeId', '=', $req->recipeId)->forceDelete();
            Vote::where('recipeId', '=', $req->recipeId)->forceDelete();

            return Response(["message" => "Usunięto przepis"]);
        } else {
            return Response(["message" => "Nie znaleziono przepisu"]);
        }
    }

    public function sendChangeSuggestion(Request $req) {
        $recipe = Recipe::where('recipeId', '=', $req->recipeId)->first();
        $user = User::where('id', '=', $recipe->userId)->first();
        $admin = User::where('api_token', '=', $req->token)->first();
        if($admin->user_type == 1) {
            if($user) {
                $data['email'] = $user->email;
                $data['title'] = "Sugerowane zmiany w przepisie";
                $data['username'] = $user->username;
                $data['recipeName'] = $recipe->name;
                $data['suggestions'] = $req->suggestions;
    
                Mail::send('suggestionsMail', ['data' => $data], function($message) use ($data){
                    $message->to($data['email'])->subject($data['title']);
                });
            }
        } else {
            return Response(["message" => "Autoryzacja nie powiodła się"]);
        }
        
    }

    public function getNewestRecipes(Request $req){
        $recipes = Recipe::orderByDesc('created_at')->get()->take(4);
        
        return $recipes;
    }
}
