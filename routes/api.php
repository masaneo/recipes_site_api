<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\IngredientApiController;
use App\Http\Controllers\CategoryApiController;
use App\Http\Controllers\RecipeApiController;
use App\Http\Controllers\VoteApiController;

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

Route::resource('/users', UserApiController::class);
Route::post('/users/auth', [UserApiController::class, 'login']);
Route::post('/recipes/ingredients/addIngredient', [IngredientApiController::class, 'addIngredient']);
Route::get('/recipes/ingredients/getIngredients', [IngredientApiController::class, 'getIngredients']);
Route::post('/recipes/categories/addCategory', [CategoryApiController::class, 'addCategory']);
Route::get('/recipes/categories/getAllCategories', [CategoryApiController::class, 'getAllCategories']);
Route::post('/recipes/addRecipe', [RecipeApiController::class, 'addRecipe']);
Route::get('/recipes/getAllRecipes', [RecipeApiController::class, 'getAllRecipes']);
Route::post('/recipes/votes/addVote', [VoteApiController::class, 'addVote']);
Route::get('/recipes/votes/getAverageVote', [VoteApiController::class, 'getAverageVote']);