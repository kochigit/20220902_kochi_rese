<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::with('favorites')->get();
        return response()->json(compact('restaurants'), 200);
    }


    public function store(Request $request)
    {
        //
    }


    public function show(Restaurant $restaurant)
    {
        $res = Restaurant::with('favorites')->where('uuid', $restaurant->uuid)->first();
        return response()->json([
            'restaurant' => $res
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restaurant $restaurant)
    {
        //
    }

    public function search(Request $request) 
    {
        $match = [];
        if ($request->area) {
            $match[] =['area', '=', $request->area];
        }
        if ($request->genre) {
            $match[] =['genre', '=', $request->genre];
        }
        if ($request->word) {
            $match[] =['name', 'LIKE', "%$request->word%"];
        }
        $searchedRestaurants = Restaurant::with('favorites')->where($match)->get();
        return response()->json(compact('searchedRestaurants'), 200);
    }
}
