<?php

namespace Tests\Unit;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LezioniController;
use Illuminate\Http\Request;
use Tests\TestCase;

class MaxOreGiornaliereInsegnanteTest extends TestCase
{

    /**
     * Salvataggio nuova lezione nella tabella Lezioni:
     * PER UN INSEGNANTE
     * IN TUTTA LA GIORNATA HA FATTO MENO DI 8 ORE
     * =>  CORRETTA
     */
    public function testMaxOreGiornaliereInsegnante_OK()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Preparazione della request
        $data = [
            'nome' => 'Lezione OK',
            'inizio' => '2023-12-31T17:00:00',
            'fine' => '2023-12-31T18:00:00',
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
     * PER UN INSEGNANTE
     * IN TUTTA LA GIONATA HA FATTO PIU' DI 8 ORE
     * => ERRATA
     */
    public function testMaxOreGiornaliereInsegnante_KO()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        $data1 = [
            'nome' => 'Lezione KO 1',
            'inizio' => '2023-12-31T08:00:00',
            'fine' => '2023-12-31T10:00:00',
            'is_bambini' => 0,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        DB::table('Lezioni')->insert($data1);

        $data2 = [
            'nome' => 'Lezione KO 2',
            'inizio' => '2023-12-31T11:00:00',
            'fine' => '2023-12-31T13:00:00',
            'is_bambini' => 0,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        DB::table('Lezioni')->insert($data2);

        $data3 = [
            'nome' => 'Lezione KO 3',
            'inizio' => '2023-12-31T14:00:00',
            'fine' => '2023-12-31T16:00:00',
            'is_bambini' => 0,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        DB::table('Lezioni')->insert($data3);

        $data4 = [
            'nome' => 'Lezione KO 4',
            'inizio' => '2023-12-31T17:00:00',
            'fine' => '2023-12-31T19:00:00',
            'is_bambini' => 0,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        DB::table('Lezioni')->insert($data4);

        // Preparazione della request
        $data = [
            'nome' => 'Lezione KO',
            'inizio' => '2023-12-31T20:00:00',
            'fine' => '2023-12-31T21:00:00',
            'is_bambini' => 0,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data);
        $response = $LezioniController->store($request);

        $this->assertTrue($response->isRedirection());
        // Verifica che il controller restituisca il messaggio di errore corretto
        $this->assertTrue(session()->has('errors'));
        $error = session('errors');
        $this->assertArrayHasKey('errore_max_ore_giornaliere_insegnante', session('errors')->getBag('default')->getMessages());

        // Termina la transazione senza committare le modifiche effettuate durante il test
        DB::rollBack();

        // Controlla che il campo non sia stato salvato nel database
        $this->assertDatabaseMissing('Lezioni', [
            'nome' => $data['nome'],
            'inizio' => $data['inizio'],
            'fine' => $data['fine'],
            'is_bambini' => $data['is_bambini'],
            'id_istruttore' => $data['id_istruttore'],
            'id_stanza' => $data['id_stanza'],
        ]);

        $this->assertDatabaseMissing('Lezioni', [
            'nome' => $data1['nome'],
            'inizio' => $data1['inizio'],
            'fine' => $data1['fine'],
            'is_bambini' => $data1['is_bambini'],
            'id_istruttore' => $data1['id_istruttore'],
            'id_stanza' => $data1['id_stanza'],
        ]);

        $this->assertDatabaseMissing('Lezioni', [
            'nome' => $data2['nome'],
            'inizio' => $data2['inizio'],
            'fine' => $data2['fine'],
            'is_bambini' => $data2['is_bambini'],
            'id_istruttore' => $data2['id_istruttore'],
            'id_stanza' => $data2['id_stanza'],
        ]);

        $this->assertDatabaseMissing('Lezioni', [
            'nome' => $data3['nome'],
            'inizio' => $data3['inizio'],
            'fine' => $data3['fine'],
            'is_bambini' => $data3['is_bambini'],
            'id_istruttore' => $data3['id_istruttore'],
            'id_stanza' => $data3['id_stanza'],
        ]);

        $this->assertDatabaseMissing('Lezioni', [
            'nome' => $data4['nome'],
            'inizio' => $data4['inizio'],
            'fine' => $data4['fine'],
            'is_bambini' => $data4['is_bambini'],
            'id_istruttore' => $data4['id_istruttore'],
            'id_stanza' => $data4['id_stanza'],
        ]);
    }
}