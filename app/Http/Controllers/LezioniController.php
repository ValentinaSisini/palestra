<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

use Illuminate\Http\Request;

class LezioniController extends Controller
{
    /**
     * Restituisce l'elenco delle lezioni
     * Restituisce inoltre l'elenco degli insegnanti e delle stanza che serviranno
     * per l'inserimento di una nuova lezione
     */
    public function index()
    {
        // Ricava l'elenco di tutte le lezioni
        $elenco_lezioni = DB::table('Lezioni')
        ->join('Stanze', 'Stanze.id', '=', 'Lezioni.id_stanza')
        ->join('Istruttori', 'Istruttori.id', '=', 'Lezioni.id_istruttore')
        ->select(
            'Lezioni.id as id',
            'Lezioni.nome as nome_lezione',
            'Lezioni.inizio',
            'Lezioni.fine',
            'Stanze.nome as nome_stanza',
            'Istruttori.nome as nome_istruttore'
        )
        ->get();

        // Ricava l'elenco di tutti gli istruttori
        $istruttori = DB::table('Istruttori')->get();

        // Riicava l'elenco di tutte le stanza
        $stanze = DB::table('Stanze')->get();

        return view('lezioni', [
            'elenco_lezioni' => $elenco_lezioni, 
            'istruttori' => $istruttori, 
            'stanze' => $stanze
        ]);
    }

    /**
     * Crea una nuova lezione
     */
    public function store(Request $request)
    {
        try
        {
            $inizio_formato_corretto = Carbon::parse($request->input('inizio'))->format('Y-m-d\TH:i:s');
            $fine_formato_corretto = Carbon::parse($request->input('fine'))->format('Y-m-d\TH:i:s');

            // Salvataggio nuova lezione nella tabella Lezioni
            DB::table('Lezioni')->insert([
                'nome' => $request->input('nome_lezione'),
                'inizio' => DB::raw("CONVERT(datetime, '" . $inizio_formato_corretto . "')"),
                'fine' => DB::raw("CONVERT(datetime, '" . $fine_formato_corretto . "')"),
                'id_istruttore' => $request->input('istruttore'),
                'id_stanza' => $request->input('stanza'),
            ]);

            // Redirezionamento: successo
            return redirect()->route('elenco.lezioni')->with('success', 'Nuova lezione aggiunta con successo!');
        }
        catch(Exception $e)
        {
            // Redirezionamento: errore
            return redirect()->route('elenco.lezioni')->with('error', 'Si è verificato un errore durante l\'inserimento della nuova lezione' . $e->getMessage());
        }
    }

    /**
     * Restituisce la lezione che ha come id quello passato come parametro 
     */
    public function edit($id)
    {
        try
        {
            $lezione = DB::table('Lezioni')->find($id);
            return view('edit_lezione', compact('lezione'))->with('success', 'Nuova lezione aggiunta con successo!');
        }
        catch(Exception $e)
        {
            // Redirezionamento: errore
            return redirect()->route('elenco.lezioni')->with('error', 'Si è verificato un errore durante il caricamento della lezione' . $e->getMessage());
        }
    }

    /**
     * Salva le modifiche della lezione il cui id è passato come parametro
     */
    public function update(Request $request, $id)
    {
        $inizio_formato_corretto = Carbon::parse($request->input('inizio'))->format('Y-m-d\TH:i:s');
        $fine_formato_corretto = Carbon::parse($request->input('fine'))->format('Y-m-d\TH:i:s');

        try 
        {
            DB::table('Lezioni')
            ->where('id', '=', $id)
            ->update(
                    [
                        'nome' => $request->input('nome_lezione'),
                        'inizio' => DB::raw("CONVERT(datetime, '" . $inizio_formato_corretto . "')"),
                        'fine' => DB::raw("CONVERT(datetime, '" . $fine_formato_corretto . "')")
                    ]
                    );
               

            // Redirect alla pagina di conferma o a un'altra pagina dopo l'update
            return redirect()->route('elenco.lezioni', $id)->with('success', 'Lezione aggiornata con successo!');
        }
        catch(Exception $e)
        {
            // Redirezionamento: errore
            return redirect()->route('elenco.lezioni')->with('error', 'Si è verificato un errore durante il salvataggio della lezione' . $e->getMessage());
        }
    }

    /**
     * Elimina la lezione il cui id è passato come parametro
     */
    public function destroy($id)
    {
        try
        {
            // Esegui la cancellazione
            DB::table('Lezioni')->where('id', $id)->delete();

            // Redirect all'elenco delle lezioni dopo la cancellazione
            return redirect()->route('elenco.lezioni')->with('success', 'Lezione cancellata con successo.');
        }
        catch(Exception $e)
        {
            // Redirezionamento: errore
            return redirect()->route('elenco.lezioni')->with('error', 'Si è verificato un errore durante la cancellazione della lezione' . $e->getMessage());
        }
    }



}
