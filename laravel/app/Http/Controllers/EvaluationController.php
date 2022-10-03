<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    
    public function store(Request $request)
    {
        $evaluation = Evaluation::create($request->all());
        return response()->json(compact('evaluation'), 201);
    }

}
