@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Signos Vitales</h3>
        <div class="float-right">
            <button type="button" onclick="location.reload()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Actualizar</button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center  neo-fondo-tabla">
                    <th></th>
                    <th>Numero Orden</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Cedula</th>
                    <th>Paciente</th>
                    <th>Medico</th>
                    <th>Especialidad</th>
                    
                </tr>
            </thead> 
            <tbody>     
                @foreach($ordenes as $orden)
                    @if($orden->orden_estado=='2')
                        <tr class="text-center">
                            <td>
                                <a href="{{ url("nuevoSignosV/{$orden->orden_id}")}}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Atender">&nbsp;&nbsp;<i class="fa fa-calendar-check"></i>&nbsp;&nbsp;</a> 
                            </td>
                            <td>
                                {{$orden->orden_numero }} &nbsp;
                                @if($orden->orden_iess==1)
                                    <img src="{{ asset('img/iess.png')  }}" width="50px">
                                @endif
                            </td> 
                            <td>{{$orden->orden_fecha }}</td>  
                            <td>{{$orden->orden_hora }}</td>                    
                            <td>{{$orden->paciente->paciente_cedula }} </td>  
                            <td>{{$orden->paciente->paciente_apellidos }} {{$orden->paciente->paciente_nombres }}</td>  
                            <td>@if($orden->medico->proveedor){{$orden->medico->proveedor->proveedor_nombre}} @else @if($orden->medico->empleado) {{$orden->medico->empleado->empleado_nombre}} @endif @endif </td>  
                            <td>{{$orden->especialidad->especialidad_nombre }}</td>
                           
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection