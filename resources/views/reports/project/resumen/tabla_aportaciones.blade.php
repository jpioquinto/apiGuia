<table class="table is-bordered is-striped is-narrow is-hoverable" id="tabla-aportaciones">
    <thead class="has-background-grey-dark">
        <tr>
            <th class="has-text-centered is-vcentered has-text-white-ter" colspan="4">
                DISTRIBUCIÓN DEL RECURSO PARA LA EJECUCIÓN DEL PROYECTO
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td><td colspan="2" class="has-text-weight-medium has-text-grey-dark has-text-centered">${{ $gTotal['total'] }}</td><td class="has-text-brown">Monto total</td>
        </tr>
        <tr class="has-text-weight-medium has-text-grey-dark">
            <td></td><td class="has-background-warning has-text-centered">
                ${{ $gTotal['federal'] }}
            </td><td class="has-text-centered">${{ $gTotal['estatal'] }}</td><td>Aportaciones totales</td>
        </tr>
        <tr>
            <td class="has-background-warning has-text-weight-medium has-text-grey-dark has-text-right">uno al millar {{ $millar['total'] }}</td><td class="has-text-brown has-text-centered">{{ $porcentaje['federal'] }}%</td><td class="has-text-brown has-text-centered">{{ $porcentaje['estatal'] }}%</td><td class="has-text-brown">Porcentajes de aportación</td>
        </tr>
        <tr class="has-text-weight-medium has-text-grey-dark">
            <td></td><td class="has-background-warning has-text-centered">${{ $total['federal'] }}</td><td class="has-text-centered">${{ $total['estatal'] }}</td><td>Aportaciones totales para la ejecución del Proyecto</td>
        </tr>
        <tr class="has-text-info-dark">
            <td></td><td colspan="2" class="has-text-centered">${{ $total['total'] }}</td><td class="has-text-info-dark">Monto para la ejecución del Proyecto</td>
        </tr>
        <tr class="has-text-brown ">
            <td></td><td class="has-text-centered">{{ $porcFactura['federal'] }}%</td><td class="has-text-centered">{{ $porcFactura['estatal'] }}%</td><td>Porcentaje para el pago de facturas</td>
        </tr>
    </tbody>
    <tfoot>
        <tr class="has-text-weight-medium has-text-black">
            <td></td><td class="has-text-centered">FEDERAL</td><td class="has-text-centered">ESTATAL</td><td></td>
        </tr>
    </tfoot>
</table>
