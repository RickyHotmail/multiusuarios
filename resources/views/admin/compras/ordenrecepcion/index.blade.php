@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Ordenes de Recepciones</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("ordenRecepecion/buscar") }}">
        @csrf
            <div class="form-group row">
                <label for="nombre_proveedor" class="col-sm-1 col-form-label"><center>Proveedor:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="nombre_proveedor" name="nombre_proveedor">  
                        <option value="0" label>--TODOS--</option>                      
                        @foreach($proveedores as $proveedor)
                            <option   value="{{$proveedor->proveedor_id}}" @if(isset($idproveedor)) @if($proveedor->proveedor_id==$idproveedor) selected @endif @endif>{{$proveedor->proveedor_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>    
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
                   
            </div>
            <div class="form-group row">
                <label for="sucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="sucursal" name="sucursal">  
                        <option value="0" label>--TODOS--</option>                      
                        @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($idsucursal)) @if($sucursal->sucursal_id==$idsucursal) selected @endif @endif>
                                {{$sucursal->sucursal_nombre}} 
                            </option>
                        @endforeach
                    </select>                                     
                </div>   
                <label for="estados" class="col-sm-1 col-form-label"><center>Estados:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select" id="estados" name="estados" >
                        <option value="0" label>--TODOS--</option>                        
                        @foreach($estados as $estado)
                            <option  value="{{$estado->ordenr_estado}}">
                            @if( $estado->ordenr_estado ==0) Anulado @endif
                            @if( $estado->ordenr_estado ==1) Activo @endif
                            @if( $estado->ordenr_estado ==2) Facturada @endif
                            </option>
                        @endforeach
                    </select>                                     
                </div>
                <div class="col-sm-2">            
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <button type="submit" id="extraer" name="extraer" class="btn btn-success"><i class="fa fa-save"></i><span> Facturar</span></button>                   
                                  
                </div>
            </div>
            <table id="example1" class="table table-bordered table-responsive table-hover sin-salto">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th></th>
                        <th></th>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($orden))
                        @foreach($orden as $x)
                        <tr>  
                            @if ( $x->ordenr_estado ==1)                       
                            <td>
                               
                                <div class="icheck-primary">
                                    <input type="checkbox"  value="{{ $x->ordenr_id}}" id="check{{$x->ordenr_id}}" name="ORDEN_ID[]">
                                    <label for="check{{$x->ordenr_id}}"></label>
                                </div>
                            </td>  
                            @else
                                <td> </td> 
                            @endif 
                            <td>
                            @if($x->ordenr_estado ==1)
                                <a href="{{ url("ordenRecepecion/{$x->ordenr_id}/edit")}}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <a href="{{ url("ordenRecepecion/{$x->ordenr_id}/eliminar") }}"  class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>                           
                            @endif
                                <a href="{{ url("ordenRecepecion/{$x->ordenr_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>     
                                <a href="{{ url("ordenRecepecion/{$x->ordenr_id}/imprimir") }}" target="_blank" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a>                   
                            </td>                                        
                            <td class="text-center">{{ $x->ordenr_numero}}</td>
                            <td class="text-center">{{ $x->ordenr_fecha}}</td>
                            <td class="text-center">{{ $x->proveedor_nombre}}</td>
                            <td class="text-center">
                                    @if( $x->ordenr_estado ==0) Anulado @endif 
                                    @if( $x->ordenr_estado ==1) Activo @endif 
                                    @if( $x->ordenr_estado ==2) Facturada @endif            
                            </td>      
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </form>
</div>
<script>
     <?php
    if(isset($fecha_todo)){  
        echo('document.getElementById("fecha_todo").checked=true;');
    }
    if(isset($fecha_hasta)){  
        ?>
         document.getElementById("fecha_hasta").value='<?php echo($fecha_hasta); ?>';
         <?php
    }
    if(isset($fecha_desde)){  
        ?>
         document.getElementById("fecha_desde").value='<?php echo($fecha_desde); ?>';
         <?php
    }
    if(isset($valorestados)){  
        ?>
         document.getElementById("estados").value='<?php echo($valorestados); ?>';
         <?php
    }
     ?>
    
    
</script>
@endsection

