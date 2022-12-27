@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title mt-1">Editar Orden de Atencion</h3>
        <!--button type="button" onclick='window.location = "{{ url("nuevaOrden") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-plus"></i>&nbsp;Nuevo</button-->
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="GET" action="{{ url("ordenAtencionEditar") }}">
        @csrf
            @if($rol->rol_nombre=="Administrador")
                <div class="form-group row">
                    <label for="fecha_desde" class="col-sm-1 col-form-label">Medicos:</label>

                    <div class="col-sm-4">
                        <select name="medico_id" class="form-control">
                            <option value=0 @if($seleccionado==0) selected @endif >Todos</option>

                            @foreach($medicos as $medico)
                                <option value="{{ $medico->medico_id }}" @if($seleccionado==$medico->medico_id) selected @endif>
                                    @if($medico->empleado)
                                        {{ $medico->empleado->empleado_nombre }}
                                    @elseif($medico->proveedor)
                                        {{ $medico->proveedor->proveedor_nombre }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            <div class="form-group row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="fecha_desde" class="col-sm-2 col-form-label">Desde:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-01");} ?>'>
                        </div>
                        <label for="fecha_desde" class="col-sm-2 col-form-label">Hasta:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                        </div>
                    </div>
                </div>
                <label for="idBanco" class="col-lg-1 col-md-1 col-form-label">Sucursal :</label>
                <div class="col-lg-4 col-md-4">
                    <select class="custom-select select2" id="sucursal_id" name="sucursal_id" required>
                        @foreach($sucursales as $sucursal)
                        <option value="{{$sucursal->sucursal_id}}" @if(isset($sucurslaC)) @if($sucurslaC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
        <table id="example1" class="table table-bordered table-hover">
            <thead>
                <tr class="text-center  neo-fondo-tabla">
                    <th></th>
                    <th>Cita</th>
                    <th>Paciente</th>
                    <th>Sucursal</th>
                    <th>Especialidad</th>
                    <th>Medico</th>
                    <th>Estatus</th>
                    <th>AG</th>
                    <th>PG</th>
                    <th>PRE</th>
                    <th>ATE</th>
                </tr>
            </thead>
            <tbody>
            @if(isset($ordenesAtencion))
                @foreach($ordenesAtencion as $ordenAtencion)
                    @if($ordenAtencion->medico->user_id==Auth::user()->user_id || ($rol->rol_nombre=="Administrador" && ($seleccionado==0 || $seleccionado==$ordenAtencion->medico->medico_id)))
                        <tr class="text-center">
                            <td>
                                @if($ordenAtencion->orden_estado == 2)
                                    <a href="{{ url("nuevoSignosV/{$ordenAtencion->orden_id}")}}" class="btn btn-xs btn-warning " data-toggle="tooltip" data-placement="top" title="Signos vitales"><i class="fas fa-heartbeat"></i></a>                         
                                @endif
                                @if($rol->rol_nombre=="Administrador" && $ordenAtencion->orden_estado >= 3)
                                    <a href="{{ url("editarSignosV/{$ordenAtencion->orden_id}")}}" class="btn btn-xs btn-warning " data-toggle="tooltip" data-placement="top" title="Editar Signos V."><i class="fas fa-heartbeat"></i><i class="fa fa-pencil-alt"></i></a>                         
                                @endif
                                @if($ordenAtencion->orden_estado == 4)
                                    <a href="{{ url("editarDiagnostico/{$ordenAtencion->orden_id}")}}" class="btn btn-xs btn-info text-dark" data-toggle="tooltip" data-placement="top" title="Editar Diagnóstico"><i class="fas fa-book"></i><i class="fa fa-pencil-alt"></i></a>                         
                                @endif

                                
                                    <!--a href="{{ url("editarDiagnostico/{$ordenAtencion->orden_id}")}}" class="btn btn-xs btn-info text-dark" data-toggle="tooltip" data-placement="top" title="Editar Diagnóstico"><i class="fas fa-book"></i><i class="fa fa-pencil-alt"></i></a-->
                                
                                
                                <a href="{{ url("ordenAtencion/{$ordenAtencion->orden_id}")}}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fas fa-eye"></i></a>
                            </td>
                            <td style="text-align: left">
                                <i class="fas fa-clock" style="color: #2062b4" ></i>
                                {{ date('d/m/Y', strtotime($ordenAtencion->orden_fecha)) }}
                                {{ $ordenAtencion->orden_hora }}
                                <br>
                                {{ $ordenAtencion->orden_numero }}
                                
                                @if($ordenAtencion->orden_iess==1)
                                    <img src="{{ asset('img/iess.png')  }}" width="50px" style="background-color: white; border-radius: 6px; padding: 5px;">
                                @endif
                            </td>
                            <td>
                                {{ $ordenAtencion->paciente->paciente_apellidos}} <br>
                                {{ $ordenAtencion->paciente->paciente_nombres }}                   
                            </td>
                            <td>
                                {{ $ordenAtencion->sucursal_nombre }}  
                            </td>
                            <td>@if(isset($ordenAtencion->especialidad->especialidad_nombre )) {{$ordenAtencion->especialidad->especialidad_nombre}} @endif</td>
                            <td>
                                @if(isset($ordenAtencion->medico->empleado))
                                    {{$ordenAtencion->medico->empleado->empleado_nombre}}
                                @elseif(isset($ordenAtencion->medico->proveedor))
                                    {{$ordenAtencion->medico->proveedor_proveedor_nombre}}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($ordenAtencion->orden_estado==0) CANCELADA @endif
                                @if($ordenAtencion->orden_estado==1) AGENDADA @endif
                                @if($ordenAtencion->orden_estado==2) PAGADA @endif
                                @if($ordenAtencion->orden_estado==3) PREATENDIDA @endif
                                @if($ordenAtencion->orden_estado==4) ATENTIDA @endif
                            </td>
                            <td>                        
                                <center>
                                    <img src="{{ asset('admin/imagenes/calendario.png') }}" data-toggle="tooltip" title="Agendada" class="brand-image" alt="NEOPAGUPA" style="width: 30px;">
                                </center>
                            </td>
                            <td>
                                @if($ordenAtencion->orden_estado >= 2)
                                    <center>
                                        <img src="{{ asset('admin/imagenes/pagar.png') }}" data-toggle="tooltip" title="Pagada" class="brand-image" alt="NEOPAGUPA" style="width: 30px;">
                                    </center>
                                @endif
                            </td>
                            <td>
                                @if($ordenAtencion->orden_estado >= 3)
                                    <center>
                                        <img src="{{ asset('admin/imagenes/nurse.png') }}" data-toggle="tooltip" title="Preatendida" class="brand-image" alt="NEOPAGUPA" style="width: 30px;">
                                    </center>
                                @endif
                            </td>
                            <td>
                                @if($ordenAtencion->orden_estado == 4 )     
                                    <center>
                                        <img src="{{ asset('admin/imagenes/vacuna.png') }}"  data-toggle="tooltip" title="Atendida" class="brand-image" alt="NEOPAGUPA" style="width: 30px;">
                                    </center>
                                @endif
                            </td>
                        </tr> 
                    @else
                    @endif                              
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection