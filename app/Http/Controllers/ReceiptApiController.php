<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receipt;
use App\Models\CookingStep;
use App\Models\IngredientReceipt;
use App\Models\ReceiptCategory;
use Illuminate\Support\Facades\DB;

class ReceiptApiController extends Controller
{
    public function addReceipt(Request $req){
        DB::transaction(function() use ($req){
            $receiptId = Receipt::create(['name' => $req->name])->id;

            foreach($req->steps as $number => $step){
                CookingStep::create(['stepId' => $number, 'receiptId' => $receiptId, 'step' => $step]);
            }
            foreach($req->categories as $category){
                ReceiptCategory::create(['categoryId' => $category, 'receiptId' => $receiptId]);
            }
            foreach($req->ingredients as $ingredient){
                IngredientReceipt::create(['ingredientId' => $ingredient['ingredientId'], 'receiptId' => $receiptId, 'amount' => $ingredient['amount']]);
            }
        });

        return Response(['message' => 'Successfully added new receipt']);
    }
}
