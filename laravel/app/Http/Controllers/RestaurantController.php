<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\Management;
use Illuminate\Support\Str;


class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::with('favorites')->get();
        return response()->json(compact('restaurants'), 200);
    }


    public function store(Request $request)
    {
        $manager = auth()->user();
        if ($manager->authority !== 'manager') {
            return response()->json([
                'message' => '不正アクセス'
            ], 403);
        }

        $uuid = (String) Str::uuid();
        $imageName = $request->image->getClientOriginalName();
        $request->image->storeAs("public/restaurant-img/$uuid/", $imageName);
        $newRestaurant = Restaurant::create([
            'uuid' => $uuid,
            'img_path' => "storage/restaurant-img/$uuid/$imageName",
            'name' => $request->name,
            'area' => $request->area,
            'genre' => $request->genre,
            'description' => $request->description,
        ]);
        
        $management = Management::create([
            'user_uuid' => $manager->uuid,
            'restaurant_uuid' => $uuid
        ]);
        $manager->load('managements.restaurant:uuid,name,area,genre,img_path');

        return response()->json(compact('manager'), 201);
    }


    public function show(Restaurant $restaurant)
    {
        return response()->json(compact('restaurant'), 200);
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
        $manager = auth()->user();
        if ($manager->authority !== 'manager') {
            return response()->json([
                'message' => '不正アクセス'
            ], 403);
        } 
        $isManager = Management::where([
            ['user_uuid', $manager->uuid],
            ['restaurant_uuid', $restaurant->uuid]
        ])->exists();
        
        if ($isManager) {
            $restaurant->update($request->all());
            $restaurant->load('reservations.user');
            return response()->json(compact('restaurant'), 200);
        } else {
            return response()->json([
                'message' => '権限がありません'
            ], 403);
        }
        
    }

    public function updateImage(Request $request)
    {
        $manager = auth()->user();
        if ($manager->authority !== 'manager') {
            return response()->json([
                'message' => '不正アクセス'
            ], 403);
        } 
        $isManager = Management::where([
            ['user_uuid', $manager->uuid],
            ['restaurant_uuid', $request->uuid]
        ])->exists();
        
        if ($isManager) {
            $imageName = $request->image->getClientOriginalName();
            $request->image->storeAs("public/restaurant-img/$request->uuid/", $imageName);
            Restaurant::where('uuid', $request->uuid)->update([
                'img_path' => "storage/restaurant-img/$request->uuid/$imageName"
            ]);
            return response()->json([
                'img_path' => "storage/restaurant-img/$request->uuid/$imageName"
            ], 200);
        } else {
            return response()->json([
                'message' => '権限がありません'
            ], 403);
        }
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
