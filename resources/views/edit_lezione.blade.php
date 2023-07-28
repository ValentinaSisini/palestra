<!DOCTYPE html>
<html>
<head>
    <title>Lezioni</title>
</head>
<body>

    <!-- Menu -->
    @include('menu')

    <h1>Modifica lezione</h1>

    <form action="{{ route('update.lezione', $lezione->id) }}" method="POST">
        @csrf
        @method('PUT')
    
        <!-- Visualizza i campi del record da modificare nel form -->
        <label for="titolo">Nome:</label>
        <input type="text" name="nome_lezione" value="{{ $lezione->nome }}">
        
        <br><br>

        <label for="inizio">Inizio</label>
        <input type="datetime-local" name="inizio" id="inizio" value="{{ $lezione->inizio }}">

        &nbsp;&nbsp;&nbsp;&nbsp;

        <label for="fine">Fine</label>
        <input type="datetime-local" name="fine" id="fine" value="{{ $lezione->fine }}">

        <br><br>
    
        <button type="submit">Aggiorna</button>
    </form>
    

</body>
</html>
