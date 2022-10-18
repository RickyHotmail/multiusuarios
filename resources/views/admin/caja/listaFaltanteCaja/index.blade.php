@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Faltante de Caja</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listaFaltanteCaja") }}">
        @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php if(isset($fechaDesde)) echo($fechaDesde); else echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php if(isset($fechahasta)) echo($fechahasta); else echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                </div>    
               
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>            
        </form>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">
                    <th>Accion</th>
                    <th>Fecha</th>
                    <th>Numero</th>
                    <th>Descripcion</th>  
                    @if(Auth::user()->empresa->empresa_contabilidad == '1')                
                    <th>Diario</th>  
                    @endif
                    <th>Valor</th>                   
                </tr>
            </thead>             
            <tbody> 
            @if(isset($faltanteCajas))
                @foreach($faltanteCajas as $faltanteCaja)
                    <tr class="text-center">
                        <td>
                            <a href="{{ url("listaFaltanteCaja/{$faltanteCaja->faltante_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            
                            @if(is_null($faltanteCaja->arqueo->cierre_id))
                                <a href="{{ url("listaFaltanteCaja/{$faltanteCaja->faltante_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            @endif                         
                        </td>
                            <td class="text-center">{{ $faltanteCaja->faltante_fecha}}</td>
                            <td class="text-center">{{ $faltanteCaja->faltante_numero}}</td>
                            <td class="text-center">{{ $faltanteCaja->faltante_observacion}}</td>
                            @if(Auth::user()->empresa->empresa_contabilidad == '1')
                                <td class="text-center"><a href="{{ url("asientoDiario/ver/{$faltanteCaja->diario->diario_codigo}") }}" target="_blank">{{ $faltanteCaja->diario->diario_codigo}}</a></td> 
                            @endif                  
                        <td class="text-rigth">${{ number_format($faltanteCaja->faltante_monto,2)}}</td>                        
                    </tr>                         
                @endforeach
            @endif               
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection