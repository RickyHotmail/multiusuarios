@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title mt-1">Facturar Orden de Atencion</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="GET" action="{{ url("facturarOrdenAtencion") }}">
            @csrf
            
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label">Pacientes:</label>

                <div class="col-sm-4">
                    <select name="paciente" class="custom-select select2">
                        @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->paciente_id }}" @if(isset($pacienteSel)) @if($pacienteSel==$paciente->paciente_id) selected @endif @endif>{{$paciente->paciente_apellidos}} {{$paciente->paciente_nombres}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
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
                    <select class="custom-select select2" id="sucursal" name="sucursal" required>
                        @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($sucursalSel)) @if($sucursalSel == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <form method="post" action="{{ url('facturarOrdenAtencion') }}" onsubmit="return comprobarSeleccionados()">
            <input type="hidden" name="sucursal" value="@if(isset($sucursal_id)) {{ $sucursal_id }} @else {{ $sucursales[0]->sucursal_id }}  @endif">
            @csrf
            <div class="custom-control custom-checkbox mb-3">
                <div class="col-md-3">
                    <input class="custom-control-input" type="checkbox"  id="checkMarcar">
                    <label for="checkMarcar" class="custom-control-label">Marcar Todos</label>
                
                    <div class="float-right">
                        <button class="btn btn-primary btn-sm float-right"><i class="fa fa-save"></i>&nbsp;Facturar</button>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="text-center  neo-fondo-tabla">
                        <th></th>   
                        <th>Fecha/Hora</th>
                        <th>Cita</th>
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
                @if(isset($ordenes))
                    @foreach($ordenes as $ordenAtencion)
                        <tr class="text-center">
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input miCheck" type="checkbox" name="orden[{{ $ordenAtencion->orden_id }}]" id="orden[{{ $ordenAtencion->orden_id }}]">
                                    <label for="orden[{{ $ordenAtencion->orden_id }}]" class="custom-control-label"></label>
                                </div>
                                <!--a href="{{ url("facturarOrden/{$ordenAtencion->orden_id}")}}" class="btn btn-xs btn-primary " style="padding: 2px 8px;" data-toggle="tooltip" data-placement="top" title="Facturar"><i class="fas fa-dollar-sign"></i></a-->
            
                            </td>
                            <td>
                                {{ $ordenAtencion->orden_fecha }} <br>
                                {{ $ordenAtencion->orden_hora }}
                            </td>
                            <td>{{ $ordenAtencion->orden_numero }}</td>
                            <td>@if(isset($ordenAtencion->especialidad->especialidad_nombre )) {{$ordenAtencion->especialidad->especialidad_nombre}} @endif</td>
                            <td>
                                @if(isset($ordenAtencion->medico->proveedor))
                                    {{$ordenAtencion->medico->proveedor->proveedor_nombre}}
                                @endif
                                @if(isset($ordenAtencion->medico->empleado))
                                    {{$ordenAtencion->medico->empleado->empleado_nombre}}
                                @endif
                            </td>
                            <td>
                                @if($ordenAtencion->orden_estado == 0 )
                                CANCELADA
                                @else
                                    @if($ordenAtencion->orden_estado == 1 )
                                        AGENDADA
                                    @else
                                        @if($ordenAtencion->orden_estado == 2 )
                                            PAGADA
                                        @else
                                            @if($ordenAtencion->orden_estado == 3 )
                                                PREATENDIDA
                                            @else
                                                @if($ordenAtencion->orden_estado == 4 )
                                                    ATENTIDA
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                @endif
                            </td>
                            <td>                        
                                <center>
                                    <img src="{{ asset('admin/imagenes/calendario.png') }}" data-toggle="tooltip" title="Agendada" class="brand-image" alt="NEOPAGUPA" style="width: 30px;">
                                </center>
                            </td>
                            <td>
                                @if($ordenAtencion->orden_estado == 2 || $ordenAtencion->orden_estado == 3 || $ordenAtencion->orden_estado == 4)
                                    <center>
                                        <img src="{{ asset('admin/imagenes/pagar.png') }}" data-toggle="tooltip" title="Pagada" class="brand-image" alt="NEOPAGUPA" style="width: 30px;">
                                    </center>
                                @endif
                            </td>
                            <td>
                                @if($ordenAtencion->orden_estado == 3)
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
                    @endforeach
                @endif
                </tbody>
            </table>
        </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<script>
    function comprobarSeleccionados(){
        cantidad=0;
        $(".miCheck").each(function () {
            if(this.checked) cantidad++
        });

        if(cantidad>0){
            return true
        }

        alert("Seleccione al menos una orden de atención para continuar")
        return false
    }
</script>
<script>
    setTimeout(function(){
        $(document).ready(function () {  
            $('#checkMarcar').on('click', function () {
                var checked_status = this.checked;
    
                $(".miCheck").each(function () {
                    this.checked = checked_status;
                });
            });
        })

        
    }, 600)

    
</script>
@endsection

