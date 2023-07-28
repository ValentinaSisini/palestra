<!DOCTYPE html>
<html>
<head>
    <title>Stanze</title>
</head>
<body>

    <!-- Menu -->
    @include('menu')

    <h1>Stanze</h1>
    <ul>
        @foreach($elenco_stanze as $stanza)
            <li><b>{{ $stanza->nome }}</b><br>
                {{ $stanza->descrizione }}
            </li>
        @endforeach
    </ul>
</body>
</html>
