@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Egresos de Banco</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listaEgresoBanco") }}">
        @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  @if(isset($fechaI)) value='{{ $fechaI }}'  @else value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  @if(isset($fechaF)) value='{{ $fechaF }}'  @else value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif required>
                </div>    
               
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>            
        </form>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th>Accion</th>
                    <th>Fecha</th>
                    <th>Banco</th>
                    <th># Cheque</th>
                    <th>Descripcion</th>
                    <th>Beneficiario</th>
                    <th>Diario</th>  
                    <th>Valor</th>                   
                </tr>
            </thead>             
            <tbody> 
            @if(isset($egresoBancos))
                @foreach($egresoBancos as $egresoBanco)
                    <tr class="text-center">
                        <td>
                            <a href="{{ url("listaEgresoBanco/{$egresoBanco->egreso_id}")}}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a href="{{ url("listaEgresoBanco/{$egresoBanco->egreso_id}/eliminar")}}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            @if($egresoBanco->cheque_id!=Null)
                            <a href="{{ url("listaEgresoBanco/{$egresoBanco->egreso_id}/anular") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Anular"><i class="fa fa-ban" aria-hidden="true"></i></a>                        
                            @endif
                            @if(isset($egresoBanco->cheque))
                                <a href="{{ url("/cheque/imprimir/{$egresoBanco->cheque->cheque_id}") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir Cheque"><i class="fas fa-money-check-alt" aria-hidden="true"></i></a>                        
                            @endif 
                        </td>
                            <td class="text-center">{{ $egresoBanco->egreso_fecha}}</td>
                            @if(is_null($egresoBanco->cheque_id))
                                <td class="text-center">{{ $egresoBanco->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</td>                        
                                <td class="text-center">TRANSFERENCIA</td>
                            @else
                                <td class="text-center">{{ $egresoBanco->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</td>                        
                                <td class="text-center">{{ $egresoBanco->cheque->cheque_numero}}</td>
                            @endif
                            <td class="text-center">{{ $egresoBanco->egreso_descripcion}}</td>
                            <td class="text-center">{{ $egresoBanco->egreso_beneficiario}}</td> 
                            <td class="text-center"><a href="{{ url("asientoDiario/ver/{$egresoBanco->diario->diario_codigo}")}}" target="_blank">{{ $egresoBanco->diario->diario_codigo}}</a></td>                   
                        <td class="text-rigth">${{ number_format($egresoBanco->egreso_valor,2)}}</td>                        
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