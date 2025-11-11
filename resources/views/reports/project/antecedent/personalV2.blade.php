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
                    <tr>
                        <td>{{ $dato->categoria }}</td>
                        <td>{{ $dato->confianza }}</td>
                        <td>{{ $dato->honorarios }}</td>
                        <td>{{ $dato->cantidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

