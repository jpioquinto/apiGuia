<table class="tabla-actividades">
    <thead>
      <tr>
        <th style="width:20%;">Actividad</th>
        <th style="width:32%;">Descripción</th>
        <th style="width:8%;">Entregables</th>
        <th style="width:7%;">Medida</th>
        <th style="width:6%;">Cantidad</th>
        <th style="width:9%;">Costo Unitario</th>
        <th style="width:8%;">IVA</th>
        <th style="width:10%;">Total</th>
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
                    <td colspan="7" style="font-weight: bold;text-align:right;font-size:9pt;">Total:</td>
                    <td style="font-weight: bold;text-align:right;font-size:9pt;">{{$total}}</td>
                </tr>
            </tfoot>
        @endif
    </tbody>
</table>
