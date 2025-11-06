@if ($fila!='porcentaje')
    <tr>
        <td class="has-text-right">{{ $leyenda }}</td>
        <td class="has-text-right">${{ $federal }}</td>
        <td class="has-text-right">${{ $estatal }}</td>
        <td class="has-text-right">${{ $total }}</td>
    </tr>
@else
    <tr>
        <td class="has-text-right is-vcentered">{{ $leyenda }}</td>
        <td class="has-text-right">{{ $federal }}%</td>
        <td class="has-text-right">{{ $estatal }}%</td>
        <td class="has-text-right">{{ $total }}%</td>
    </tr>
@endif
