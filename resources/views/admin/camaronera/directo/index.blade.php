@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Costo Directo</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("directo/buscar") }}">
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
                    <div class="icheck-secondary">
                        <input type="checkbox" id="fecha_todo" name="fecha_todo">
                        <label for="fecha_todo" class="custom-checkbox"><center>Todo</center></label>
                    </div>                    
                </div>
                <label for="nombre_bodega" class="col-sm-1 col-form-label"><center>Bodega:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select " id="nombre_bodega" name="nombre_bodega" >
                        <option value="0" label>--TODOS--</option>                         
                        @foreach($bodega as $bodega)
                            <option id="{{$bodega->bodega_nombre}}" name="{{$bodega->bodega_nombre}}" value="{{$bodega->bodega_id}}">{{$bodega->bodega_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <div class="col-sm-1">            
                    <button type="submit"  class="btn btn-primary btn-sm" data-toggle="modal"><i class="fa fa-search"></i></button>                         
                </div>          
            </div>
            <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
                    <thead>
                    <tr class="text-center">
                        <th></th>
                        <th>N?? Egreso</th>
                        <th>Fecha</th>
                        <th>Bodega</th>
                      
                        <th>Sub-Total</th>
                        <th>Total</th>
                      
                    </tr>
                </thead>
                <tbody>
                    @if(isset($egreso))
                        @foreach($egreso as $x)
                        <tr>
                                <td> 
                                    <input id="egreso_id" name="egreso_id" value="{{ $x->cabecera_egreso_id}}" type="hidden">   
                                    <a href="{{ url("directo/eliminar/{$x->cabecera_egreso_id}") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a> 
                                    <a href="{{ url("directo/{$x->cabecera_egreso_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>    
                                </td> 
                                         
                            <td class="text-center">{{ $x->cabecera_egreso_numero}}</td>
                            <td class="text-center">{{ $x->cabecera_egreso_fecha}}</td>
                            <td class="text-center">{{ $x->bodega_nombre}}</td>
                            <td class="text-center">{{ $x->cabecera_egreso_destino}}</td>
                            <td class="text-center"> <?php echo '$' . number_format($x->cabecera_egreso_total, 2)?> </td>     
                                       
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </form>
    </div>
</div>
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
        if(isset($nombre_bodega)){  
            echo('document.getElementById("nombre_bodega").value="'.$nombre_bodega.'";');
        }
    ?>
</script>


@endsection
