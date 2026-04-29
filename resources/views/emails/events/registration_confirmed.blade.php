<!DOCTYPE html>
<html>

<head>
    <title>Inscrição Confirmada</title>
</head>

<body>
    <h1>Olá, {{ $participant->name }}</h1>
    <p>Sua inscrição no evento <strong>{{ $participant->event->title }}</strong> foi confirmada!</p>
    <p>Data: {{ $participant->event->date->format('d/m/Y H:i') }}</p>
    <p>Local: {{ $participant->event->location }}</p>
    <br>
    <p>Obrigado por se inscrever.</p>
</body>

</html>