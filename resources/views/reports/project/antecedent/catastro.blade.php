<div class="content">
    <h4>2.1.{{$subind}} Catastro</h4>
    <section class="antecedente-catastral">
        <h5>Oficinas catastrales</h5>
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth tabla-informativa">
            <thead class="has-background-grey-dark has-text-centered">
                <tr>
                    <th class="has-text-white center">Concepto</th>
                    <th class="has-text-white center">Cantidad</th>
                </tr>
            </thead>
            <tbody class="has-text-centered">
                @foreach ($oficinas as $oficina)
                    <tr>
                        <td>{{ $oficina['concepto'] }}</td>
                        <td>{{ $oficina['cantidad'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="has-text-right is-size-7 is-italic">
            <strong>Fuente: </strong>Sistema Integral para la Gestión de Información Registral y Catastral - Diagnóstico {{ $anioDiagnostico }}
        </p>
        <h5>Predios y cuentas catastrales</h5>
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth tabla-informativa">
            <thead class="has-background-grey-dark has-text-centered">
                <tr>
                    <th class="has-text-white">Concepto</th>
                    <th class="has-text-white">Cantidad</th>
                </tr>
            </thead>
            <tbody class="has-text-centered">
                @foreach ($predios as $predio)
                    <tr>
                        <td>{{ $predio['concepto'] }}</td>
                        <td>{{ $predio['cantidad'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="has-text-right is-size-7 is-italic">
            <strong>Fuente: </strong>Sistema Integral para la Gestión de Información Registral y Catastral - Diagnóstico {{ $anioDiagnostico }}
        </p>
        <h5>Cobertura de la cartografía en Km<sup>2</sup></h5>
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth tabla-informativa">
            <thead class="has-background-grey-dark has-text-centered">
                <tr>
                    <th class="has-text-white">Concepto</th>
                    <th class="has-text-white">Cantidad</th>
                </tr>
            </thead>
            <tbody class="has-text-centered">
                @foreach ($cartografia as $carto)
                    <tr>
                        <td>{{ $carto['concepto'] }}</td>
                        <td>{{ $carto['cantidad'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="has-text-right is-size-7 is-italic">
            <strong>Fuente: </strong>Sistema Integral para la Gestión de Información Registral y Catastral - Diagnóstico {{ $anioDiagnostico }}
        </p>
        {!! $vistaPersonal !!}
        <p class="has-text-right is-size-7 is-italic">
            <strong>Fuente: </strong>Sistema Integral para la Gestión de Información Registral y Catastral - Diagnóstico {{ $anioDiagnostico }}
        </p>
    </section>
</div>
