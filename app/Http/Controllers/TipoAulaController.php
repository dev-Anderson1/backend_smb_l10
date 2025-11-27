<?php

namespace App\Http\Controllers;

use App\Models\TipoAula;
use Illuminate\Http\Request;

class TipoAulaController extends Controller
{
    public function index()
    {
        return response()->json(TipoAula::orderBy('nome')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nome' => 'required|string|max:255']);
        $tipo = TipoAula::create($data);
        return response()->json($tipo);
    }

   

}
