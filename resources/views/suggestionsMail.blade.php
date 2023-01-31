<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$data['title']}}</title>
</head>
<body>
    <p>Witaj {{ $data['username'] }}</p>
    <p>Jeden z naszych administratorów sprawdzając poprawność Twojego przepisu znalazł pewne drobiazgi, 
        które uznał za możliwe do poprawienia. Jego sugestie możesz przeczytać poniżej
    </p>
    <br />
    <p>{{ $data['suggestions'] }}</p>
    <br />
    <p>Jeżeli podzielasz jego zdanie i uważasz, że po zastosowaniu tych zmian 
        Twój przepis może być przyjaźniejszy dla innych użytkowników to prosimy Cię o zastosowanie zmian. 
        Możesz to uczynić w karcie "Twoje przepisy" dostępnej po zalogowaniu się!
    </p>
    <p>Dziękujemy!</p>
</body>
</html>