<?php

namespace Tests\Unit;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LezioniController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Tests\TestCase;

class LimiteTempoNuovaLezioneTest extends TestCase
{

    /**
     * Salvataggio nuova lezione nella tabella Lezioni:
     * 5 ORE PRIMA DELL'INIZIO
     * => CORRETTA
     */
    public function testLimiteTempoNuovaLezione_OK()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Calcola Una data che corrisponde al timestamp attuale + 5 ore
        $oraAttuale = Carbon::now('Europe/Rome');
        $dataTra5Ore = $oraAttuale->addHours(5);
        $dataInizioOK = $dataTra5Ore->format('Y-m-d\TH:i:s');
        $dataTra6Ore = $oraAttuale->addHours(1);
        $dataFineOK = $dataTra6Ore->format('Y-m-d\TH:i:s');

        // Preparazione della request
        $data = [
            'nome' => 'Lezione OK',
            'inizio' => $dataInizioOK,
            'fine' => $dataFineOK,
            'is_bambini' => 0,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data);
        $response = $LezioniController->store($request);

        // Verifica se il LezioniController non ha generato errori di validazione
        $this->assertTrue($response->isRedirection());

        // Termina la transazione senza committare le modifiche effettuate durante il test
        DB::rollBack();
        
        // Controlla che il campo non sia stato salvato nel database
        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data['nome'],
            'inizio' => $data['inizio'],
            'fine' => $data['fine'],
            'is_bambini' => $data['is_bambini'],
            'id_istruttore' => $data['id_istruttore'],
            'id_stanza' => $data['id_stanza'],
        ]);
    }


    /**
     * Salvataggio nuova lezione nella tabella Lezioni:
     * 2 ORE PRIMA DELL'INIZIO (< 3 ORE)
     * => ERRATA
     */
    public function testLimiteTempoNuovaLezione_KO()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Calcola Una data che corrisponde al timestamp attuale + 5 ore
        $oraAttuale = Carbon::now('Europe/Rome');
        $dataTra2Ore = $oraAttuale->addHours(2);
        $dataInizioKO = $dataTra2Ore->format('Y-m-d\TH:i:s');
        $dataTra3Ore = $oraAttuale->addHours(1);
        $dataFineKO = $dataTra3Ore->format('Y-m-d\TH:i:s');

        // Preparazione della request
        $data = [
            'nome' => 'Lezione KO',
            'inizio' => $dataInizioKO,
            'fine' => $dataFineKO,
            'is_bambini' => 0,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data);
        $response = $LezioniController->store($request);

        // Verifica se ci sono errori di validazione sul campo inizio
        $this->assertTrue($response->isRedirection());
        $this->assertArrayHasKey('inizio', session('errors')->getBag('default')->getMessages());

        // Termina la transazione senza committare le modifiche effettuate durante il test
        DB::rollBack();
        
        // Controlla che il campo non sia stato salvato nel database
        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data['nome'],
            'inizio' => $data['inizio'],
            'fine' => $data['fine'],
            'is_bambini' => $data['is_bambini'],
            'id_istruttore' => $data['id_istruttore'],
            'id_stanza' => $data['id_stanza'],
        ]);
    }
}