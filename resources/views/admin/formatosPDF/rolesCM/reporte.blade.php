@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">Reporte de Empleados</td></tr>
        <tr><td colspan="2" class="centrar letra15 borde-gris">Fecha:  {{ $desde }}  AL {{ $hasta }} </td></tr>
    @endsection
    <table>
        <tr class="letra12">
            <td class="negrita"width="10%">Rubros:</td>
            <td>@if(isset($rubros))
                    @for ($i = 1; $i <= count($rubros); ++$i) 
                    {{ $rubros[$i]}},
                    @endfor
                @endif       
            </td>
          
        </tr>
    </table>
    <br>
    <?php $total=0;?> 
    <table style="white-space: normal!important;" id="tabladetalle">
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra12">
                <th>Cedula</th>
                <th>Nombre</th>
                <th>Total</th>
                
            </tr>
        </thead>
        <tbody>
            @if(isset($datos))
   
                @for ($i = 1; $i <= count($datos); ++$i)   
                    @if($datos[$i]['total']>0)
                    <?php $total=$total+$datos[$i]['total']?>          
                    <tr class=" letra10">
                        <td>{{ $datos[$i]['cedula'] }}</td>
                        <td>{{ $datos[$i]['nombre'] }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['total'],2) }}</td>  
                            
                    </tr>
                    @endif
                @endfor
            @endif
                <tr class="letra10">
                    <td colspan="2">Total</td>
                    <td class="centrar">{{ number_format($total,2) }}</td>                
                </tr>
        </tbody>
    </table>
@endsection