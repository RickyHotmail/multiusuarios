@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Roles</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal"  method="POST" action="{{ url("listacontroldia") }} ">
        @csrf 
             
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="month" name="fechames" id="fechames" class="form-control" value='<?php echo(date("Y")."-".date("m")); ?>'>
                </div>
               
               
                <label for="nombre_empleado" class="col-sm-1 col-form-label"><center>empleado:</center></label>
                <div class="col-sm-5">
                    <select class="custom-select select2" id="nombre_empleado" name="nombre_empleado" >
                        <option value="0" label>--TODOS--</option>                       
                        @foreach($empleado as $empleado)
                            <option id="{{$empleado->empleado_nombre}}" name="{{$empleado->empleado_nombre}}" value="{{$empleado->empleado_id}}">{{$empleado->empleado_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <div class="col-sm-1">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div> 
            </div>
            <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center">
                        <th></th>
                        <th>Mes</th>
                        <th>Año</th>
                        <th>Empleado</th>
                      

                    </tr>
                </thead>
                <tbody>
                    @if(isset($controles))
                   @foreach($controles as $control)
                   <tr>
                            <td class="text-center">
                            <a href="{{url("listacontroldia/{$control->control_id}/ver")}}"  class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>
                                @if($control->control_estado=='1') <a href="{{url("listacontroldia/{$control->control_id}/eliminar")}}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a> @endif
                            </td>      
                                               
                            <td class="text-center">{{ $control->control_mes}}</td>
                            <td class="text-center">{{ $control->control_ano}}</td>
                            <td class="text-center">{{ $control->empleado_nombre}}</td>
                           
                       
                    </tr>
                   @endforeach
                   @endif
                </tbody>
            </table>
        </form>
    </div>
</div>

<script>
   
    if('<?php echo($nombre_empleado); ?>'){  
        document.getElementById("nombre_empleado").value='<?php echo($nombre_empleado); ?>';
    }
   
    if('<?php echo($fechames); ?>'){  
        document.getElementById("fechames").value='<?php echo($fechames); ?>';
    }
   
    
    
</script>

@endsection

