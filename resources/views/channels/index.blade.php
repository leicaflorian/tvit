@extends("layouts.app")

@section("page_content")
  <div class="container">
    <h1 class="mb-3">Lista Canali</h1>

    <div class="card">
      <table class="table">
        <thead>
        <tr>
          <th></th>
          <th>Nome</th>
          <th>Numero DTT</th>
          <th>Gruppo</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($channels as $channel)
          <tr>
            <td class="text-center"><img class="" style="width: 80px" src="{{$channel->logo_url_light}}"></td>
            <td>
              <h5 class="mb-0">{{ $channel->name }}</h5>
              <div class="channel-programs">
                <small class="channel-program-now">
                  <strong>{{$channel->nowOnAir["start"]->format("H:i")}}</strong> - {{$channel->nowOnAir["title"]}}
                </small>

                @if($channel->nextOnAir)
                  <small class="channel-program-next">
                    <strong>{{$channel->nextOnAir["start"]->format("H:i")}}</strong> - {{$channel->nextOnAir["title"]}}
                  </small>
                @endif
              </div>

            </td>
            <td>{{ $channel->dtt_num }}</td>
            <td>{{ Str::title($channel->group) }}</td>
            <td>
              <a class="btn btn-link" href="{{ route('channels.show', $channel->tvg_slug)  }}">Vedi tutti i programmi</a>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>

@endsection
