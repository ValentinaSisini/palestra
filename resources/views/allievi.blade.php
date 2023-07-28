<!DOCTYPE html>
<html>
<head>
    <title>Allievi</title>
</head>
<body>

    <!-- Menu -->
    @include('menu')

    <h1>Allievi</h1>

    <!-- Form inserimento nuova lezione -->
    <h2>Nuovo allievo</h2>
    <form action="{{ url('/aggiungi-allievo') }}" method="post">
        @csrf
        
        <label for="nome">Nome</label>
        <input type="text" name="nome" id="nome">

        <br><br>

        <label for="cognome">Cognome</label>
        <input type="text" name="cognome" id="cognome">    

        <br><br>

        <label for="email">Email</label>
        <input type="text" name="email" id="email">  

        <br><br>

        <label for="cellulare">Cellulare</label>
        <input type="number" name="cellulare" id="cellulare">  
    
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

    
    <!-- Elenco allievi -->
    <h2>Elenco lezioni</h2>
    <ul>
        @foreach($elenco_allievi as $allievo)
        <li><b>{{ $allievo->nome }} {{ $allievo->cognome }}</b><br>
            email: {{ $allievo->email }}<br>
            cellulare: {{ $allievo->cellulare }}
            <br><br>
        </li>
        @endforeach
    </ul>
</body>
</html>
