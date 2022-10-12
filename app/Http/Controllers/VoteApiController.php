<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vote;

class VoteApiController extends Controller
{
    public function addVote(Request $req){
        //$id = User::where('api_token', '=', $req->token)->first()->id;

        $id = 1; // for now, delete later;

        if(Vote::where('receiptId', '=', $req->receiptId)->where('userId', '=', $id)->first()){
            Vote::where('receiptId', '=', $req->receiptId)->where('userId', '=', $id)->update(['vote' => $req->vote]);
            return Response(['message' => 'Successfully updated vote']);
        } else {
            if(Vote::create(['userId' => $id, 'receiptId' => $req->receiptId,'vote' => $req->vote])){
                return Response(['message' => 'Successfully voted']);
            }
        }

        return Response(['message' => 'Failed to vote']);
    }

    public function getAverageVote(Request $req){
        $count = Vote::where('receiptId', '=', $req->receiptId)->count();
        $sum = Vote::where('receiptId', '=', $req->receiptId)->sum('vote');

        $average = $sum / $count;

        return $average;
    }
}
