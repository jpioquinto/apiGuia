<div class="table-container" style="margin-top:15px;">
    <p>{{$nombre}}</p>
    <table class="table is-bordered table is-striped">
        <thead class="has-background-grey-dark">
            <tr>
                <th rowspan="2" class="has-text-centered is-vcentered has-text-white-ter">Oficina registral</th>
                <th rowspan="2" class="has-text-centered is-vcentered has-text-white-ter">Acervo existente</th>
                <th rowspan="2" class="has-text-centered is-vcentered has-text-white-ter">Acervo digitalizado</th>
                <th rowspan="2" class="has-text-centered is-vcentered has-text-white-ter">Porcentaje de digitalización</th>
                <th colspan="2" class="has-text-centered is-vcentered has-text-white-ter">Pendiente de digitalizar</th>
            </tr>
            <tr>
                <th class="has-text-centered is-vcentered has-text-white-ter">Libros / Legajos</th>
                <th class="has-text-centered is-vcentered has-text-white-ter">Número de imágenes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($acervo as $oficina)
            <tr>
                <td>{{$oficina['concepto']}}</td>
                <td class="has-text-centered is-vcentered">{{ $oficina['acervo_existe'] }}</td>
                <td class="has-text-centered is-vcentered">{{ $oficina['acervo_digitalizado'] }}</td>
                <td class="has-text-centered is-vcentered">{{ $oficina['porc_digitalizado'] }}%</td>
                <td class="has-text-centered is-vcentered">{{ $oficina['libros_legajos'] }}</td>
                <td class="has-text-centered is-vcentered">{{ $oficina['num_imagenes'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="has-background-white-ter has-text-weight-medium">
                <td class="is-vcentered">{{$total['concepto']}}</td>
                <td class="has-text-centered is-vcentered">{{ $total['existente'] }}</td>
                <td class="has-text-centered is-vcentered">{{ $total['digitalizado'] }}</td>
                <td class="has-text-centered is-vcentered"></td>
                <td class="has-text-centered is-vcentered">{{ $total['librosLegajos'] }}</td>
                <td class="has-text-centered is-vcentered">{{ $total['numImagenes'] }}</td>
            </tr>
        </tfoot>
    </table>
</div>
