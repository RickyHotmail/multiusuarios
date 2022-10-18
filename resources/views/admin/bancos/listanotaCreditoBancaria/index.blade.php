@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("listanotaCreditoBancario") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Lista de Notas de Credito Bancario</h3>                                 
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>

                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
               
                <div class="col-sm-1">
                    <button type="submit"  class="btn btn-primary btn-sm" data-toggle="modal"><i class="fa fa-search"></i></button>
                </div>
            </div>   
            
            <hr>        
            <table id="example1" class="table table-bordered table-responsive table-hover sin-salto">
                <thead>              
                    <tr class="text-center neo-fondo-tabla">    
                    <th>Accion</th>
                    <th>Numero</th>
                    <th>Fecha</th>
                    <th>Banco</th> 
                    <th># Cuenta</th>
                    <th>Motivo</th>
                    <th>Beneficiario</th>
                    <th>Diario</th>  
                    <th>Valor</th>  
                    </tr>
                </thead>
                          
                <tbody>                                                                        
                    @if(isset($notaCredito)) 
                        @foreach($notaCredito as $x)
                            <tr class="text-center">
                            <td>
                                <a href="{{ url("listanotaCreditoBancario/{$x->nota_id}")}}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Visualizar"><i class="fa fa-eye"></i></a>    
                                <a href="{{ url("listanotaCreditoBancario/{$x->nota_id}/eliminar")}}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            </td>
                                <td>{{ $x->nota_numero}}</td>  
                                <td>{{ $x->nota_fecha}}</td>              
                                <td class="text-center">{{ $x->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</td>                        
                                <td class="text-center">{{ $x->cuentaBancaria->cuenta_bancaria_numero}}</td>
                                <td>{{ $x->nota_descripcion}} </td>
                                <td> {{ $x->nota_beneficiario}}</td>
                                <td class="text-center"><a href="{{ url("asientoDiario/ver/{$x->diario->diario_codigo}")}}" target="_blank">{{ $x->diario->diario_codigo}}</a></td>     

                                <td> <?php echo '$' . number_format($x->nota_valor, 2)?> </td>
                                                    
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>     
        </div>
    </div>
</form>
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