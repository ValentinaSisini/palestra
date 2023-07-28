<!DOCTYPE html>
<html>
<head>
    <title>Lezioni</title>
</head>
<body>

    <!-- Menu -->
    @include('menu')

    <h1>Istruttori</h1>
    <ul>
        @foreach($elenco_istruttori as $istruttore)
        <li><b>{{ $istruttore->nome }} {{ $istruttore->cognome }}</b><br>
            {{ $istruttore->descrizione }}
        </li>
        @endforeach
    </ul>
</body>
</html>
