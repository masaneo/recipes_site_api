<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vote;

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

        $average = $sum / $count;

        return $average;
    }
}
