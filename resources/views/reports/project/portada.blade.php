<br><br>
<table width="100%"  style="background:{{$fondo}};margin-top:-20px">
    <tr>
        <td style="min-height:80px;padding:5px;text-align:center;">
            <h1 class="h1-portada">{!! $unidadSEDATU !!}</h1>
        </td>
    </tr>
</table>
<h3 class="vertiente-proyecto"> {{ $descProyecto }} </h3>
<h4 class="vertiente-proyecto" style="margin-top:-5px;font-size:22px;"> {{ $entidad }}</h4>
<div style="width:100%;">
    <img src="{{ $imgPortada }}" style="width:50%;height:335px;margin:0 auto;margin-left:25%;margin-top:10px;" />
</div>
<div style="margin-top:15px;width:100%;font-size:8.7pt;">
    <p style="text-align:right;" class="fecha-elaboracion">Elaboración: {{ $creacion}} </p>
    <p style="text-align:right;" class="fecha-elaboracion">Ultima Modificación: {{ $ultimaModificacion }} </p>
    <p style="text-align:right;" class="fecha-elaboracion">Emisión: {{ $emision }} </p>
    <p style="text-align:right;" class="version">Versión: {{ $version }} </p>
</div>
