@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Ordenes de Imagenes33</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("ordenImagen") }}">
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
                    <th>Acci??n</th>
                    <th>Estado</th> 
                    <th>N??mero</th>
                    <th>Paciente</th> 
                    <th>Otros Examenes</th>                                                                                       
                </tr>
            </thead>
            <tbody>

            @foreach($ordenesImagen as $ordenImagen)
                <?php
                    $iess=false;
                    if($ordenImagen->expediente)
                        if($ordenImagen->expediente->ordenatencion->orden_iess==1) $iess=true;
                ?>
                <tr class="text-center">
                    <td>
                        {{$ordenImagen->orden_id}}
                        @if($ordenImagen->orden_estado == 1  &&  !$iess)
                            <a href="{{ url("ordenImagen/{$ordenImagen->orden_id}/facturarOrden") }}" class="btn btn-xs btn-primary " style="padding: 2px 8px; border-radius: 6px" data-toggle="tooltip" data-placement="top" title="NO FACTURADO">
                                <i class="fas fa-edit"></i> Facturar
                            </a>
                        @elseif($ordenImagen->orden_estado==2 || ($ordenImagen->orden_estado==3 && $iess))
                            <a href="{{ url("ordenImagen/{$ordenImagen->orden_id}/subirImagenes") }}" class="btn btn-xs btn-primary " style="padding: 2px 8px; border-radius: 6px" data-toggle="tooltip" data-placement="top" title="Subir Resultados">
                                <i class="fas fa-upload"></i> &nbsp;&nbsp;&nbsp; Subir
                            </a>
                        @elseif($ordenImagen->orden_estado==3)
                            <a href="{{ url("ordenImagen/{$ordenImagen->orden_id}/verResultadosImagen") }}" class="btn btn-xs btn-primary " style="padding: 2px 8px; border-radius: 6px" data-toggle="tooltip" data-placement="top" title="ver Resultados">
                                <i class="fa fa-eye"></i>
                            </a>
                        @endif
                    </td>
                    <td>
                        @if($ordenImagen->orden_estado == 1)
                            <a class="btn btn-xs btn-outline-danger " style="padding: 2px 8px;" data-toggle="tooltip" data-placement="top">
                                PENDIENTE
                            </a>
                        @elseif($ordenImagen->orden_estado==2)
                            <a class="btn btn-xs btn-outline-primary " style="padding: 2px 8px;" data-toggle="tooltip" data-placement="top">
                                POR SUBIR
                            </a>
                        @else
                            <a class="btn btn-xs btn-outline-success" style="padding: 2px 8px;" data-toggle="tooltip" data-placement="top">
                                TODO LISTO
                            </a>
                        @endif
                    </td>
                        
                    <td style="text-align: left">
                        <i class="fas fa-clock" style="color: #2062b4" ></i>
                        {{ date('d/m/Y', strtotime($ordenImagen->expediente->ordenAtencion->orden_fecha)) }}
                        <br>
                        {{ $ordenImagen->expediente->ordenAtencion->orden_numero }}
                        @if($iess)
                            @if($ordenImagen->expediente->ordenatencion->orden_iess==1)
                                <img src="{{ asset('img/iess.png')  }}" width="50px">
                            @endif
                        @endif
                    </td>
                    <td>{{ $ordenImagen->expediente->ordenAtencion->paciente->paciente_apellidos}} <br>
                        {{ $ordenImagen->expediente->ordenAtencion->paciente->paciente_nombres }}
                    </td>
                    <td>{{ $ordenImagen->expediente->ordenAtencion->orden_otros }}</td>                                         
                </tr>
                 
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.modal -->
@endsection