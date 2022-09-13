<?php

namespace App\Http\Controllers;

use App\Mail\EmailFromManager;
use App\Models\Favorite;
use App\Models\Management;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Mail;

class ManagementController extends Controller
{
    public function getManager()
    {
        $manager = auth()->user();
        if ($manager->authority === 'manager') {
            $manager->load('managements.restaurant:uuid,name,area,genre,img_path');
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


    public function sendEmails(Request $request)
    {
        $users = collect();
        if (in_array('visited', $request->sendTo)) {
            $users = User::Join('reservations','users.uuid','reservations.user_uuid')
            ->where([
                ['restaurant_uuid', $request->uuid],
                ['visited_at', '!=', null]
            ])->select('name','email')->get()->unique('email');
        }
        if (in_array('reserved', $request->sendTo)) {
            $users = $users->concat(
                User::Join('reservations','users.uuid','reservations.user_uuid')
                ->where([
                    ['restaurant_uuid', $request->uuid],
                    ['visited_at', null]
                ])->select('name','email')->get()->unique('email')
            );
        }
        if (in_array('liked', $request->sendTo)) {
            $users = $users->concat(
                User::Join('favorites','users.uuid','favorites.user_uuid')
                ->where('restaurant_uuid', $request->uuid)
                ->select('name','email')->get()->unique('email')
            );
        }
        $users = $users->concat(
            User::Join('managements', 'users.uuid', 'managements.user_uuid')
            ->where('restaurant_uuid', $request->uuid)->select('name','email')->get()
        );
        $users = $users->unique('email');
        foreach ($users as $user) {
            Mail::to($user)->send(new EmailFromManager($user, $request));
        }
        return response()->json(null, 200);
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
        $manager = auth()->user();
        if ($manager->authority === 'manager') {
            Management::create($request->all());
            $manager->load('managements.restaurant:uuid,name,area,genre,img_path');
            return response()->json(compact('manager'), 201);
        } else {
            return response()->json(null, 403);
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
