<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function pay(Request $request)
    {
        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Token is created using Checkout or Elements!
            // Get the payment token ID submitted by the form:
            $charge = Charge::create([
                'amount' => 1000,
                'currency' => 'jpy',
                'description' => 'Test Payment! NOT Charged!',
                'source' => $request->id,
            ]);
        } catch (Exception $e) {
            return response()->json($request->id, 200);
        }
        // } catch (Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'errors' => $e->getMessage()
        //     ], 422);
        // }


        return response()->json([
            'success' => true,
            'data' => $charge,
            'message' => 'お支払いが完了しました。（テスト）'
        ], 201);
    }
}
