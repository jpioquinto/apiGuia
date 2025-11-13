<div class="contenido-resumen">
    <table class="table tabla-resumen">
        <thead class="has-background-grey-dark">
            <tr>
                <th class="has-text-centered is-vcentered has-text-white-ter" rowspan="2">Componentes</th>
                <th class="has-text-centered has-text-white-ter" colspan="2">Distribución del recurso</th>
                <th class="has-text-centered is-vcentered has-text-white-ter" rowspan="2">Total</th>
            </tr>
            <tr>
                <th class="has-text-centered has-text-white-ter">Federal</th>
                <th class="has-text-centered has-text-white-ter">Estatal</th>
            </tr>
        </thead>
        <tbody>
            {!! $filasComponente !!}
        </tbody>
        <tfoot>
            {!! $tfootTotales !!}
        </tfoot>
    </table>

    @if ($anio>2020)
        <div style="margin-top: 10px;">
            {!! $tabla_aportaciones !!}
        </div>
    @endif
    <p class="has-text-justify" style="font-size:9pt;font-weight:bold;font-style:italic;margin:0px;padding:0px;">
        (*) Este {{ $anio<2018 ? 'porcentaje' : 'monto' }} incluye el pago de la vigilancia, inspección, control y evaluación de la ejecución de Programa, conforme a la Ley Federal de Presupuesto y Responsabilidad Hacendaria y su reglamento.
    </p>
    @if (!empty($observaciones))
        <div class="content-observaciones">
            <h5 style="margin-top:30px;">Observaciones del resumen financiero</h5>
            <div class="observaciones-resumen">
                {!! $observaciones !!}
            </div>
        </div>
    @else
        <div class="hide" style="display:none;width:1px;height:1px;"></div>
    @endif
</div>
