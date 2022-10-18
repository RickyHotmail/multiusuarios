@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Nota de Entrega</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("ingresoPrestamo") }}">
        @csrf
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <label for="nombre_cliente" class="col-sm-1 col-form-label"><center>Cliente:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="nombre_cliente" name="nombre_cliente" >
                        <option value="0" label>--TODOS--</option>                         
                        @foreach($clientes as $cliente)
                            <option  value="{{$cliente->cliente_id}}">{{$cliente->cliente_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <div class="col-sm-1">            
                    <button type="submit" id="buscar" name="buscar"  class="btn btn-primary btn-sm" data-toggle="modal"><i class="fa fa-search"></i></button>                         
                </div>
            </div>
        </form>
        <hr>
        <table id="tnt" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Numero</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Fecha</th>
                    <th>Descripcion</th>
                    <th>Total</th>
                    
                </tr>
            </thead>
            <tbody>
                @if(isset($ingresos))
                    @foreach($ingresos as $x)
                    <tr>                   
                        <td> 
                            <a href="{{ url("ingresoPrestamo/{$x->ingreso_id}/ver") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Visualizar"><i class="fa fa-eye"></i></a>    
                            <a href="{{ url("ingresoPrestamo/{$x->ingreso_id}/eliminar") }}"  class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>         
                        </td>                   
                        </td> 
                        <td class="text-center">{{ $x->ingreso_numero}}</td>
                        <td class="text-center">{{ $x->cliente->cliente_nombre}}</td>
                        <td class="text-center">{{ $x->producto->producto_nombre}}</td>
                        <td class="text-center">{{ $x->ingreso_fecha}}</td>
                        <td class="text-center">{{ $x->ingreso_descripcion}}</td>
                        <td class="text-center"> <?php echo number_format($x->ingreso_valor, 2)?> </td>     
                                   
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
        if(isset($nombre_cliente)){  
        echo('document.getElementById("nombre_cliente").value="'.$nombre_cliente.'";'); 
        }
    ?>
</script>


@endsection
