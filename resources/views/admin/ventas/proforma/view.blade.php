@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Proforma</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listaProforma") }}">
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
                        <option value="0" label>--TODOS--</option>                         
                        @foreach($clientes as $cliente)
                            <option  value="{{$cliente->cliente_id}}">{{$cliente->cliente_nombre}}</option>
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
                        <option value="0" label>--TODOS--</option>                       
                        @foreach($sucursal as $sucursales)
                            <option  value="{{$sucursales->sucursal_id}}" @if(isset($idsucursal)) @if($sucursales->sucursal_id==$idsucursal) selected @endif @endif>{{$sucursales->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
            </div>
        </form>
        <hr>
        <table id="tproforma" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Puntos de mision</th>
                    <th>Proforma</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Sub-Total</th>
                    <th>Tarifa 0%</th>
                    <th>Tarifa 12%</th>
                    <th>Descuento</th>
                    <th>IVA</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($reporteproforma))
                    @foreach($reporteproforma as $x)
                    <tr>
                        <form action="proforma/factura" method="POST">
                            @csrf
                                        
                            <input id="PROFORMA_ID" name="PROFORMA_ID" value="{{ $x->proforma_id}}" type="hidden">                             
                            @if($x->proforma_estado ==1)
                                <td> 
                                <button type="submit"  method="POST"  class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Facturar"><i class="fas fa-money-check-alt"></i></button>
                                <a href="{{ url("proforma/edit/{$x->proforma_id}") }}"  data-toggle="tooltip" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>        
                               
                                <a href="{{url("proforma/{$x->proforma_id}/imprimir")}}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a>
                            </td> 
                                <td>
                                    <select class="custom-select" id="punto_id" name="punto_id" onchange="ShowSelected(); ">
                                        @foreach($puntoEmisiones as $puntoEmision)
                                            <option value="{{$puntoEmision->punto_id}}">{{$puntoEmision->sucursal->sucursal_codigo}}{{$puntoEmision->punto_serie}} - {{$puntoEmision->punto_descripcion}} </option>
                                        @endforeach
                                    </select>
                                </td>
                            @else
                                <td> 
                               
                                <a href="{{url("proforma/{$x->proforma_id}/imprimir")}}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a>    
                            </td> 
                                <td> 
                                </td>
                            @endif        
                            
                        </form>
                        <td class="text-center">{{ $x->proforma_numero}}</td>
                        <td class="text-center">{{ $x->cliente_nombre}}</td>
                        <td class="text-center">{{ $x->proforma_fecha}}</td>
                        <td class="text-center"> <?php echo '$' . number_format($x->proforma_subtotal, 2)?> </td>
                        <td class="text-center"> <?php echo '$' . number_format($x->proforma_tarifa0, 2)?> </td>
                        <td class="text-center"> <?php echo '$' . number_format($x->proforma_tarifa12, 2)?> </td>
                        <td class="text-center"> <?php echo '$' . number_format($x->proforma_descuento, 2)?> </td>
                        <td class="text-center"> <?php echo '$' . number_format($x->proforma_iva, 2)?> </td>
                        <td class="text-center"> <?php echo '$' . number_format($x->proforma_total, 2)?> </td>     
                        <td class="text-center" > 
                            <?php  
                                if($x->proforma_estado ==0){ echo 'Anulado';} 
                                if($x->proforma_estado ==1){ echo 'Activo';}
                                if($x->proforma_estado ==2){ echo 'Facturado';}  
                            ?>                      
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
        if(isset($nombre_cliente)){  
        echo('document.getElementById("nombre_cliente").value="'.$nombre_cliente.'";'); 
        }
    ?>
</script>


@endsection
