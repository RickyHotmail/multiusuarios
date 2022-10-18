
@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Roles</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal"  method="POST" action="{{ url("modificacionRoles") }} ">
        @csrf 
            <div class="float-right">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary float-right"><i class="fa fa-search"></i>Buscar</button>
                    <button type="submit" id="guardar" name="guardar" class="btn btn-secondary float-right"><i class="fa fa-save"></i>Guardar</button>
            </div>   
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
               
                <label for="nombre_empleado" class="col-sm-1 col-form-label"><center>empleado:</center></label>
                <div class="col-sm-5">
                    <select class="custom-select select2" id="nombre_empleado" name="nombre_empleado" >
                        <option value="--TODOS--" label>--TODOS--</option>                       
                        @foreach($empleado as $empleado)
                            <option id="{{$empleado->empleado_nombre}}" name="{{$empleado->empleado_nombre}}" value="{{$empleado->empleado_id}}">{{$empleado->empleado_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
            </div>
            <table id="example5" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center">
                        <th><div class="icheck-primary d-inline"><input type="checkbox" id="checkboxPrimary1" value="select" onClick="SELECTITEMS()" checked>
                                <label for="checkboxPrimary1"></div> </th>
                        <th>Tipo de Rol</th>
                        <th>Empleado</th>
                        <th>Fecha</th>
                        <th>Dias Trabajados</th>
                        <th>Sueldo</th>
                        <th>Pago</th>  
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)   
                        <tr>  
                            <td> 
                                <input class="invisible" name="idrol[]" value="{{ $datos[$i]['idrol'] }}" />
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="item{{$datos[$i]['count']}}"  name="contador[]"  value="{{ $datos[$i]['count'] }}" checked> 
                                    <label for="item{{$datos[$i]['count']}}">
                                    </label>
                                </div>
                            </td>
                            <td class="text-center">{{ $datos[$i]["tipo"]}}</td>                         
                            <td class="text-center">{{ $datos[$i]["nombre"]}}</td>
                            <td class="text-center">{{ $datos[$i]["fecha"]}}</td>
                            <td class="text-center">{{ $datos[$i]["dias"]}}</td>
                            <td class="text-center">{{ $datos[$i]["sueldo"]}}</td>
                            <td class="text-center">{{ $datos[$i]["pago"]}}</td>    
                        
                            
                        </tr>
                        @endfor
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
    
    if('<?php echo($fecha_desde); ?>'){  
        document.getElementById("fecha_desde").value='<?php echo($fecha_desde); ?>';
    }
    if('<?php echo($fecha_hasta); ?>'){  
        document.getElementById("fecha_hasta").value='<?php echo($fecha_hasta); ?>';
    }
    
    
</script>

@endsection

