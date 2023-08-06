<?php

namespace Tests\Unit;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LezioniController;
use Illuminate\Http\Request;
use Tests\TestCase;

class DurataLezioneTest extends TestCase
{

    /**
     * Salvataggio nuova lezione nella tabella Lezioni:
     * DURATA: 1 H 30 MIN
     * =>  CORRETTA
     */
    public function testDurataLezione_OK()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Preparazione della request
        $data = [
            'nome' => 'Lezione OK',
            'inizio' => '2023-12-31T17:00:00',
            'fine' => '2023-12-31T18:30:00',
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
     * DURATA: 2 H 30 MIN
     * => ERRATA
     */
    public function testDurataLezione_KO_lunga()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Preparazione della request
        $data = [
            'nome' => 'Lezione KO lunga',
            'inizio' => '2023-12-31T20:00:00',
            'fine' => '2023-12-31T22:30:00',
            'is_bambini' => 0,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data);
        $response = $LezioniController->store($request);

        // Verifica se ci sono errori di validazione sul campo fine
        $this->assertTrue($response->isRedirection());
        $this->assertArrayHasKey('fine', session('errors')->getBag('default')->getMessages());

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
     * DURATA: 20 MIN
     * => ERRATA
     */
    public function testDurataLezione_KO_corta()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Preparazione della request
        $data = [
            'nome' => 'Lezione KO lunga',
            'inizio' => '2023-12-31T20:00:00',
            'fine' => '2023-12-31T20:20:00',
            'is_bambini' => 0,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data);
        $response = $LezioniController->store($request);

        // Verifica se ci sono errori di validazione sul campo fine
        $this->assertTrue($response->isRedirection());
        $this->assertArrayHasKey('fine', session('errors')->getBag('default')->getMessages());

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