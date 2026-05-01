<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição Confirmada</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333333;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #008744;
            color: #ffffff;
            text-align: center;
            padding: 20px 0;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .banner {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            display: block;
        }

        .content {
            padding: 30px;
        }

        .content h2 {
            color: #008744;
            margin-top: 0;
        }

        .event-details {
            background-color: #f9f9f9;
            border-left: 4px solid #008744;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }

        .event-details p {
            margin: 8px 0;
            font-size: 15px;
        }

        .footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #777777;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="https://vozesdors.com.br/eventos_api/storage/events/4.jpg" alt="{{ $participant->event->title }}"
            class="banner">

        <div class="header">
            <h1>Inscrição Confirmada</h1>
        </div>

        <div class="content">
            <h2>Olá, {{ $participant->name }}!</h2>
            <p>Sua inscrição no evento <strong>{{ $participant->event->title }}</strong> foi confirmada com sucesso.
                Estamos muito felizes em ter você conosco!</p>

            <div class="event-details">
                <p><strong>📅 Data:</strong> {{ $participant->event->date->format('d/m/Y \à\s H:i') }}</p>
                <p><strong>📍 Local:</strong> {{ $participant->event->location }}</p>
            </div>

            <p>Prepare-se para uma experiência incrível. Caso tenha alguma dúvida, não hesite em nos contatar.</p>

            <p>Obrigado por se inscrever!</p>
        </div>

        <div class="footer">
            <p>Este é um e-mail automático, por favor não responda.</p>
        </div>
    </div>
</body>

</html>