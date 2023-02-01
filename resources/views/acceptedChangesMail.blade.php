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
    <p>Z przyjemnością pragniemy poinformować Cię, że zmiany w twoim przepisie - "{{ $data['recipeName'] }}" - zostały zaakceptowane 
        przez administratora. Twój przepis jest znów widoczny dla wszystkich użytkowników naszego serwisu!
    </p>
    <p>Dziękujemy!</p>
</body>
</html>