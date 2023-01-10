@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Edición de Ordenes de Imagenes</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("ordenesImagen") }}">
        @csrf
            <div class="form-group row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="fecha_desde" class="col-sm-2 col-form-label">Desde:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
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
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th>Acción</th>
                    <th>Estado</th> 
                    <th>Número</th>
                    <th>Paciente</th> 
                    <th>Otros Examenes</th>                                                                                       
                </tr>
            </thead>
            <tbody>
                @foreach($ordenesAtencion as $ordenAtencion)
                    @if($ordenAtencion->expediente)
                        <?php
                            $iess=false;
                            if($ordenAtencion->orden_iess==1) $iess=true;
                        ?>
                        
                        <tr class="text-center">
                            <td style="vertical-align: middle">
                                @if($ordenAtencion->expediente->ordenImagen)
                                    <?php $ordenImagen=$ordenAtencion->expediente->ordenImagen ?>

                                    @if($ordenImagen->orden_estado == 1)
                                        <a href="{{ url("ordenImagen/{$ordenImagen->orden_id}/editar") }}" class="btn btn-xs btn-warning " style="padding: 2px 8px;" data-toggle="tooltip" data-placement="top" title="Editar Orden">&nbsp;&nbsp;<i class="fas fa-edit"></i></a>
                                    @elseif($ordenImagen->orden_estado == 2)
                                        <a class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Por Subir"><i class="fas fa-clock"></i></a>
                                    @elseif($ordenImagen->orden_estado == 3)
                                        <a class="btn btn-xs btn-primary " style="padding: 2px 8px;" data-toggle="tooltip" data-placement="top" title="Listo"><i class="fas fa-check"></i></a>
                                    @endif 
                                @else
                                    <a href="{{ url("ordenImagen/{$ordenAtencion->orden_id}_000/editar") }}" class="btn btn-xs btn-warning " style="padding: 2px 8px;" data-toggle="tooltip" data-placement="top" title="Editar Orden">&nbsp;&nbsp;<i class="fas fa-edit"></i></a>
                                @endif 
                            </td>

                            <td style="text-align: left">
                                <i class="fas fa-clock" style="color: #2062b4"></i>
                                {{ date('d/m/Y', strtotime($ordenAtencion->orden_fecha)) }}
                                <br>
                                {{ $ordenAtencion->orden_numero }}
                                @if($iess)
                                    <img src="{{ asset('img/iess.png')  }}" width="50px">
                                @endif
                            </td>
                            <td>
                                {{ $ordenAtencion->paciente->paciente_apellidos}} 
                                <br>
                                {{ $ordenAtencion->paciente->paciente_nombres }}
                            </td>
                            <td>-</td>
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