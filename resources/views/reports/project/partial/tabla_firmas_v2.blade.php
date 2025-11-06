<table class="tablaPrincipal">
<tr><td colspan="6" style="text-align:center;">{{ $institucion }}</td></tr>
</table>
<table style="width:100%;font-size:12pt;">
    <tr>
        <td rowspan="2" style="width:25%;text-align:left;border:1px solid #cacaca;">
            <img src="{{ $qrEmisor }}" style="width:150px;height:150px;margin-left:5%;">
        </td>
        <td style="width:75%;text-align:center;font-size:12pt;border:1px solid #cacaca;">{{ $emisor }}<br>Sello digital del emisor</td>
    </tr>
    <tr>
        <td style="width:75%;text-align:left;border:1px solid #cacaca;font-size:11pt;">{{ $selloEmisor }}</td>
    </tr>
    <tr>
        <td colspan="2" style="height:30px;font-size:8pt;">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" style="text-align:center;border:1px solid #ddd;">{{ $urSEDATU }}</td>
    </tr>
    <tr>
        <td rowspan="2" style="width:25%;text-align:left;border:1px solid #cacaca;">
            <img src="{{ $qrCertificador }}" style="width:150px;height:150px;margin-left:5%;">
        </td>
        <td style="width:75%;text-align:center;font-size:12pt;border:1px solid #cacaca;">{{ $certificador }}<br>Sello digital de certificación</td>
    </tr>
    <tr>
        <td style="width:75%;text-align:left;border:1px solid #cacaca;font-size:11pt;">{{ $selloCertificador }}</td>
    </tr>
    <tr>
        <td colspan="2" style="width:75%;text-align:center;border:1px solid #cacaca;">Cadena original del complemento de certificación</td>
    </tr>
    <tr>
        <td colspan="2" style="width:75%;text-align:left;border:1px solid #cacaca;font-size:11pt;">{{ $cadenaOriginal }}</td>
    </tr>
    <tr>
        <td colspan="2" style="height:30px;font-size:8pt;">* Esta es una representación impresa del Proyecto Ejecutivo de Modernización</td>
    </tr>
</table>
