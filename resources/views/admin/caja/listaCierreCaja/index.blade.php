@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Cierres de Caja</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listaCierreCaja") }}">
        @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
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
                    <th>Hora</th>                    
                    <th>Descripcion</th>                  
                    <th>Caja</th>
                    <th>Usuario</th>    
                    <th>Saldo de Apertura</th>
                    <th>Saldo de Cierre</th>                      
                </tr>
            </thead>             
            <tbody> 
            @if(isset($cierresCaja))
                @foreach($cierresCaja as $cierreCaja)
                    <tr class="text-center">
                        <td>
                            <a href="{{ url("listaCierreCaja/{$cierreCaja->arqueo_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a href="{{ url("cierreCajaPdf/imprimir/{$cierreCaja->arqueo_id}") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a>   
                        </td>
                            <td class="text-center">{{ $cierreCaja->arqueo_fecha}}</td>
                            <td class="text-center">{{ $cierreCaja->arqueo_hora}}</td>
                            <td class="text-center">{{ $cierreCaja->arqueo_observacion}}</td>
                            <td class="text-center">{{ $cierreCaja->caja->caja_nombre}}</td>
                            <td class="text-center">{{ $cierreCaja->usuario->user_nombre}}</td>                       
                        <td class="text-rigth">${{ number_format($cierreCaja->arqueo_saldo_inicial,2)}}</td>
                        <td class="text-rigth">${{ number_format($cierreCaja->arqueo_monto,2)}}</td>                          
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