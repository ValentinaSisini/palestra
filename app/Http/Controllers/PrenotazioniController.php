<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Exception;

use Illuminate\Http\Request;

class PrenotazioniController extends Controller
{
    /**
     * Restituisce l'elenco delle prenotazioni
     * Restituisce inoltre l'elenco degli allievi e delle lezioni
     * per l'inserimento di una nuova prenotazione
     */
    public function index()
    {
        // Ricava l'elenco di tutte le prenotazioni
        $elenco_prenotazioni = DB::table('Prenotazioni')
        ->join('Allievi', 'Allievi.id', '=', 'Prenotazioni.id_allievo')
        ->join('Lezioni', 'Lezioni.id', '=', 'Prenotazioni.id_lezione')
        ->select(
            'Prenotazioni.id as id',
            'Allievi.nome as nome_allievo',
            'Allievi.cognome as cognome_allievo',
            'Lezioni.nome as nome_lezione',
            'Lezioni.inizio',
            'Lezioni.fine'
        )
        ->get();

        // Ricava l'elenco di tutti gli allievi
        $allievi = DB::table('Allievi')->get();

        // Riicava l'elenco di tutte le lezioni
        $lezioni = DB::table('Lezioni')->get();

        return view('prenotazioni', [
            'elenco_prenotazioni' => $elenco_prenotazioni, 
            'allievi' => $allievi, 
            'lezioni' => $lezioni
        ]);
    }

    /**
     * Crea una nuova prenotazione
     */
    public function store(Request $request)
    {
        try
        {
            // Salvataggio nuova prenotazione nella tabella Prenotazioni
            DB::table('Prenotazioni')->insert([
                'id_allievo' => $request->input('allievo'),
                'id_lezione' => $request->input('lezione'),
            ]);

            // Redirezionamento: successo
            return redirect()->route('elenco.prenotazioni')->with('success', 'Nuova prenotazione aggiunta con successo!');
        }
        catch(Exception $e)
        {
            // Redirezionamento: errore
            return redirect()->route('elenco.prenotazioni')->with('error', 'Si è verificato un errore durante l\'inserimento della nuova prenotazione' . $e->getMessage());
        }
    }
}
