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
use App\Http\Controllers\EmailVerificationController;

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
Route::post('/users/auth', [UserApiController::class, 'login'])->name('login');
Route::post('/users/email/verification', [UserApiController::class, 'verifyEmail']);
Route::post('/users/email/resendVerification', [UserApiController::class, 'resendVerificationEmail']);

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
Route::get('/recipes/findRecipes', [RecipeApiController::class, 'findRecipesWithIngredients']);
Route::get('/recipes/getIngredientsByRecipeId', [RecipeApiController::class, 'getAllIngredientsByRecipeId']);
Route::get('/recipes/getRecipeData', [RecipeApiController::class, 'getRecipeDataToEdit']);
Route::put('/recipes/modifyRecipe', [RecipeApiController::class, 'updateRecipe']);
Route::delete('/recipes/editRecipe/deleteIngredient', [RecipeApiController::class, 'deleteIngredientFromRecipe']);
Route::delete('/recipes/editRecipe/deleteStep', [RecipeApiController::class, 'deleteStepFromRecipe']);
Route::delete('/recipes/editRecipe/deleteRecipe', [RecipeApiController::class, 'deleteRecipe']);
Route::get('/recipes/getRecipesByCategory', [RecipeApiController::class, 'getRecipesByCategory']);
Route::get('/recipes/admin/getAllRecipes', [RecipeApiController::class, 'getRecipesForAdmin']);
Route::get('/recipes/admin/getHiddenRecipes', [RecipeApiController::class, 'getHiddenRecipesForAdmin']);
Route::get('/recipes/admin/getRecipeData', [RecipeApiController::class, 'getRecipeDataAdmin']);
Route::put('/recipes/admin/modifyRecipe', [RecipeApiController::class, 'updateRecipeAdmin']);
Route::delete('/recipes/admin/editRecipe/deleteIngredient', [RecipeApiController::class, 'deleteIngredientFromRecipeAdmin']);
Route::delete('/recipes/admin/editRecipe/deleteStep', [RecipeApiController::class, 'deleteStepFromRecipeAdmin']);
Route::delete('/recipes/admin/editRecipe/deleteRecipe', [RecipeApiController::class, 'deleteRecipeAdmin']);
Route::post('/recipes/admin/editRecipe/sendChangeSuggestion', [RecipeApiController::class, 'sendChangeSuggestion']);
Route::put('/recipes/admin/editRecipe/acceptChanges', [RecipeApiController::class, 'acceptChangesAdmin']);
Route::get('/recipes/getNewestRecipes', [RecipeApiController::class, 'getNewestRecipes']);
Route::post('/shoppingList/sendToEmail', [RecipeApiController::class, 'sendShoppingListToEmail']);

//Vote routes
Route::post('/recipes/votes/addVote', [VoteApiController::class, 'addVote']);
Route::get('/recipes/votes/getAverageVote', [VoteApiController::class, 'getAverageVote']);
Route::get('/recipes/votes/getUserVote', [VoteApiController::class, 'getUserVote']);
Route::get('/recipes/votes/highestVoted', [VoteApiController::class, 'getHighestVoted']);

//Other routes
Route::get('/recipes/units/getAllUnits', [UnitController::class, 'getAllUnits']);