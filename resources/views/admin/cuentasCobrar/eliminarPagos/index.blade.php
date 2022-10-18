@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Eliminar Pago de Clientes</h3>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("eliminarPagoCXC") }} ">
        @csrf
            <div class="row">
                <div class="col-sm-7">
                    <div class="form-group row">
                        <label for="fecha_desde" class="col-sm-2 col-form-label">Desde:</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                        </div>
                        <label for="fecha_desde" class="col-sm-2 col-form-label">Hasta:</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                        </div>
                        <div class="col-sm-1">
                            <div class="icheck-primary">
                                <input type="checkbox" id="fecha_todo" name="fecha_todo" @if(isset($todo)) @if($todo == 1) checked @endif @else checked @endif>
                                <label for="fecha_todo" class="custom-checkbox"><center>Todo</center></label>
                            </div>                    
                        </div>
                    </div>     
                </div>
                <label for="idBanco" class="col-sm-1 col-form-label">Sucursal :</label>
                <div class="col-sm-3">
                    <select class="custom-select" id="sucursal_id" name="sucursal_id" required>
                        <option value="0" @if(isset($sucurslaC)) @if($sucurslaC == 0) selected @endif @endif>Todas</option>
                        @foreach($sucursales as $sucursal)
                        <option value="{{$sucursal->sucursal_id}}" @if(isset($sucurslaC)) @if($sucurslaC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1">
                    <center><button type="submit" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div> 
            <div class="row">
                <label for="clienteID" class="col-sm-1 col-form-label">Cliente:</label>
                <div class="col-sm-6">
                    <select class="custom-select select2" id="clienteID" name="clienteID" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($clientes as $cliente)
                            <option value="{{$cliente->cliente_id}}" @if(isset($clienteC)) @if($clienteC == $cliente->cliente_id) selected @endif @endif>{{$cliente->cliente_nombre}}</option>
                        @endforeach
                    </select>                    
                </div> 
                @if(isset($datos))
                <div class="col-sm-4 centrar-texto"><h6 style="color: #a34712;">IMPORTANTE : Se eliminaran todos los pagos relacionados al asiento diario que le pertenece al pago seleccionado.</h6></div>
                <div class="col-sm-1">
                    <button type="button" onclick="confirmacion();" id="eliminarAux" name="eliminarAux" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                    <center><button type="submit"  id="IDeliminar" name="eliminar" class="invisible"><i class="fa fa-trash"></i></button><center>
                </div>
                @endif
            </div>   
            <br>
            <table class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th>Documento</th>
                        <th>Numero</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Saldo</th>
                        @if(Auth::user()->empresa->empresa_llevaContabilidad == '1')
                        <th>Diario</th>
                        @endif  
                        <th></th>  
                        <th>Valor</th>  
                        <th>Fecha</th>
                        @if(Auth::user()->empresa->empresa_llevaContabilidad == '1')
                        <th>Diario Pago</th> 
                        @endif
                        <th>Referencia</th>   
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)   
                            <tr>
                            <td @if($datos[$i]['tot'] == '1') style="background:  #DEAA99;" @endif class="text-center">{{ $datos[$i]['doc'] }}</td>
                            <td @if($datos[$i]['tot'] == '1') style="background:  #DEAA99;" @endif class="text-center">{{ $datos[$i]['num'] }}</td>
                            <td @if($datos[$i]['tot'] == '1') style="background:  #DEAA99;" @endif class="text-center">{{ $datos[$i]['fec'] }}</td>
                            <td @if($datos[$i]['tot'] == '1') style="background:  #DEAA99;" @endif class="text-center">@if(is_numeric($datos[$i]['mon'])){{ number_format($datos[$i]['mon'],2) }}@endif</td>
                            <td @if($datos[$i]['tot'] == '1') style="background:  #DEAA99;" @endif class="text-center">@if(is_numeric($datos[$i]['sal'])){{ number_format($datos[$i]['sal'],2) }}@endif</td>
                            @if(Auth::user()->empresa->empresa_llevaContabilidad == '1')
                            <td @if($datos[$i]['tot'] == '1') style="background:  #DEAA99;" @endif class="text-center"><a href="{{ url("asientoDiario/ver/{$datos[$i]['dia']}") }}" target="_blank">{{ $datos[$i]['dia'] }}</a></td>
                            @endif
                            <td @if($datos[$i]['tot'] == '1') style="background:  #DEAA99;" @endif>@if($datos[$i]['tot'] == '2') @if($datos[$i]['chk'] == '1')<input type="checkbox" id="{{ $datos[$i]['cod'] }}" name="checkbox[]" value="{{ $datos[$i]['cod'] }}">@endif @endif</td>
                            <td @if($datos[$i]['tot'] == '1') style="background:  #DEAA99;" @endif class="text-center">@if(is_numeric($datos[$i]['val'])){{ number_format($datos[$i]['val'],2) }}@endif</td>
                            <td @if($datos[$i]['tot'] == '1') style="background:  #DEAA99;" @endif class="text-center">{{ $datos[$i]['fep'] }}</td>
                            @if(Auth::user()->empresa->empresa_llevaContabilidad == '1')
                            <td @if($datos[$i]['tot'] == '1') style="background:  #DEAA99;" @endif class="text-center"><a href="{{ url("asientoDiario/ver/{$datos[$i]['dip']}") }}" target="_blank">{{ $datos[$i]['dip'] }}</a></td>
                            @endif
                            <td @if($datos[$i]['tot'] == '1') style="background:  #DEAA99;" @endif class="text-center">{{ $datos[$i]['ref'] }}</td>
                            </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </form>
    </div>
</div>
<script type="text/javascript">
    function confirmacion(){
        bootbox.confirm({
            message: "¿Estás seguro de eliminar estos pagos?",
            buttons: {
                confirm: {
                    label: 'SI',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'NO',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if(result == true){
                    $("#IDeliminar").click();
                }
            }
        });
    }
</script>
@endsection