<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryApiController extends Controller
{
    public function addCategory(Request $req){
        if(!Category::where('name', '=', $req->name)->first()){
            Category::create(['name' => $req->name]);
            return Response(['message' => 'Successfully created new category']);
        }
        return Response(['message' => 'Category already exists in database']);
    }
    
    public function getAllCategories(Request $req){
        return Category::all();
    }
}
