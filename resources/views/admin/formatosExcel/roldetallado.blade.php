<table>
    <tr>
        <td colspan="10" style="text-align: center;">NEOPAGUPA | Sistema Contable</td>
    </tr>
    <tr>
        <td colspan="10" style="text-align: center;">Rol Detallado Empleado</td>
    </tr>
</table>
<table>
    <thead>
        <?php $dato=$datos[2]; $rubros=$datos[1];   ?>
        <tr class="text-center">
        <th colspan="2"></th>
        @if($datos[3]>0)
            <th colspan="{{$datos[3]+1}}">Ingresos</th>  
        @endif
        @if($datos[4]>0)
            <th colspan="{{$datos[4]+1}}">Egresos</th> 
        @endif
        <th ></th> 
        @if($datos[5]>0)
            <th colspan="{{$datos[5]}}">Beneficios</th> 
        @endif
        @if($datos[6]>0)
            <th colspan="{{$datos[6]}}">Otros</th> 
        @endif
        <th ></th> 
        </tr>   
        <tr class="text-center">
            <th>Cedula</th>
            <th>Nombre</th> 
            @foreach($rubros as $rubro)
                @if($rubro->rubro_tipo =='2')
                <th>{{ $rubro->rubro_descripcion}}</th>  
                @endif 
            @endforeach
            <th>Total Ingresos</th> 
            @foreach($rubros as $rubro)
                @if($rubro->rubro_tipo =='1')
                <th>{{ $rubro->rubro_descripcion}}</th>  
                @endif 
            @endforeach
            <th>Total Egresos</th> 
            <th>Total</th>
            @foreach($rubros as $rubro) 
                @if($rubro->rubro_tipo =='3')
                <th>{{ $rubro->rubro_descripcion}}</th>  
                @endif 
            @endforeach
            @foreach($rubros as $rubro) 
                @if($rubro->rubro_tipo =='4')
                    <th>{{ $rubro->rubro_descripcion}}</th>  
                @endif  
            @endforeach
            <th>Total A Pagar</th> 
        </tr>
    </thead>
    <tbody>
        @if(isset($dato))
            @for ($i = 1; $i <= count($dato); ++$i)  
            <tr>  
                <td >{{ $dato[$i]['cedula'] }}</td>
                <td >{{ $dato[$i]['nombre'] }}</td>
                @foreach($rubros as $rubro)
                    @if($rubro->rubro_tipo =='2')
                        <td >{{number_format($dato[$i][$rubro->rubro_nombre],2)}}</td>  
                    @endif 
                @endforeach
                <td >{{ $dato[$i]['totalingresos'] }}</td>
                @foreach($rubros as $rubro)
                    @if($rubro->rubro_tipo =='1')
                        <td >{{number_format($dato[$i][$rubro->rubro_nombre],2)}}</td>  
                    @endif 
                @endforeach
                <td >{{ number_format($dato[$i]['totalegresos'],2) }}</td>
                <td >{{ number_format($dato[$i]['totalingresos']-$dato[$i]['totalegresos'],2) }}</td>
                @foreach($rubros as $rubro) 
                    @if($rubro->rubro_tipo =='3')
                    <?php $vari='E'.$rubro->rubro_nombre; ?>
                    <td  @if(isset($dato[$i][$vari]))  @if($dato[$i][$vari]=='Pagado')  style="background:  #70B1F7;" @endif  @if($dato[$i][$vari]=='Acumulado')  style="background:  #B1E2DD;" @endif @endif>{{number_format($dato[$i][$rubro->rubro_nombre],2)}}</td>
                    @endif  
                @endforeach
                @foreach($rubros as $rubro) 
                    @if($rubro->rubro_tipo =='4')
                        <td >{{ number_format($dato[$i][$rubro->rubro_nombre],2)}}</td>
                    @endif  
                @endforeach
                <td >{{ number_format($dato[$i]['total'],2)}}</td>
            </tr>
            @endfor
        @endif  
    </tbody>
</table>