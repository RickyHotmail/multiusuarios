@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("listaAsientoDiario") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Lista de Asientos Diarios</h3>                                 
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>

                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <div class="col-sm-1">
                    <div class="icheck-secondary">
                        <input type="checkbox" id="fecha_todo" name="fecha_todo">
                        <label for="fecha_todo" class="custom-checkbox"><center>Todo</center></label>
                    </div>                    
                </div>
               
                <label for="diario_tipo" class="col-sm-1 col-form-label"><center>Tipo:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="diario_tipo" name="diario_tipo" >
                        <option value="--TODOS--" label>--TODOS--</option>                       
                        @foreach($tipos as $tipo)
                            <option id="{{$tipo->diario_tipo_documento}}" name="{{$tipo->diario_tipo_documento}}" value="{{$tipo->diario_tipo_documento}}">{{$tipo->diario_tipo_documento}}</option>
                        @endforeach
                    </select>                                     
                </div>
               
            </div>   
            <div class="form-group row">
                <label for="nombre_sucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="sucursal" name="sucursal" >
                        <option value="--TODOS--" label>--TODOS--</option>                       
                        @foreach($sucursal as $sucursales)
                            <option id="{{$sucursales->sucursal_nombre}}" name="{{$sucursales->sucursal_nombre}}" value="{{$sucursales->sucursal_nombre}}">{{$sucursales->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <div class="col-sm-1">                    
                </div>               
                <label for="BuscarLike" class="col-sm-1 col-form-label"><center>Buscar:</center></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="BuscarLike" name="BuscarLike">                        
                </div>
                <div class="col-sm-1">
                    <button type="submit"  class="btn btn-primary btn-sm" data-toggle="modal"><i class="fa fa-search"></i></button>
                </div>
            </div> 
            <hr>        
            <table id="example1" class="table table-bordered table-responsive table-hover sin-salto">
                <thead>              
                    <tr class="text-center neo-fondo-tabla">    
                        <th></th>                
                        <th>Fecha</th>
                        <th>Codigo</th>
                        <th>Referencia</th>
                        <th>Documento</th>
                        <th>Comentario</th>
                        <th>N° Documento</th>
                        <th>Sucursal</th>
                    </tr>
                </thead>
                          
                <tbody>                                                                        
                    @if(isset($diarios)) 
                        @foreach($diarios as $x)
                            <tr class="text-center">
                                <td>
                                    <a href="{{ url("asientoDiario/ver/{$x->diario_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Visualizar"><i class="fa fa-eye"></i></a> 
                                    <a href="{{ url("asientoDiario/imprimir/{$x->diario_id}") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a>    
                                </td>
                                <td>{{ $x->diario_fecha}}</td>  
                                <td>{{ $x->diario_codigo}}</td>                                      
                                <td>{{ $x->diario_referencia}}</td>
                                <td>{{ $x->diario_tipo_documento}}</td>
                                <td>{{ $x->diario_comentario}}</td>
                                <td>{{ $x->diario_numero_documento}}</td>
                                <td>{{ $x->sucursal->sucursal_nombre}}</td>
                                
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>        
        </div>
    </div>
</form>
<script>
     <?php
    if(isset($fecha_todo)){  
        echo('document.getElementById("fecha_todo").checked=true;');
    }
    if(isset($fecha_hasta)){  
        ?>
         document.getElementById("idHasta").value='<?php echo($fecha_hasta); ?>';
         <?php
    }
    if (isset($fecha_desde)) {
        ?>
       document.getElementById("idDesde").value='<?php echo($fecha_desde); ?>';
    
    <?php
    }
    if(isset($idtipo)){  
        ?>
       document.getElementById("diario_tipo").value='<?php echo($idtipo); ?>';
        <?php
     }
     if(isset($idsucursal)){ 
      ?>
     document.getElementById("sucursal").value='<?php echo($idsucursal); ?>';
         <?php
     }
     //buscaLike
     if(isset($buscaLike)){ 
        ?>
       document.getElementById("BuscarLike").value='<?php echo($buscaLike); ?>';
           <?php
       }
      ?>
</script>

@endsection