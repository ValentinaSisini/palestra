<!DOCTYPE html>
<html>
<head>
    <title>Prenotazioni</title>
</head>
<body>

    <!-- Menu -->
    @include('menu')

    <h1>Prenotazioni</h1>

    <!-- Form inserimento nuova lezione -->
    <h2>Nuova prenotazione</h2>
    <form action="{{ url('/aggiungi-prenotazione') }}" method="post">
        @csrf

        <label for="allievo">Istruttore:</label>
        <select name="allievo" id="allievo">
            @foreach ($allievi as $allievo)
                <option value="{{ $allievo->id }}">{{ $allievo->nome }} {{ $allievo->cognome }}</option>
            @endforeach
        </select>

        <br><br>

        <label for="lezione">Lezione:</label>
        <select name="lezione" id="lezione">
            @foreach ($lezioni as $lezione)
                <option value="{{ $lezione->id }}">{{ $lezione->nome }}</option>
            @endforeach
        </select>

        <br><br>
    
        <button type="submit">Aggiungi</button>
    </form>

    <!-- Eventi server di successo ed errore -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-success">
            {{ session('error') }}
        </div>
    @endif

    
    <!-- Elenco lezioni con tasto modifica -->
    <h2>Elenco prenotazioni</h2>
    <ul>
        @foreach($elenco_prenotazioni as $prenotazione)
        <li><b>{{ $prenotazione->nome_allievo }} {{ $prenotazione->cognome_allievo }}</b>: 
            {{ $prenotazione->nome_lezione }}<br>
            orario: {{ $prenotazione->inizio }} - {{ $prenotazione->fine }}
        </li>
        @endforeach
    </ul>
</body>
</html>
