<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="container">

  <h1>Lista Canali</h1>

  <div class="card">
    <table class="table">
      <thead>
      <tr>
        <th>ID</th>
        <th>Logo</th>
        <th>Nome</th>
        <th>Numero</th>
        <th>Categoria</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      @foreach($channels as $channel)
        <tr>
          <td>{{ $channel->id }}</td>
          <td><img class="img-thumbnail" style="width: 80px" src="{{$channel->logo_url_color}}"></td>
          <td>{{ $channel->name }}</td>
          <td>{{ $channel->channel_number }}</td>
          <td>{{ $channel->type }}</td>
          <td><a href="{{ route('channels.show', $channel->id)  }}">Vedi programmi</a></td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

</div>
</body>
</html>
