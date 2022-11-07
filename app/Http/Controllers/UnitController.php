<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    public function getAllUnits(Request $req){
        return Response(["units" => Unit::all()]);
    }
}
