<table class="tabla-actividades">
    <thead>
      <tr  style="text-align:center!important;">
        <th align="center" style="width:20%;text-align:center!important;">Actividad</th>
        <th align="center" style="width:32%!important;text-align:center!important;">Descripción</th>
        <th align="center" style="width:8%;text-align:center!important;">Entregables</th>
        <th align="center" style="width:7%;text-align:center!important;">Medida</th>
        <th align="center" style="width:6%;font-size:11px;text-align:center!important;">Cantidad</th>
        <th align="center" style="width:9%;text-align:center!important;">Costo Unitario</th>
        <th align="center" style="width:8%;text-align:center!important;">IVA</th>
        <th align="center" style="width:10%;text-align:center!important;">Total</th>
      </tr>
    </thead>
    <tbody>
        {!! $filas !!}
        @empty($filas)
            <tr>
                <td colspan="8">No se capturaron actividades</td>
            </tr>
        @endempty
        @if (!empty($filas))
            <tfoot>
                <tr>
                    <td colspan="7" align="right"><strong>Total:</strong></td>
                    <td align="right"><strong>${{$total}}</strong></td>
                </tr>
            </tfoot>
        @endif
    </tbody>
</table>
