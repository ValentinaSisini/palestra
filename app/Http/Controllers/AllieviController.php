<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

use Illuminate\Http\Request;

class AllieviController extends Controller
{
    public function index()
    {
        $elenco_allievi = DB::table('Allievi')->get();
        return view('allievi', ['elenco_allievi' => $elenco_allievi]);
    }

    /**
     * Crea una nuovo allievo
     */
    public function store(Request $request)
    {
        try
        {
            // Salvataggio nuova lezione nella tabella Lezioni
            DB::table('Allievi')->insert([
                'nome' => $request->input('nome'),
                'cognome' => $request->input('cognome'),
                'email' => $request->input('email'),
                'cellulare' => $request->input('cellulare'),
            ]);

            // Redirezionamento: successo
            return redirect()->route('elenco.allievi')->with('success', 'Nuovo allievo aggiunto con successo!');
        }
        catch(Exception $e)
        {
            // Redirezionamento: errore
            return redirect()->route('elenco.allievi')->with('error', 'Si è verificato un errore durante l\'inserimento del nuovo allievo' . $e->getMessage());
        }
    }

}
