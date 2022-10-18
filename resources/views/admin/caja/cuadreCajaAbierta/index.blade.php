@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Cuadre de Caja Abierta</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("cuadreCajaAbierta") }}">
        @csrf
            <div class="form-group row">
                <label for="idsucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="idsucursal" name="idsucursal" onchange="cargarCajaSucursal();">
                        <option value="" label>--Seleccione una sucursal--</option>
                        @foreach($sucursales as $sucursal)                            
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($sucursalselect)) @if($sucursalselect == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                                                     
                        @endforeach
                    </select> 
                </div>
                <label for="idcaja" class="col-sm-1 col-form-label"><center>Caja:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select" id="idcaja"  name="idcaja" required>
                        @if(isset($cajaxsucursal))
                            @foreach($cajaxsucursal as $cajaxsucursa)
                                <option value="{{$cajaxsucursa->caja_id}}" @if(isset($cajaselect)) @if($cajaselect == $cajaxsucursa->caja_id) selected @endif @endif>{{$cajaxsucursa->caja_nombre}}</option>
                             @endforeach
                        @else
                            <option value="" label>--Seleccione una caja--</option>
                        @endif                                   
                    </select>
                </div>                
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
                <label for="idMensaje" class="col-sm-2 col-form-label"><center>Caja Abierta por:</center></label>
                    <div class="col-sm-2">
                        @if(isset($cajaAbierta))
                            <label class="form-control">{{$cajaAbierta->usuario->user_nombre}}</label>
                        @else
                        <label class="form-control">Ninguno</label>
                        @endif
                    </div>                    
            </div>            
        </form>        
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Movimientos de Caja</h5>
        <div class="form-group row">
            <div class="col-sm-6">
                <h5 class="form-control" style="color:#fff; background:#17a2b8;"><CENTER>MOVIMIENTOS DE CAJA</CENTER></h5>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <table class="table table-head-fixed text-nowrap">
                        <thead>
                            <tr>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Saldo Actual</th>
                            <th class="text-center">Descripcion</th>
                            @if(Auth::user()->empresa->empresa_contabilidad == '1')
                            <th class="text-center">Diario</th>
                            @endif                            
                            </tr>
                        </thead>
                        <tbody>                           
                            @if(isset($datos))
                                @for ($i = 1; $i <= count($datos); ++$i)                                                                
                                    <tr class="text-center">                                        
                                        <td class="text-center">{{ $datos[$i]['Fecha'] }}</td>
                                        <td class="text-center">@if(is_numeric($datos[$i]['Valor'])) {{ number_format($datos[$i]['Valor'],2,'.','')  }} @endif</td>
                                        <td class="text-rigth">@if(is_numeric($datos[$i]['Saldo'])){{ number_format($datos[$i]['Saldo'],2,'.','') }} @endif</td>
                                        <td class="text-center">{{ $datos[$i]['Descripcion'] }}</td>   
                                        @if(Auth::user()->empresa->empresa_contabilidad == '1')                                   
                                        <td class="text-center"><a href="{{ url("asientoDiario/ver/{$datos[$i]['Diario']}") }}" target="_blank">{{ $datos[$i]['Diario'] }}</a></td>
                                        @endif
                                    </tr>                         
                                @endfor
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="form-group row">
                </div>
                <div class="form-group row">                        
                    <div class="col-sm-9">                           
                    </div> 
                    <label for="idCaja" class="col-sm-1 col-form-label">Total</label>
                    <div class="col-sm-2">                            
                        <input type="text" id="idSaldoMovimiento" class="form-control" value='{{number_format($saldoActualmovimiento,2,'.','')}}' readonly>
                    </div> 
                </div>
            </div>
            @if(Auth::user()->empresa->empresa_contabilidad == '1')
            <div class="col-sm-6">
                <h5 class="form-control" style="color:#fff; background:#17a2b8;"><CENTER>MOVIMIENTOS CONTABLE DE CAJA</CENTER></h5>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <table class="table table-head-fixed text-nowrap">
                        <thead>
                            <tr>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Debe</th>
                            <th class="text-center">Haber</th>
                            <th class="text-center">Saldo Actual</th>
                            <th class="text-center">Descripcion</th>
                            <th class="text-center">Diario</th>
                            </tr>
                        </thead>
                        <tbody>                           
                            @if(isset($datosDiarios))
                                @for ($a = 1; $a <= count($datosDiarios); ++$a)                                                                
                                    <tr class="text-center">                                        
                                        <td class="text-center">{{ $datosDiarios[$a]['Fecha'] }}</td>
                                        <td class="text-center">@if(is_numeric($datosDiarios[$a]['Debe'])) {{ number_format($datosDiarios[$a]['Debe'],2,'.','') }} @endif</td>
                                        <td class="text-rigth">@if(is_numeric($datosDiarios[$a]['Haber'])) {{ number_format($datosDiarios[$a]['Haber'],2,'.','') }} @endif</td>
                                        <td class="text-rigth">@if(is_numeric($datosDiarios[$a]['Saldo'])) {{ number_format($datosDiarios[$a]['Saldo'],2,'.','')}} @endif</td> 
                                        <td class="text-center">{{ $datosDiarios[$a]['Descripcion'] }}</td>                                             
                                        <td class="text-center"><a href="{{ url("asientoDiario/ver/{$datosDiarios[$a]['Diario']}") }}" target="_blank">{{ $datosDiarios[$a]['Diario']}}</a></td>                                  
                                    </tr>                         
                                @endfor
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="form-group row">
                </div>
                <div class="form-group row">                        
                    <div class="col-sm-9">                           
                    </div> 
                    <label for="idCaja" class="col-sm-1 col-form-label">Total</label>
                    <div class="col-sm-2">                            
                        <input type="text" id="idSaldoDiario" class="form-control" value='{{number_format($saldoActualdiario,2,'.','')}}' readonly>
                    </div> 
                </div>
            </div>
            @endif
        </div>            
    </div>  
</div>
<!-- /.card -->
@endsection
<script type="text/javascript">
    function cargarCajaSucursal(){    
    $.ajax({
        url: '{{ url("cajaSucursal/searchN") }}'+ '/' +document.getElementById("idsucursal").value, 
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("idsucursal").value
        },        
        success: function(data){
            document.getElementById("idcaja").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            for (var i=0; i<data.length; i++) {
                document.getElementById("idcaja").innerHTML += "<option value='"+data[i].caja_id+"'>"+data[i].caja_nombre+"</option>";

            }           
        },
    });
}
</script>