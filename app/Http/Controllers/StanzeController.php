<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class StanzeController extends Controller
{
    public function index()
    {
        $elenco_stanze = DB::table('Stanze')->get();
        return view('stanze', ['elenco_stanze' => $elenco_stanze]);
    }
}
