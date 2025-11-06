<h3>{{$indice}}.{{$orden}} Componente: {{ $nombre }}</h3>
<h4>{{$indice}}.{{$orden}}.1 Situación actual</h4>
<div class="text" style="{{$fontSize}}">{!!$situacion!!}</div>
<h4>{{$indice}}.{{$orden}}.2 Objetivos y alcances</h4>
@foreach ($objetivos as $value)
    <h5>{{$indice}}.{{$orden}}.2.{{$value['orden']}} {{$value['objetivo']}}</h5>
    {!!$value['alcance']!!}
@endforeach
<h4>{{$indice}}.{{$orden}}.3 Actividades a realizar en {{$anio}}</h4>
{!! $vistaTablaActividades !!}
@isset($vistaOficinasRPP)
    {!!$vistaOficinasRPP!!}
@endisset
<h4>{{$indice}}.{{$orden}}.4 Estrategia de desarrollo</h4>
<div style="{{$fontSize}}">{!!$estrategia!!}</div>
