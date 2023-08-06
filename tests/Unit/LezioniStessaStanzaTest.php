<?php

namespace Tests\Unit;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LezioniController;
use Illuminate\Http\Request;
use Tests\TestCase;

class LezioniStessaStanzaTest extends TestCase
{

    /**
     * Salvataggio nuova lezione nella tabella Lezioni:
     * ALLO STESSO ORARIO
     * IN STANZE DIVERSE
     * =>  CORRETTA
     */
    public function testLezioniStessaStanza_OK()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Preparazione delle request: due lezioni allo stesso orario in stanze diverse
        $data1 = [
            'nome' => 'Lezione KO 2',
            'inizio' => '2023-12-31T17:00:00',
            'fine' => '2023-12-31T18:00:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        DB::table('Lezioni')->insert($data1);

        $data2 = [
            'nome' => 'Lezione KO 2',
            'inizio' => '2023-12-31T17:00:00',
            'fine' => '2023-12-31T18:00:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data2);
        $response = $LezioniController->store($request);

        // Verifica se il LezioniController non ha generato errori di validazione
        $this->assertTrue($response->isRedirection());

        // Termina la transazione senza committare le modifiche effettuate durante il test
        DB::rollBack();

        // Controlla che i record non siano stati salvati nel database
        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data1['nome'],
            'inizio' => $data1['inizio'],
            'fine' => $data1['fine'],
            'is_bambini' => $data1['is_bambini'],
            'id_istruttore' => $data1['id_istruttore'],
            'id_stanza' => $data1['id_stanza'],
        ]);

        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data2['nome'],
            'inizio' => $data2['inizio'],
            'fine' => $data2['fine'],
            'is_bambini' => $data2['is_bambini'],
            'id_istruttore' => $data2['id_istruttore'],
            'id_stanza' => $data2['id_stanza'],
        ]);
    }


    /**
     * Salvataggio nuova lezione nella tabella Lezioni:
     * ALLO STESSO ORARIO
     * NELLA STESSA STANZA
     * => ERRATA
     */
    public function testLezioniStessaStanza_KO_uguali()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Preparazione delle request: due lezioni allo stesso orario nella stessa stanza
        $data1 = [
            'nome' => 'Lezione KO 2',
            'inizio' => '2023-12-31T17:00:00',
            'fine' => '2023-12-31T18:00:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        DB::table('Lezioni')->insert($data1);

        $data2 = [
            'nome' => 'Lezione KO 2',
            'inizio' => '2023-12-31T17:00:00',
            'fine' => '2023-12-31T18:00:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data2);
        $response = $LezioniController->store($request);

        $this->assertTrue($response->isRedirection());
        // Verifica che il controller restituisca il messaggio di errore corretto
        $this->assertTrue(session()->has('errors'));
        $error = session('errors');
        $this->assertArrayHasKey('errore_lezioni_stessa_stanza', session('errors')->getBag('default')->getMessages());

        // Termina la transazione senza committare le modifiche effettuate durante il test
        DB::rollBack();

        // Controlla che i record non siano stati salvati nel database
        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data1['nome'],
            'inizio' => $data1['inizio'],
            'fine' => $data1['fine'],
            'is_bambini' => $data1['is_bambini'],
            'id_istruttore' => $data1['id_istruttore'],
            'id_stanza' => $data1['id_stanza'],
        ]);

        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data2['nome'],
            'inizio' => $data2['inizio'],
            'fine' => $data2['fine'],
            'is_bambini' => $data2['is_bambini'],
            'id_istruttore' => $data2['id_istruttore'],
            'id_stanza' => $data2['id_stanza'],
        ]);
    }

    /**
     * Salvataggio nuova lezione nella tabella Lezioni:
     * INIZIO CONTENUTO IN UN'ALTRA LEZIONE
     * NELLA STESSA STANZA
     * => ERRATA
     */
    public function testLezioniStessaStanza_KO_inizio()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Preparazione delle request: due lezioni allo stesso orario nella stessa stanza
        $data1 = [
            'nome' => 'Lezione KO 1',
            'inizio' => '2023-12-31T17:00:00',
            'fine' => '2023-12-31T18:00:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        DB::table('Lezioni')->insert($data1);

        $data2 = [
            'nome' => 'Lezione KO 2',
            'inizio' => '2023-12-31T17:30:00',
            'fine' => '2023-12-31T18:30:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data2);
        $response = $LezioniController->store($request);

        $this->assertTrue($response->isRedirection());
        // Verifica che il controller restituisca il messaggio di errore corretto
        $this->assertTrue(session()->has('errors'));
        $error = session('errors');
        $this->assertArrayHasKey('errore_lezioni_stessa_stanza', session('errors')->getBag('default')->getMessages());

        // Termina la transazione senza committare le modifiche effettuate durante il test
        DB::rollBack();

        // Controlla che i record non siano stati salvati nel database
        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data1['nome'],
            'inizio' => $data1['inizio'],
            'fine' => $data1['fine'],
            'is_bambini' => $data1['is_bambini'],
            'id_istruttore' => $data1['id_istruttore'],
            'id_stanza' => $data1['id_stanza'],
        ]);

        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data2['nome'],
            'inizio' => $data2['inizio'],
            'fine' => $data2['fine'],
            'is_bambini' => $data2['is_bambini'],
            'id_istruttore' => $data2['id_istruttore'],
            'id_stanza' => $data2['id_stanza'],
        ]);
    }

    /**
     * Salvataggio nuova lezione nella tabella Lezioni:
     * FINE CONTENUTA IN UN'ALTRA LEZIONE
     * NELLA STESSA STANZA
     * => ERRATA
     */
    public function testLezioniStessaStanza_KO_fine()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Preparazione delle request: due lezioni allo stesso orario nella stessa stanza
        $data1 = [
            'nome' => 'Lezione KO 2',
            'inizio' => '2023-12-31T17:30:00',
            'fine' => '2023-12-31T18:30:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        DB::table('Lezioni')->insert($data1);

        $data2 = [
            'nome' => 'Lezione KO 2',
            'inizio' => '2023-12-31T17:30:00',
            'fine' => '2023-12-31T18:30:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data2);
        $response = $LezioniController->store($request);

        $this->assertTrue($response->isRedirection());
        // Verifica che il controller restituisca il messaggio di errore corretto
        $this->assertTrue(session()->has('errors'));
        $error = session('errors');
        $this->assertArrayHasKey('errore_lezioni_stessa_stanza', session('errors')->getBag('default')->getMessages());

        // Termina la transazione senza committare le modifiche effettuate durante il test
        DB::rollBack();

        // Controlla che i record non siano stati salvati nel database
        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data1['nome'],
            'inizio' => $data1['inizio'],
            'fine' => $data1['fine'],
            'is_bambini' => $data1['is_bambini'],
            'id_istruttore' => $data1['id_istruttore'],
            'id_stanza' => $data1['id_stanza'],
        ]);

        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data2['nome'],
            'inizio' => $data2['inizio'],
            'fine' => $data2['fine'],
            'is_bambini' => $data2['is_bambini'],
            'id_istruttore' => $data2['id_istruttore'],
            'id_stanza' => $data2['id_stanza'],
        ]);
    }

    /**
     * Salvataggio nuova lezione nella tabella Lezioni:
     * COMPLETAMENTE CONTENUTA IN UN'ALTRA LEZIONE
     * NELLA STESSA STANZA
     * => ERRATA
     */
    public function testLezioniStessaStanza_KO_contenuta()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Preparazione delle request: due lezioni allo stesso orario nella stessa stanza
        $data1 = [
            'nome' => 'Lezione KO 2',
            'inizio' => '2023-12-31T17:00:00',
            'fine' => '2023-12-31T18:00:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        DB::table('Lezioni')->insert($data1);

        $data2 = [
            'nome' => 'Lezione KO 2',
            'inizio' => '2023-12-31T17:10:00',
            'fine' => '2023-12-31T17:50:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data2);
        $response = $LezioniController->store($request);

        $this->assertTrue($response->isRedirection());
        // Verifica che il controller restituisca il messaggio di errore corretto
        $this->assertTrue(session()->has('errors'));
        $error = session('errors');
        $this->assertArrayHasKey('errore_lezioni_stessa_stanza', session('errors')->getBag('default')->getMessages());

        // Termina la transazione senza committare le modifiche effettuate durante il test
        DB::rollBack();

        // Controlla che i record non siano stati salvati nel database
        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data1['nome'],
            'inizio' => $data1['inizio'],
            'fine' => $data1['fine'],
            'is_bambini' => $data1['is_bambini'],
            'id_istruttore' => $data1['id_istruttore'],
            'id_stanza' => $data1['id_stanza'],
        ]);

        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data2['nome'],
            'inizio' => $data2['inizio'],
            'fine' => $data2['fine'],
            'is_bambini' => $data2['is_bambini'],
            'id_istruttore' => $data2['id_istruttore'],
            'id_stanza' => $data2['id_stanza'],
        ]);
    }

    /**
     * Salvataggio nuova lezione nella tabella Lezioni:
     * CONTIENE COMPLETAMENTE UN'ALTRA LEZIONE
     * NELLA STESSA STANZA
     * => ERRATA
     */
    public function testLezioniStessaStanza_KO_contiene()
    {   
        // Inizia una transazione del database
        DB::beginTransaction();

        // Preparazione delle request: due lezioni allo stesso orario nella stessa stanza
        $data1 = [
            'nome' => 'Lezione KO 2',
            'inizio' => '2023-12-31T17:10:00',
            'fine' => '2023-12-31T17:50:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        DB::table('Lezioni')->insert($data1);

        $data2 = [
            'nome' => 'Lezione KO 2',
            'inizio' => '2023-12-31T17:00:00',
            'fine' => '2023-12-31T18:00:00',
            'is_bambini' => 1,
            'id_istruttore' => 1,
            'id_stanza' => 1,
        ];

        $LezioniController = new LezioniController();

        // Chiama direttamente il metodo store() del LezioniController con la richiesta simulata
        $request = new Request($data2);
        $response = $LezioniController->store($request);

        $this->assertTrue($response->isRedirection());
        // Verifica che il controller restituisca il messaggio di errore corretto
        $this->assertTrue(session()->has('errors'));
        $error = session('errors');
        $this->assertArrayHasKey('errore_lezioni_stessa_stanza', session('errors')->getBag('default')->getMessages());

        // Termina la transazione senza committare le modifiche effettuate durante il test
        DB::rollBack();

        // Controlla che i record non siano stati salvati nel database
        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data1['nome'],
            'inizio' => $data1['inizio'],
            'fine' => $data1['fine'],
            'is_bambini' => $data1['is_bambini'],
            'id_istruttore' => $data1['id_istruttore'],
            'id_stanza' => $data1['id_stanza'],
        ]);

        $this->assertDatabaseMissing(
            'Lezioni', [
            'nome' => $data2['nome'],
            'inizio' => $data2['inizio'],
            'fine' => $data2['fine'],
            'is_bambini' => $data2['is_bambini'],
            'id_istruttore' => $data2['id_istruttore'],
            'id_stanza' => $data2['id_stanza'],
        ]);
    }
}