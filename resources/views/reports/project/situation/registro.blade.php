<div class="container">
    <div class="table-container">
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth tabla-informativa">
            <thead class="has-background-grey-dark">
                <tr>
                    <th colspan="4" class="has-text-centered has-text-white-ter">
                        Tabla comparativa de avance y estimación
                    </th>
                </tr>
                <tr>
                    <th class="has-text-white-ter">Componente</th>
                    <th class="has-text-centered has-text-white-ter">Modelo Integral de Registro Público de la Propiedad SEDATU</th>
                    <th class="has-text-centered has-text-white-ter">Diagnóstico {{ $anio }}</th>
                    <th class="has-text-centered has-text-white-ter">Estimación de avance {{ $anioProyecto }}</th>
                </tr>
            </thead>
            <tbody>{!! $filas !!}</tbody>
            <tfoot>{!! $totales !!}</tfoot>
        </table>
    </div>
</div>
