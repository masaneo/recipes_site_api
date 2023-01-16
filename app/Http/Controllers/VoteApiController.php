<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;
use App\Models\Recipe;
use Illuminate\Support\Facades\Storage;

class VoteApiController extends Controller
{
    public function addVote(Request $req){
        $id = User::where('api_token', '=', $req->token)->first()->id;

        if($vote = Vote::updateOrCreate(
            ['userId' => $id, 'recipeId' => $req->recipeId], 
            ['vote' => $req->vote]
            )
        ){
            return Response(['message' => 'Successfully voted']);
        }

        return Response(['message' => 'Failed to vote']);
    }

    public function getUserVote(Request $req){
        if($req->token){
            $id = User::where('api_token', '=', $req->token)->first()->id;

            if($vote = Vote::where('userId', '=', $id)->where('recipeId', '=', $req->recipeId)->first()){
                return Response(["vote" => $vote->vote]);
            } else {
                return Response(["vote" => 0]);
            }
        }
        return Response(["vote" => "0"]);
    }

    public function getAverageVote(Request $req){
        $count = Vote::where('recipeId', '=', $req->recipeId)->count();
        $sum = Vote::where('recipeId', '=', $req->recipeId)->sum('vote');

        if($count > 0){
            $average = $sum / $count;
        } else {
            $average = 0;
        }

        return Response(["averageVote" => number_format($average, 2)]);
    }

    public function getHighestVoted(Request $req) {
        $result = [];

        $recipes = Vote::select('recipeId', DB::raw('round(AVG(vote), 2) as srednia'), DB::raw('COUNT(vote) as ilosc'))
        ->groupBy('recipeId')
        ->having('ilosc', '>=', 1) //zwiększyć później żeby wymagane było więcej głosów
        ->orderBy('srednia', 'desc')
        ->orderBy('ilosc', 'desc')
        ->take(10)->get();

        foreach($recipes as $index => $recipe) {
            $temp = Recipe::where('recipeId', '=', $recipe->recipeId)->get();
            $result[$index]['recipeId'] = $recipe->recipeId;
            $result[$index]['srednia'] = $recipe->srednia;
            $result[$index]['ilosc'] = $recipe->ilosc;
            $result[$index]['name'] = $temp[0]['name'];
            $result[$index]['img'] = 
            Storage::get('images/' . $recipe->recipeId . '.txt') 
            ? Storage::get('images/' . $recipe->recipeId . '.txt') 
            : Storage::get('images/no_image_available.txt');
        }

        return $result;
    }
}
