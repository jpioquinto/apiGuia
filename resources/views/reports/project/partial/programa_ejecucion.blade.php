<table>
    <thead style="border:0px;">
        <tr style="border-bottom: 1px solid #000000;background: #4CAF50;font-size: 11px;">
            <th rowspan="2">Componente</th>
            <th rowspan="2">Actividad</th>
            <th colspan="{{$totalMeses}}" style="text-align:center;">Meses</th>
        </tr>
        <tr style="border-bottom: 1px solid #000000;background: #4CAF50;font-size: 10px;">
            <th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th><th>9</th><th>10</th><th>11</th><th>12</th>
            {!! $meses !!}
        </tr>
    </thead>
    <tbody>
        {!! $filas !!}
    </tbody>
</table>
