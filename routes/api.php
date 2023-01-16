<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\IngredientApiController;
use App\Http\Controllers\CategoryApiController;
use App\Http\Controllers\RecipeApiController;
use App\Http\Controllers\VoteApiController;
use App\Http\Controllers\UnitController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//User routes
Route::resource('/users', UserApiController::class);
Route::post('/users/auth', [UserApiController::class, 'login']);

//Ingredients routes
Route::post('/recipes/ingredients/addIngredient', [IngredientApiController::class, 'addIngredient']);
Route::get('/recipes/ingredients/getIngredients', [IngredientApiController::class, 'getIngredients']);

//Category Routes
Route::post('/recipes/categories/addCategory', [CategoryApiController::class, 'addCategory']);
Route::get('/recipes/categories/getAllCategories', [CategoryApiController::class, 'getAllCategories']);

//Recipe Routes
Route::post('/recipes/addRecipe', [RecipeApiController::class, 'addRecipe']);
Route::get('/recipes/getAllRecipes', [RecipeApiController::class, 'getAllRecipes']);
Route::post('/recipes/getSingleRecipe', [RecipeApiController::class, 'getSingleRecipe']);
Route::get('/recipes/getSingleRecipe/{id}/getImage', [RecipeApiController::class, 'getSingleRecipeImage']);
Route::get('/recipes/getUserRecipes', [RecipeApiController::class, 'getUserRecipes']);
Route::post('/recipes/addToFavourite', [RecipeApiController::class, 'addRecipeToFavourite']);
Route::delete('/recipes/removeFromFavourite', [RecipeApiController::class, 'removeFromFavourite']);
Route::get('/recipes/getFavouriteRecipes', [RecipeApiController::class, 'getFavouriteRecipes']);
Route::get('/recipes/searchRecipes', [RecipeApiController::class, 'getRecipesSearch']);

//Vote routes
Route::post('/recipes/votes/addVote', [VoteApiController::class, 'addVote']);
Route::get('/recipes/votes/getAverageVote', [VoteApiController::class, 'getAverageVote']);
Route::get('/recipes/votes/getUserVote', [VoteApiController::class, 'getUserVote']);
Route::get('/recipes/votes/highestVoted', [VoteApiController::class, 'getHighestVoted']);

//Other routes
Route::get('/recipes/units/getAllUnits', [UnitController::class, 'getAllUnits']);