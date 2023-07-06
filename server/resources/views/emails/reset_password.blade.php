<!DOCTYPE html>
<html>
<head>
    <title>Resetowanie hasła</title>
</head>
<body>
    <h1>GymTracker - Resetowanie hasła</h1>
    <p>Witaj,</p>
    <p>Otrzymujesz tę wiadomość, ponieważ otrzymaliśmy prośbę o zresetowanie hasła dla Twojego konta.</p>
    <p>Kliknij poniższy przycisk, aby zresetować hasło:</p>
    <a href="{{ config('app.url').'/przypomnienie-hasla/'.$hash }}" target="_blank">Resetuj hasło</a>
    <p>Jeśli nie złożyłeś prośby o zresetowanie hasła, nie są wymagane żadne dodatkowe działania.</p>
    <p>Dziękujemy!</p>
</body>
</html>