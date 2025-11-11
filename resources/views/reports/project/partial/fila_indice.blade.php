@switch($nivel)
    @case(1)
        <tr>
            <td colspan="3" style="font-size:14px;width:70%;border-bottom:1px dotted #c8c8c8;padding-right:200px;">{{$seccion}}</td>
            <td style="font-size:14px;width:25%;border-bottom:1px dotted #c8c8c8;"></td>
            <td style="font-size:14px;width:5%;text-align:center;margin-bottom:0;" class="text-center">{{$num}}</td>
        </tr>
        @break
    @case(2)
        <tr>
            <td style="font-size:14px;width:5%;"></td>
            <td colspan="2" style="font-size:14px;padding-left:-200px;width:65%;margin-left:-50px;border-bottom:1px dotted #c8c8c8;">{{$seccion}}</td>
            <td style="font-size:14px;width:25%;border-bottom:1px dotted #c8c8c8;"></td>
            <td style="font-size:14px;width:5%;text-align:center;margin-bottom:0;" class="text-center">{{$num}}</td>
        </tr>
        @break
    @default
        <tr>
            <td style="font-size:14px;width:5%;"></td>
            <td style="font-size:14px;width:5%;"></td>
            <td colspan="2" style="font-size:14px;width:40%;border-bottom:1px dotted #c8c8c8;padding-left:-240px;margin-left:-50px;">{{$seccion}}</td>
            <td style="font-size:14px;width:5%;text-align:center;margin-bottom:0;" class="text-center">{{$num}}</td>
        </tr>
@endswitch
