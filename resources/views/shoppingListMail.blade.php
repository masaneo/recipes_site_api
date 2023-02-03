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
    <p>Przesyłamy Ci twoją listę zakupów</p>
    <p>
        <ul>
            @foreach ($data['shoppingList'] as $item)
                <li>{{ $item['amount'] . " " . $item['unit'] . " " . $item['name'] }}</li>
            @endforeach
        </ul>
    </p>
    <p>Udanych zakupów!</p>
</body>
</html>