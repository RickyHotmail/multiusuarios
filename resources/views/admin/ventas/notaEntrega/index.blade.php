@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Nota de Entrega</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("notaentrega/buscar") }}">
        @csrf
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <div class="col-sm-1">
                    <div class="icheck-success">
                        <input type="checkbox" id="fecha_todo" name="fecha_todo">
                        <label for="fecha_todo" class="custom-checkbox"><center>Todo</center></label>
                    </div>                    
                </div>
                <label for="nombre_cliente" class="col-sm-1 col-form-label"><center>Cliente:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="nombre_cliente" name="nombre_cliente" >
                        <option value="--TODOS--" label>--TODOS--</option>                         
                        @foreach($clientes as $cliente)
                            <option id="{{$cliente->cliente_nombre}}" name="{{$cliente->cliente_nombre}}" value="{{$cliente->cliente_id}}">{{$cliente->cliente_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <div class="col-sm-1">            
                    <button type="submit"  class="btn btn-primary btn-sm" data-toggle="modal"><i class="fa fa-search"></i></button>                         
                </div>
            </div>
            <div class="form-group row">
                <label for="nombre_sucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="sucursal" name="sucursal" >
                        <option value="--TODOS--" label>--TODOS--</option>                       
                        @foreach($sucursal as $sucursales)
                            <option id="{{$sucursales->sucursal_nombre}}" name="{{$sucursales->sucursal_nombre}}" value="{{$sucursales->sucursal_nombre}}" @if(isset($idsucursal)) @if($sucursales->sucursal_nombre==$idsucursal) selected @endif @endif>{{$sucursales->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
            </div>
        </form>
        <hr>
        <table id="tnt" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Nota de Entrega</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Tipo de Pago</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($notaentrega))
                    @foreach($notaentrega as $x)
                    <tr>                   
                        <td> 
                            @if($x->nt_tipo_pago=='EN EFECTIVO')
                                <a href="{{ url("notaentrega/{$x->nt_id}/eliminar") }}"  class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>         
                            @else
                                @if($x->cuentaCobrar->cuenta_monto==$x->cuentaCobrar->cuenta_saldo)
                                    <a href="{{ url("notaentrega/{$x->nt_id}/eliminar") }}"  class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>         
                                @endif
                            @endif
                            <a href="{{ url("notaentrega/{$x->nt_id}/ver") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Visualizar"><i class="fa fa-eye"></i></a>    
                            <a href="{{ url("notaentrega/{$x->nt_id}/imprimir") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a> 
                            <a href="{{ url("notaentrega/{$x->nt_id}/imprimirRecibo") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir Recibo"><i class="fa fa-print"></i></a>  
                        </td>                   
                        </td> 
                        <td class="text-center">{{ $x->nt_numero}}</td>
                        <td class="text-center">{{ $x->cliente_nombre}}</td>
                        <td class="text-center">{{ $x->nt_fecha}}</td>
                        <td class="text-center">{{ $x->nt_tipo_pago}}</td>
                        <td class="text-center"> <?php echo '$' . number_format($x->nt_total, 2)?> </td>     
                        <td class="text-center" > 
                            @if($x->nt_estado ==0) Anulado @endif
                            @if($x->nt_estado ==1) Activo @endif
                            @if($x->nt_estado ==2) Facturado @endif                     
                        </td>              
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<script>
    <?php
        if(isset($fecha_desde)){  
            echo('document.getElementById("fecha_desde").value="'.$fecha_desde.'";');
        }
        if(isset($fecha_hasta)){  
            echo('document.getElementById("fecha_hasta").value="'.$fecha_hasta.'";');
        }
        if(isset($fecha_todo)){  
            echo('document.getElementById("fecha_todo").checked=true;');
        }
        if(isset($nombre_cliente)){  
        echo('document.getElementById("nombre_cliente").value="'.$nombre_cliente.'";'); 
        }
    ?>
</script>


@endsection
