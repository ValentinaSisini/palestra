<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class IstruttoriController extends Controller
{
    public function index()
    {
        $elenco_istruttori = DB::table('Istruttori')->get();
        return view('istruttori', ['elenco_istruttori' => $elenco_istruttori]);
    }
}
