<?php

namespace Tests\Unit;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LezioniController;
use Illuminate\Http\Request;
use Tests\TestCase;

class OrarioBambiniTest extends TestCase
{

    /**
     * Salvataggio nuova lezione nella tabella Lezioni:
     * PER BAMBINI
     * FINISCE PRIMA DELLE 20
     * =>  CORRETTA
     */
    public function testOrarioBambini_OK()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Preparazione della request
        $data = [
            'nome' => 'Lezione OK',
            'inizio' => '2023-12-31T17:00:00',
            'fine' => '2023-12-31T18:00:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data);
        $response = $LezioniController->store($request);

        // Verifica se il LezioniController non ha generato errori di validazione
        $this->assertTrue($response->isRedirection());

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

        // Termina la transazione senza committare le modifiche effettuate durante il test
        DB::rollBack();
    }


    /**
     * Salvataggio nuova lezione nella tabella Lezioni:
     * PER BAMBINI
     * FINISCE DOPO LE 20
     * => ERRATA
     */
    public function testOrarioBambini_KO()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Preparazione della request
        $data = [
            'nome' => 'Lezione KO',
            'inizio' => '2023-12-31T20:00:00',
            'fine' => '2023-12-31T21:00:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data);
        $response = $LezioniController->store($request);

        // Verifica se ci sono errori di validazione sul campo is_bambini
        $this->assertTrue($response->isRedirection());
        $this->assertArrayHasKey('is_bambini', session('errors')->getBag('default')->getMessages());

        // Controlla che il campo non sia stato salvato nel database
        $this->assertDatabaseMissing('Lezioni', [
            'nome' => $data['nome'],
            'inizio' => $data['inizio'],
            'fine' => $data['fine'],
            'is_bambini' => $data['is_bambini'],
            'id_istruttore' => $data['id_istruttore'],
            'id_stanza' => $data['id_stanza'],
        ]);

        // Termina la transazione senza committare le modifiche effettuate durante il test
        DB::rollBack();
    }
}