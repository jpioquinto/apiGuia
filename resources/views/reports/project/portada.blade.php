<br><br>
<table width="100%"  style="background:{{$fondo}};margin-top:-20px">
    <tr>
        <td style="min-height:80px;padding:5px;color:#FFF!important;">
            <center><h1 class="h1-portada">{!! $unidadSEDATU !!}</h1></center>
        </td>
    </tr>
</table>
<h3 class="vertienteProyecto" style="text-transform:uppercase;"> {{ $descProyecto }} </h3>
<h3 class="vertienteProyecto" style="text-transform:uppercase;margin-top:-5px;font-size:22px;"> {{ $entidad }} </h3>
<div style="width:100%;">
    <img src="{{ $imgPortada }}" style="width:50%;margin:0 auto;margin-left:25%;margin-top:10px;">
</div>
<div style="margin-top:35px;width:100%;border-style:none;">
    <div style="text-align:right;" class="fecha-elaboracion">Elaboración: {{ $creacion}} </div>
    <div style="text-align:right;" class="fecha-elaboracion">Ultima Modificación: {{ $ultimaModificacion }} </div>
    <div style="text-align:right;" class="fecha-elaboracion">Emisión: {{ $emision }} </div>
    <div style="text-align:right;" class="version" id="version" name="version">Versión: {{ $version }} </div>
</div>
