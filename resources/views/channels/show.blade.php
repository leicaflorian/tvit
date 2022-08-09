@extends("layouts.app")
@section("page_content")
  <div class="container">
    <h1 class="mb-3">Programmazione su {{$channel->name}} per <strong>oggi</strong></h1>

    <div class="card">
      <table class="table">
        <thead>
        <tr>
          <th>Ora inizio</th>
          <th>Ora fine</th>
          <th>Durata</th>
          <th>In Onda</th>
          <th>Titolo</th>
          <th>Categoria</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($channel->programs as $program)
          <tr>
            <td>{{ $program->start->format("H:i") }}</td>
            <td>{{ $program->end->format("H:i") }}</td>
            <td>{{ $program->duration }}'</td>
            <td>
              @if($program->onAir)
                <img src="{{asset("assets/live.gif")}}" alt="" style="width: 80px">
              @endif
            </td>
            <td>{{ $program->title }}</td>
            <td>{{ $program->category }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
