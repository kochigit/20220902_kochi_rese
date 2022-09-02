<?php

namespace App\Http\Controllers;

use App\Models\Management;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant;

class ManagementController extends Controller
{
    public function getManager()
    {
        $authority = auth()->user()->authority;
        if ($authority === 'manager') {
            $manager = auth()->user()->load('managements.restaurant:uuid,name,area,genre,img_path');
            return response()->json(compact('manager'), 200);
        } else {
            return response()->json([
                'message' => '不正アクセス'
            ],403);
        }
    }


    public function getRestaurants()
    {
        $restaurants = Restaurant::with('managements')->get();
        return response()->json(compact('restaurants'), 200);
    }


    public function checkManagement(Request $request)
    {
        $manager = auth()->user();
        if ($manager->authority !== 'manager') {
            return response()->json([
                'message' => '不正アクセス'
            ], 403);
        } 
        $isManager = Management::where([
                ['user_uuid', $manager->uuid],
                ['restaurant_uuid', $request->restaurant_uuid]
            ])->exists();
        if ($isManager) {
            return response()->json(compact('isManager'),200);
        } else {
            return response()->json([
                'message' => '権限がありません'
            ], 204);
        }
    }


    public function getManagedRestaurant(Restaurant $restaurant)
    {
        $restaurant->load('reservations.user');
        return response()->json(compact('restaurant'), 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $authority = auth()->user()->authority;
        if ($authority === 'manager') {
            $management = Management::create($request->all());
            return response()->json(compact('management'), 201);
        } else {
            return response()->json(403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Management  $management
     * @return \Illuminate\Http\Response
     */
    public function show(Management $management)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Management  $management
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Management $management)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Management  $management
     * @return \Illuminate\Http\Response
     */
    public function destroy(Management $management)
    {
        //
    }
}
