@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Editar Ordenes de Examenes</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="GET" action="{{ url("ordenExamenEditar") }}">
            @csrf
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
                        @foreach($sucursales as $suc)
                        <option value="{{$suc->sucursal_id}}" @if(isset($sucursal)) @if($sucursal == $suc->sucursal_id) selected @endif @endif>{{$suc->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>N??mero</th>
                    <th>Paciente</th>
                    <th>Otros Examenes</th>
                </tr>
            </thead>
            <tbody>
            @foreach($ordenesAtencion as $ordenAtencion)
                @if($ordenAtencion->expediente)
                    <tr class="text-center">
                        <td style="vertical-align: middle">
                            @if($ordenAtencion->expediente->ordenExamen)
                                <?php $ordenExamen=$ordenAtencion->expediente->ordenExamen ?>

                                @if($ordenExamen->orden_estado == 1)
                                    <a href="{{ url("ordenExamenEditar/{$ordenExamen->orden_id}/editarOrden") }}" class="btn btn-xs btn-warning " style="padding: 2px 8px;" data-toggle="tooltip" data-placement="top" title="Editar Orden">&nbsp;&nbsp;<i class="fas fa-edit"></i></a>
                                @elseif($ordenExamen->orden_estado == 2)
                                    <a class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="En espera Resultados"><i class="fas fa-clock"></i></a>
                                @elseif($ordenExamen->orden_estado == 3)
                                    <a class="btn btn-xs btn-primary " style="padding: 2px 8px;" data-toggle="tooltip" data-placement="top" title="Listo"><i class="fas fa-check"></i></a>
                                @endif 
                            @else
                                <a href="{{ url("ordenExamenEditar/{$ordenAtencion->orden_id}_000/editarOrden") }}" class="btn btn-xs btn-warning " style="padding: 2px 8px;" data-toggle="tooltip" data-placement="top" title="Editar Orden">&nbsp;&nbsp;<i class="fas fa-edit"></i></a>
                            @endif 
                        </td>    
                        <td style="text-align: left">
                            <i class="fas fa-clock" style="color: #2062b4" ></i>
                            {{ date('d/m/Y', strtotime($ordenAtencion->orden_fecha)) }}
                            <br>
                            {{ $ordenAtencion->orden_numero }}
                            
                            @if($ordenAtencion->orden_iess==1)
                                <img src="{{ asset('img/iess.png')  }}" width="50px">
                            @endif
                        </td>
                        <td>{{ $ordenAtencion->paciente->paciente_apellidos}} <br>
                            {{ $ordenAtencion->paciente->paciente_nombres }} </td>
                        <td>{{ $ordenAtencion->orden_otros }}</td>                                         
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.modal -->
@endsection