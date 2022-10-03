<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        Reservation::create($request->all());
        return response()->json(null, 201);
    }


    public function update(Request $request, Reservation $reservation)
    {
        $reservation->update($request->all());
        return response()->json(compact('reservation'), 200);
    }


    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return response()->json(null, 200);
    }
}
