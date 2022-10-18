@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Eliminar Anticipos de Clientes</h3>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST"   action="{{ url("eliminatAntCli") }} ">
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
                    <center><button type="submit" onclick="return confirm('¿Estás seguro de eliminar estos registros?');" name="eliminar" class="btn btn-danger"><i class="fa fa-trash"></i></button></center>
                </div>
                @endif
            </div>   
            <br>
            <table class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th>Beneficiario</th>
                        <th></th>
                        <th>Monto</th>
                        <th>Saldo</th>
                        <th>Fecha</th>
                        <th></th>
                        <th>Pago</th>  
                        <th>Fecha Pago</th>
                        @if(Auth::user()->empresa->empresa_llevaContabilidad == '1')
                        <th>Diario</th>       
                        @endif           
                        <th>Tipo</th>
                        <th>Factura</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)   
                            <tr>
                                @if($datos[$i]['tot'] == '1')
                                <td style="background:  #a5c7eb;" colspan="11">{{ $datos[$i]['ben'] }}</td>
                                @endif
                                @if($datos[$i]['tot'] == '2')
                                <td style="background:  #e5c3b3"></td>
                                <td style="background:  #e5c3b3">@if($datos[$i]['chk'] == '1') <input type="checkbox" id="{{ $datos[$i]['cod'] }}" name="checkbox[]" value="{{ $datos[$i]['cod'] }}"> @endif</td>
                                <td style="background:  #e5c3b3">{{ number_format($datos[$i]['mon'],2) }}</td>
                                <td style="background:  #e5c3b3">{{ number_format($datos[$i]['sal'],2) }}</td>
                                <td style="background:  #e5c3b3">{{ $datos[$i]['fec'] }}</td>
                                <td style="background:  #e5c3b3" colspan="3"></td>
                                @if(Auth::user()->empresa->empresa_llevaContabilidad == '1')
                                <td style="background:  #e5c3b3"><a href="{{ url("asientoDiario/ver/{$datos[$i]['dir']}") }}" target="_blank">{{ $datos[$i]['dir'] }}</a></td>
                                @endif
                                <td style="background:  #e5c3b3">{{ $datos[$i]['tip'] }}</td>
                                <td style="background:  #e5c3b3"></td>
                                @endif
                                @if($datos[$i]['tot'] == '3')
                                <td colspan="5"></td>
                                <td><input type="checkbox" id="{{ $datos[$i]['cod'] }}" name="checkbox2[]" value="{{ $datos[$i]['cod'] }}"></td>
                                <td>{{ number_format($datos[$i]['pag'],2) }}</td>
                                <td>{{ $datos[$i]['fep'] }}</td>
                                @if(Auth::user()->empresa->empresa_llevaContabilidad == '1')
                                <td><a href="{{ url("asientoDiario/ver/{$datos[$i]['dir']}") }}" target="_blank">{{ $datos[$i]['dir'] }}</a></td>
                                @endif
                                <td>{{ $datos[$i]['tip'] }}</td>
                                <td>{{ $datos[$i]['fac'] }}</td>
                                @endif
                            </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </form>
    </div>
</div>
@endsection