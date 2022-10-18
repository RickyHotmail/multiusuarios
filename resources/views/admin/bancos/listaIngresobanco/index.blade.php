@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Ingresos de Banco</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listaIngresoBanco") }}">
        @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                </div>    
               
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>            
        </form>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center  neo-fondo-tabla">
                    <th>Accion</th>
                    <th>Fecha</th>
                    <th>Banco</th>
                    <th># Cuenta</th>
                    <th>Descripcion</th>
                    <th>Beneficiario</th>
                    <th>Diario</th>  
                    <th>Valor</th>                   
                </tr>
            </thead>             
            <tbody> 
            @if(isset($ingresoBancos))
                @foreach($ingresoBancos as $ingresoBanco)
                    <tr class="text-center">
                        <td>
                            <a href="{{ url("listaIngresoBanco/{$ingresoBanco->ingreso_id}")}}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a href="{{ url("listaIngresoBanco/{$ingresoBanco->ingreso_id}/eliminar")}}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                           
                           
                            @if(isset($ingresoBanco->cheque))
                                <a href="{{ url("/cheque/imprimir/{$ingresoBanco->cheque->cheque_id}") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir Cheque"><i class="fas fa-money-check-alt" aria-hidden="true"></i></a>                        
                            @endif   
                        
                        </td>
                            <td class="text-center">{{ $ingresoBanco->ingreso_fecha}}</td>
                                @if($ingresoBanco->deposito_id)
                                    <td class="text-center">{{ $ingresoBanco->deposito->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</td>                        
                                    <td class="text-center">{{ $ingresoBanco->deposito->cuentaBancaria->cuenta_bancaria_numero}}</td>
                                @else
                                <td></td>
                                <td></td>
                                @endif
                            <td class="text-center">{{ $ingresoBanco->ingreso_descripcion}}</td>
                            <td class="text-center">{{ $ingresoBanco->ingreso_beneficiario}}</td> 
                            @if($ingresoBanco->diario_id)
                                <td class="text-center"><a href="{{ url("asientoDiario/ver/{$ingresoBanco->diario->diario_codigo}")}}" target="_blank">{{ $ingresoBanco->diario->diario_codigo}}</a></td>                   
                            @else
                                <td></td>
                            @endif
                            <td class="text-rigth">${{ number_format($ingresoBanco->ingreso_valor,2)}}</td>                        
                    </tr>                         
                @endforeach   
            @endif            
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<script>
      <?php
   if(isset($fecha_hasta)){  
        ?>
         document.getElementById("fecha_hasta").value='<?php echo($fecha_hasta); ?>';
         <?php
    }
    if (isset($fecha_desde)) {
        ?>
       document.getElementById("fecha_desde").value='<?php echo($fecha_desde); ?>';
    
    <?php
    }
    if(isset($descripcion)){  
        ?>
       document.getElementById("descripcion").value='<?php echo($descripcion); ?>';
        <?php
    }
    ?>
</script>
@endsection