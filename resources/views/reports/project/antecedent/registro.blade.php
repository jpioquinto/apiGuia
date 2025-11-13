<div class="content">
    <h4 style="display: none;">2.1.{{$subind}} Registro público de la propiedad</h4>
    <section class="antecedente-catastral">
        <h5>Oficinas registrales</h5>
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth tabla-informativa">
            <thead class="has-background-grey-dark has-text-centered">
                <tr>
                    <th class="has-text-white center">Concepto</th>
                    <th class="has-text-white center">Cantidad</th>
                </tr>
            </thead>
            <tbody class="has-text-centered">
                @foreach ($oficinas as $oficina)
                    @php
                        $resaltar = $loop->last ? 'texto-resaltado' : 'texto-normal';
                    @endphp
                    <tr>
                        <td class="{{ $resaltar }}">{{ $oficina['concepto'] }}</td>
                        <td class="{{ $resaltar }}">{{ $oficina['cantidad'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="has-text-right is-size-7 is-italic">
            <strong>Fuente: </strong>Sistema Integral para la Gestión de Información Registral y Catastral - Diagnóstico {{ $anioDiagnostico }}
        </p>
        <h5>Situación del acervo documental</h5>
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth tabla-informativa">
            <thead class="has-background-grey-dark has-text-centered">
                <tr>
                    <th class="has-text-white">Concepto</th>
                    <th class="has-text-white">Cantidad</th>
                </tr>
            </thead>
            <tbody class="has-text-centered">
                @foreach ($acervo as $value)
                    <tr>
                        <td>{{ $value['concepto'] }}</td>
                        <td>{{ $value['cantidad'] }}</td>
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
        <h5>Presupuesto y promedio de ingresos anuales</h5>
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth tabla-informativa">
            <tbody class="has-text-centered">
                {!! $ingreso !!}
            </tbody>
        </table>
        <p class="has-text-right is-size-7 is-italic">
            <strong>Fuente: </strong>Sistema Integral para la Gestión de Información Registral y Catastral - Diagnóstico {{ $anioDiagnostico }}
        </p>
    </section>
</div>
