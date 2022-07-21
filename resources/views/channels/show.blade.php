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

  <h1>Programmazione per {{$channel->name}} per oggi</h1>

  <div class="card">
    <table class="table">
      <thead>
      <tr>
        <th>ID</th>
        <th></th>
        <th>Ora inizio</th>
        <th>Titolo</th>
        <th>Categoria</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      @foreach($channel->programs as $program)
        <tr>
          <td>{{ $program->id }}</td>
          <td><img class="img-thumbnail" style="width: 80px" src="{{ $program->thumbnail }}"></td>
          <td>{{ $program->start->format("H:i") }}</td>
          <td>{{ $program->title }}</td>
          <td>{{ $program->category }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

</div>
</body>
</html>
