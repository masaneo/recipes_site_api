<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class IngredientRecipe extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'ingredients_recipes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ingredientId',
        'recipeId',
        'amount',
        'unitId',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        
    ];

    protected $primaryKey = ['ingredientId', 'recipeId'];
    public $incrementing = false;
}
