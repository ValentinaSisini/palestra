<!DOCTYPE html>
<html>
<head>
    <title>Lezioni</title>
</head>
<body>

    <!-- Menu -->
    @include('menu')

    <h1>Lezioni</h1>

    <!-- Form inserimento nuova lezione -->
    <h2>Nuova lezione</h2>
    <form action="{{ url('/aggiungi-lezione') }}" method="post">
        @csrf
        
        <label for="nome_lezione">Nome lezione</label>
        <input type="text" name="nome_lezione" id="nome_lezione">

        <br><br>

        <label for="istruttore">Istruttore:</label>
        <select name="istruttore" id="istruttore">
            @foreach ($istruttori as $istruttore)
                <option value="{{ $istruttore->id }}">{{ $istruttore->nome }} {{ $istruttore->cognome }}</option>
            @endforeach
        </select>

        <br><br>

        <label for="stanza">Stanza:</label>
        <select name="stanza" id="stanza">
            @foreach ($stanze as $stanza)
                <option value="{{ $stanza->id }}">{{ $stanza->nome }}</option>
            @endforeach
        </select>

        <br><br>

        <label for="inizio">Inizio</label>
        <input type="datetime-local" name="inizio" id="inizio">

        &nbsp;&nbsp;&nbsp;&nbsp;

        <label for="fine">Fine</label>
        <input type="datetime-local" name="fine" id="fine">

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
    <h2>Elenco lezioni</h2>
    <ul>
        @foreach($elenco_lezioni as $lezione)
        <li><b>{{ $lezione->nome_stanza }}: <u>{{ $lezione->nome_lezione }}</u></b><br>
            istruttore: {{ $lezione->nome_istruttore }}<br>
            orario: {{ $lezione->inizio }} - {{ $lezione->fine }}
            <br>
            <a href="{{ route('edit.lezione', $lezione->id) }}">Modifica</a>

             <!-- Form di cancellazione -->
             <form action="{{ route('destroy.lezione', $lezione->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Cancella</button>
            </form>
            <br><br>
        </li>
        @endforeach
    </ul>
</body>
</html>
