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
    <p>Jeden z naszych administratorów sprawdzając poprawność Twojego przepisu - "{{ $data['recipeName'] }}" - znalazł poważne błędy.
        Z racji wagi owych błędów uznał za konieczne wprowadzenie przez Ciebie pewnych poprawek. Jego sugestie możesz przeczytać poniżej
    </p>
    <br />
    <p>{{ $data['suggestions'] }}</p>
    <br />
    <p> 
        Jeżeli chcesz, aby Twój przepis był dalej widoczny dla pozostałych użytkowników zastosuj 
        sugerowane poprawki, następnie po ponownej weryfikacji przez któregoś z administratorów Twój przepis 
        będzie ponownie dostępny dla reszty użytkowników. Aby wprowadzić zmiany do przepisu musisz się zalogować na swoje konto 
        po czym Twój przepis będzie dostępny dla Ciebie w karcie "Twoje przepisy".
    </p>
    <p>Dziękujemy!</p>
</body>
</html>