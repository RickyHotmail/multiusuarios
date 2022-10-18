@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Ingresos de Caja</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listaIngresoCaja") }}">
        @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php if(isset($fechaDesde)) echo($fechaDesde); else  echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
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
        <hr>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">
                    <th>Accion</th>
                    <th>Fecha</th>
                    <th>Numero</th>
                    <th>Tipo</th>
                    <th>Beneficiario</th>
                    @if(Auth::user()->empresa->empresa_contabilidad == '1')
                    <th>Diario</th>  
                    @endif
                    <th>Valor</th>    
                    <th>Comentario</th>               
                </tr>
            </thead>             
            <tbody> 
            @if(isset($ingresoCajas))
                @foreach($ingresoCajas as $ingresoCaja)
                    <tr class="text-center">
                        <td>
                            <a href="{{ url("listaIngresoCaja/{$ingresoCaja->ingreso_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            @if($ingresoCaja->ingreso_tipo == 'EFECTIVO')
                                @if(is_null($ingresoCaja->arqueo->cierre_id))
                                    <a href="{{ url("listaIngresoCaja/{$ingresoCaja->ingreso_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif
                            @endif                    
                        </td>
                            <td class="text-center">{{ $ingresoCaja->ingreso_fecha}}</td>
                            <td class="text-center">{{ $ingresoCaja->ingreso_numero}}</td>
                            <td class="text-center">{{ $ingresoCaja->ingreso_tipo}}</td>
                            <td class="text-center">{{ $ingresoCaja->ingreso_beneficiario}}</td> 
                            @if(Auth::user()->empresa->empresa_contabilidad == '1')
                            <td class="text-center"><a href="{{ url("asientoDiario/ver/{$ingresoCaja->diario->diario_codigo}") }}" target="_blank">{{ $ingresoCaja->diario->diario_codigo}}</a></td>     
                            @endif              
                        <td class="text-rigth">${{ number_format($ingresoCaja->ingreso_valor,2)}}</td>   
                        <td class="text-center">{{ $ingresoCaja->ingreso_descripcion}}</td>                     
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