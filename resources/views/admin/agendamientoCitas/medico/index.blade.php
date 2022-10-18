@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Medico</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" onclick="bothDisable()" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">  
                    <th></th>
                    <th>Nombre</th>                                                                                  
                </tr>
            </thead>            
            <tbody>
                @foreach($medicos as $medico)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("medico/{$medico->medico_id}/horario")}}" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Horario de Atención"><i class="fa fa-clock" aria-hidden="true"></i></a>
                        <a href="{{ url("medico/{$medico->medico_id}/especialidades")}}" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Asignar Especialidades"><i class="fa fa-tasks" aria-hidden="true"></i></a>
                        <a href="{{ url("medicoAseguradora/{$medico->medico_id}/aseguradoras")}}" class="btn btn-xs btn-naranja" data-toggle="tooltip" data-placement="top" title="Asignar Aseguradora"><i class="fa fa-tasks" aria-hidden="true"></i></a>
                        <a href="{{ url("medico/{$medico->medico_id}/edit")}}" class="btn btn-xs btn-primary" onclick="editDisable();" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("medico/{$medico->medico_id}")}}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("medico/{$medico->medico_id}/eliminar")}}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>
                        @if($medico->empleado_id != null)
                            {{ $medico->empleado->empleado_nombre }}
                        @elseif($medico->proveedor_id != null)
                            {{ $medico->proveedor->proveedor_nombre }}                                             
                        @endif
                    </td>                                    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Medico</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("medico") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="empleado_id" class="col-sm-2 col-form-label">Empleado</label>
                            <div class="col-sm-8">
                                <select class="custom-select select2" id="empleado_id" name="empleado_id" required>
                                    <option value="" label>--Seleccione una opcion--</option>                                    
                                    @foreach($empleados as $empleado)                                                                                      
                                        <option value="{{$empleado->empleado_id}}">{{$empleado->empleado_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2 col-form-label">
                                <input type="radio" value="check1" id="check1" onclick="makeDisable()" class="with-gap radio-col-deep-orange" name="check" required></input>                                
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="proveedor_id" class="col-sm-2 col-form-label">Proveedor</label>
                            <div class="col-sm-8">
                                <select class="custom-select select2" id="proveedor_id" name="proveedor_id" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{$proveedor->proveedor_id}}">{{$proveedor->proveedor_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2 col-form-label">
                                <input type="radio" value="check2" id="check2" onclick="makeEnable()" class="with-gap radio-col-deep-orange" name="check"></input>                                
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="usuario_id" class="col-sm-2 col-form-label">Usuario</label>
                            <div class="col-sm-8">
                                <select class="custom-select select2" id="usuario_id" name="usuario_id" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($usuarios as $usuario)
                                        <?php $existe = false ?>
                                        @foreach($medicos as $medico)
                                            @if($usuario->user_id == $medico->user_id)
                                                <?php $existe = true ?>
                                            @endif
                                        @endforeach
                                        @if(!$existe)
                                            <option value="{{$usuario->user_id}}">{{$usuario->user_username}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>             
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- /.script -->
<script type="text/javascript">
    function bothDisable(){
        var x=document.getElementById("proveedor_id")
        document.getElementById("proveedor_id").value = "";
        x.disabled=true
        var y=document.getElementById("empleado_id")
        document.getElementById("empleado_id").value = "";
        y.disabled=true        
    }        
    function makeDisable(){
        var x=document.getElementById("proveedor_id")
        document.getElementById("proveedor_id").value = "";
        x.disabled=true
        var y=document.getElementById("empleado_id")
        y.disabled=false
    }
    function makeEnable(){
        var x=document.getElementById("empleado_id")
        document.getElementById("empleado_id").value = "";
        x.disabled=true
        var y=document.getElementById("proveedor_id")
        y.disabled=false
    }    
</script>
@endsection