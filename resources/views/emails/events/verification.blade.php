<!DOCTYPE html>
<html>

<head>
    <title>Verifique seu E-mail</title>
</head>

<body>
    <h1>Olá, {{ $participant->name }}</h1>
    <p>Para confirmar sua inscrição no evento <strong>{{ $participant->event->title }}</strong>, utilize o código
        abaixo:</p>

    <h2 style="padding: 10px; background-color: #f4f4f4; display: inline-block;">
        {{ $participant->verification_code }}
    </h2>

    <p>Se você não solicitou esta inscrição, por favor, ignore este e-mail.</p>
</body>

</html>