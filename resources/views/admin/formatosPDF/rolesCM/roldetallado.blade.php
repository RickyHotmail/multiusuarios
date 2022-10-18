@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">Reporte de Empleados</td></tr>
        <tr><td colspan="2" class="centrar letra15 borde-gris">FECHA :  {{ $desde }}  AL {{ $hasta }} </td></tr>
    @endsection

    <table style="white-space: normal!important;" id="tabladetalle">
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra12">
                <th colspan="2"></th>
                @if($ingre>0)
            
                    <th colspan="{{$ingre+1}}">Ingresos</th>  
                @endif
                @if($egre>0)
                    <th colspan="{{$egre+1}}">Egresos</th> 
                @endif
                <th ></th> 
                @if($bene>0)
                    <th colspan="{{$bene}}">Beneficios</th> 
                @endif
                @if($otros>0)
                    <th colspan="{{$otros}}">Otros</th> 
                @endif
                <th ></th> 
            </tr>
            
            <tr style="border: 1px solid black;" class="centrar letra12">
                <th style="font-size: 7px;">Cedula</th>
                <th style="font-size: 7px;">Nombre</th>
                @foreach($rubros as $rubro)
                    @if($rubro->rubro_tipo =='2')
                    <th style="font-size: 5px;">{{ $rubro->rubro_descripcion}}</th>
                    @endif 
                @endforeach
                <th style="font-size: 7px;"> Tot. Ingr.</th>
                @foreach($rubros as $rubro)
                    @if($rubro->rubro_tipo =='1')
                    <th  style="font-size: 5px;">{{ $rubro->rubro_descripcion}}</th>  
                    @endif 
                @endforeach
                <th style="font-size: 7px;">Tot. Egre.</th>  
                <th style="font-size: 7px;">Total</th>
                @foreach($rubros as $rubro) 
                    @if($rubro->rubro_tipo =='3')
                    <th  style="font-size: 5px;">{{ $rubro->rubro_descripcion}}</th> 
                    @endif 
                @endforeach
                @foreach($rubros as $rubro) 
                    @if($rubro->rubro_tipo =='4')
                        <th  style="font-size: 5px;">{{ $rubro->rubro_descripcion}}</th>  
                    @endif  
                @endforeach
                <th style="font-size: 7px;">Tot. Pagar</th>                  
            </tr>
        </thead>
        <tbody>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)   
                    <tr style="font-size: 7px; border: 1px solid black;">
                        <td style="border: 1px solid black;">{{ $datos[$i]['cedula'] }}</td>
                        <td style="border: 1px solid black;">{{ $datos[$i]['nombre'] }}</td>
                        @foreach($rubros as $rubro)
                            @if($rubro->rubro_tipo =='2')
                                <td style="border: 1px solid black;">{{number_format($datos[$i][$rubro->rubro_nombre],2)}}</td>  
                            @endif 
                        @endforeach
                        <td style="border: 1px solid black;">{{ number_format($datos[$i]['totalingresos'],2) }}</td>
                        @foreach($rubros as $rubro)
                            @if($rubro->rubro_tipo =='1')
                                <td style="border: 1px solid black;">{{number_format($datos[$i][$rubro->rubro_nombre],2)}}</td>  
                            @endif 
                        @endforeach
                        <td style="border: 1px solid black;">{{ number_format($datos[$i]['totalegresos'],2) }}</td>
                        <td style="border: 1px solid black;">{{ number_format($datos[$i]['totalingresos']-$datos[$i]['totalegresos'],2) }}</td>
                        @foreach($rubros as $rubro) 
                            @if($rubro->rubro_tipo =='3')
                                <?php $vari='E'.$rubro->rubro_nombre; ?>
                                <td  @if(isset($datos[$i][$vari]))  @if($datos[$i][$vari]=='Pagado')  style="background:  #70B1F7;" @endif  @if($datos[$i][$vari]=='Acumulado')  style="background:  #B1E2DD;" @endif @endif>  {{number_format($datos[$i][$rubro->rubro_nombre],2)}}</td>
                            @endif  
                        @endforeach
                        @foreach($rubros as $rubro) 
                            @if($rubro->rubro_tipo =='4')
                                <td style="border: 1px solid black;">{{ number_format($datos[$i][$rubro->rubro_nombre],2)}}</td>
                            @endif  
                        @endforeach
                        <td style="border: 1px solid black;">{{ number_format($datos[$i]['total'],2)}}</td>
                        
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
   
<style>
    tbody {
   border-top: 1px solid #000;
   border-bottom: 1px solid #000;
}
tbody th, tfoot th {
   border: 0;
}
th.name {
   width: 25%;
}
th.location {
   width: 20%;
}
th.lasteruption {
   width: 30%;
}
th.eruptiontype {
   width: 25%;
}
tfoot {
   text-align: center;
   color: #555;
   font-size: 0.8em;
}
</style>
@endsection