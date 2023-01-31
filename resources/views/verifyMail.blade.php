<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$data['title']}}</title>
</head>
<body>
    <p>{{ $data['body'] }}</p>
    <a href="{{ $data['url'] }}"> Naciśnij tutaj aby zweryfikować adres e-mail</a>
    <p>Dziękujemy</p>
</body>
</html>