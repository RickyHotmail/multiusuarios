@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Prestamos</h3>
        <button type="button" onclick='window.location = "{{ url("prestamos") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        <a class="btn btn-info btn-sm float-right" href="{{ asset('admin/archivos/FORMATO_INTERESES.xlsx') }}" download="FORMATO INTERESES"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
        <a class="btn btn-success btn-sm float-right" href="{{ url("/detalleprestamos/{$ide}/cargar") }}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Fecha</th>
                    <th>Interes</th>  
                    <th>Capital</th>  
                    <th>Monto Interes</th>      
                    <th>Diario</th>                                
                </tr>
            </thead>            
            <tbody>
            <?php $valores= 0?>
                    @foreach($detalles as $detalle)
                    <tr class="text-center">
                        <td>
                            @if(!isset($detalle->diario->diario_id))<a href="{{ url("detalleprestamos/{$detalle->detalle_id}/editar") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>@endif
                            <a href="{{ url("detalleprestamos/{$detalle->detalle_id}/ver") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a href="{{ url("detalleprestamos/{$detalle->detalle_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                        <td>{{ $detalle->detalle_fecha }}</td>  
                        <td>{{ $detalle->detalle_total}}</td>    
                        <td>{{ round($detalle->prestamo->prestamo_monto,2)}}</td> 
                        @if(isset($detalle->diario->diario_id))
                            <?php $valores= $valores+$detalle->detalle_total?>
                        @endif
                        <td> @if(isset($detalle->diario->diario_id))
                        {{round($valores+$detalle->prestamo->prestamo_monto,2) }}
                            @else
                                0
                        @endif</td>    
                        <td >@if(isset($detalle->diario->diario_id))<a href="{{ url("asientoDiario/ver/{$detalle->diario->diario_id}") }}" target="_blank">{{ $detalle->diario->diario_codigo }}</a>@endif</td>  
                    </tr>
                    @endforeach
          
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>

@endsection