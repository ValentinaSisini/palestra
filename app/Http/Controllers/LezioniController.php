<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'Lezioni.nome as nome',
            'Lezioni.inizio',
            'Lezioni.fine',
            'Lezioni.is_bambini',
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
        $inizio_formato_corretto = Carbon::parse($request->input('inizio'))->format('Y-m-d\TH:i:s');
        $fine_formato_corretto = Carbon::parse($request->input('fine'))->format('Y-m-d\TH:i:s');

        // Validazione campi
        $validator = Validator::make($request->all(), [
            'nome' => 'nullable',
            'inizio' => [
                'nullable', 
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $inizioLezione = Carbon::parse($value);

                    // Calcola l'anticipo in ore rispetto all'orario attuale
                    $anticipoInOre = now('Europe/Rome')->diffInHours($inizioLezione);

                    // Esegue il controllo sull'anticipo
                    if ($anticipoInOre <= 3) {
                        $fail('La lezione deve essere pianificata con almeno 3 ore di anticipo.');
                    }
                }
            ],
            'fine' => [
                'nullable',
                'date',
                'after:inizio', // Assicura che 'fine' sia dopo 'inizio'
                function ($attribute, $value, $fail) use ($request) {
                    $inizio = strtotime($request->input('inizio'));
                    $fine = strtotime($value);

                    // Calcola la differenza in minuti tra 'inizio' e 'fine'
                    $diffInMinutes = ($fine - $inizio) / 60;

                    // Verifica che la durata sia compresa tra 30 minuti e 2 ore
                    if ($diffInMinutes < 30 || $diffInMinutes > 120) {
                        $fail('La durata della lezione deve essere compresa tra 30 minuti e 2 ore.');
                    }
                }
            ],
            'is_bambini' => function ($attribute, $value, $fail) use ($request) {
                $fineTime = date('H:i:s', strtotime($request->input('fine')));
                if ($fineTime > '20:00:00' && ($value == 1)) {
                    // La validazione fallisce se l'orario nel campo "fine" è successivo alle 20:00 e il campo "is_bambini" è 1
                    $fail('Le lezioni per bambini non possono finire dopo le 20:00');
                }
            },
            'id_istruttore' => 'required|exists:istruttori,id',
            'id_stanza' => 'required|exists:stanze,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('elenco.lezioni')->with('error', 'Si è verificato un errore durante l\'inserimento della nuova lezione ' . $validator->errors())->withErrors($validator)->withInput();
        }

        /** Controlla se esiste già una lezione attiva nella stanza per l'orario specificato */
        $existingLezione = DB::table('Lezioni')
        ->where('id_stanza', $request->id_stanza, 'and')
        ->where(function ($query) use ($request, $inizio_formato_corretto, $fine_formato_corretto) {
                $query->whereBetween('inizio', [$inizio_formato_corretto, $fine_formato_corretto])
                      ->orWhereBetween('fine', [$inizio_formato_corretto, $fine_formato_corretto])
                      ->orWhere(function ($query) use ($request, $inizio_formato_corretto, $fine_formato_corretto) {
                          $query->where('inizio', '<', $inizio_formato_corretto)
                                ->where('fine', '>', $fine_formato_corretto);
                      });
            })
            ->first();

        if ($existingLezione) {
            // Se esiste già una lezione attiva nella stanza per l'orario specificato, restituisci un messaggio di errore
            return redirect()->route('elenco.lezioni')
            ->with('error', 'Nella stanza selezionata c\'è già una lezione in corso per l\'orario specificato.')
            ->withErrors([
                'errore_lezioni_stessa_stanza' => 'Nella stanza selezionata c\'è già una lezione in corso per l\'orario specificato.'
            ]);
        }

        /** Controlla che l'istruttore non abbia più di 8 ore di lezione nello stesso giorno */
        // Ricava tutte le lezioni dell'istruttore nella giornata specificata
        $lezioni = DB::table('Lezioni')
        ->where('id_istruttore', '=', $request->input('id_istruttore'), 'and')
        ->whereDate('inizio', '=', $request->input('inizio'))
        ->select('*')
        ->get();
    
        if($lezioni)
        {
            // Conta il numero di ore di lezione dell'istruttore durante la giornata
            $oreDiLezione = 0;
            foreach ($lezioni as $lezione) {
                $inizio = Carbon::parse($lezione->inizio);
                $fine = Carbon::parse($lezione->fine);
                $oreDiLezione += $inizio->diffInHours($fine);
            }
        }

        // Ci aggiunge le ore della lezione che si sta inserendo
        $inizio_nuova_lezione = Carbon::parse($request->input('inizio'));
        $fine_nuova_lezione = Carbon::parse($request->input('fine'));
        $oreDiLezione += $inizio_nuova_lezione->diffInHours($fine_nuova_lezione);

        // Esegui il controllo sulle ore di lezione massime
        if ($oreDiLezione > 8) {
            return redirect()->route('elenco.lezioni')
            ->with('error', 'L\'insegnante ha già superato il limite di 8 ore di lezione per questo giorno.')
            ->withErrors([
                'errore_max_ore_giornaliere_insegnante' => 'L\'insegnante ha già superato il limite di 8 ore di lezione per questo giorno.'
            ]);
        }

        try
        {
            // Salvataggio nuova lezione nella tabella Lezioni
            DB::table('Lezioni')->insert([
                'nome' => $request->input('nome'),
                'inizio' => DB::raw("CONVERT(datetime, '" . $inizio_formato_corretto . "')"),
                'fine' => DB::raw("CONVERT(datetime, '" . $fine_formato_corretto . "')"),
                'is_bambini' => ($request->input('is_bambini') ? 1 : 0),
                'id_istruttore' => $request->input('id_istruttore'),
                'id_stanza' => $request->input('id_stanza'),
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
            return view('edit_lezione', compact('lezione'))->with('success', 'Lezione trovata!');
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
                        'nome' => $request->input('nome'),
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
