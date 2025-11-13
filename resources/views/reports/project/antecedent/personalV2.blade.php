<div class="content">
    <h5>Personal por perfil profesional</h5>
    <div class="table-container">
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth tabla-informativa">
            <thead class="has-background-grey-dark has-text-centered">
                <tr>
                    <th class="has-text-white center">Categoría</th>
                    <th class="has-text-white center">Confianza / Estructura / Sindicalizado / Base / Comisionado</th>
                    <th class="has-text-white center">Honorarios / Eventual / Otro</th>
                    <th class="has-text-white center">Cantidad</th>
                </tr>
            </thead>
            <tbody class="has-text-centered">
                @foreach ($datos as $dato)
                    @php
                        $resaltar = $loop->last ? 'texto-resaltado' : 'texto-normal';
                    @endphp
                    <tr>
                        <td class="{{ $resaltar }}">{{ $dato->categoria }}</td>
                        <td class="{{ $resaltar }}">{{ $dato->confianza }}</td>
                        <td class="{{ $resaltar }}">{{ $dato->honorarios }}</td>
                        <td class="{{ $resaltar }}">{{ $dato->cantidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

