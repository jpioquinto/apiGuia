<table style="margin-top: 10px !important;">
	<tr>
		<td width="20%"></td>
		<td>
            <table class="table is-bordered is-striped is-narrow is-hoverable tabla-informativa" id="tabla-aportaciones" style="border: 1px solid #efefef;width:100%;">
                <thead class="has-background-grey-dark">
                    <tr>
                        <th class="has-text-centered is-vcentered has-text-white-ter" colspan="4" style="background: #616161 !important;">
                            <h6 style="text-align: center !important;color: #FFFFFF !important;">
                                DISTRIBUCIÓN DEL RECURSO PARA LA EJECUCIÓN DEL PROYECTO
                            </h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td><td colspan="2" class="has-text-weight-medium has-text-grey-dark has-text-centered">${{ $gTotal['total'] }}</td><td class="has-text-brown">Monto total</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="background: #ffc107 !important;">
                            ${{ $gTotal['federal'] }}
                        </td><td class="has-text-centered">${{ $gTotal['estatal'] }}</td>
                        <td>Aportaciones totales</td>
                    </tr>
                    <tr>
                        <td class="has-bg-warning" style="background: #ffc107 !important;">uno al millar {{ $millar['total'] }}</td><td class="has-text-brown has-text-centered">{{ $porcentaje['federal'] }}%</td><td class="has-text-brown has-text-centered">{{ $porcentaje['estatal'] }}%</td><td class="has-text-brown">Porcentajes de aportación</td>
                    </tr>
                    <tr class="has-text-weight-medium has-text-grey-dark">
                        <td></td><td class="has-bg-warning has-text-centered" style="background: #ffc107 !important;">${{ $total['federal'] }}</td><td class="has-text-centered">${{ $total['estatal'] }}</td><td>Aportaciones totales para la ejecución del Proyecto</td>
                    </tr>
                    <tr>
                        <td></td><td colspan="2" class="has-text-centered has-text-info">${{ $total['total'] }}</td><td class="has-text-info">Monto para la ejecución del Proyecto</td>
                    </tr>
                    <tr>
                        <td></td><td class="has-text-centered has-text-brown">{{ $porcFactura['federal'] }}%</td><td class="has-text-centered has-text-brown">{{ $porcFactura['estatal'] }}%</td><td class="has-text-brown">Porcentaje para el pago de facturas</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="has-text-weight-medium has-text-black">
                        <td></td><td class="has-text-centered">FEDERAL</td><td class="has-text-centered">ESTATAL</td><td></td>
                    </tr>
                </tfoot>
            </table>
        </td>
        <td width="20%"></td>
    </tr>
</table>
