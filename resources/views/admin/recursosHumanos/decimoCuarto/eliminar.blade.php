@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
    <h3 class="card-title">¿Esta seguro de eliminar datos del decimo cuarto del empleado?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('individualdecimoCuarto.destroy', [$decimo->decimo_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-success btn-sm ">Eliminar</button>
                <a href="{{ url("lquincena") }}" class="btn btn-danger btn-sm">Cancelar</a>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos</h5>
           
            <div class="card-body">     
                                        
                        <div class="form-group row">
                            <label for="idSucursal" class="col-sm-2 col-form-label">Mes y Año</label>
                            <div class="col-sm-2">
                                <input type="month" name="fecha_desde" id="fecha_desde" class="form-control" value='<?php echo(DateTime::createFromFormat('Y-m-d', ($decimo->decimo_fecha))->format('Y').'-'.DateTime::createFromFormat('Y-m-d', ($decimo->decimo_fecha))->format('m')); ?>' readonly>
                                
                            </div>
                            <div class="col-sm-2">
                            <input type="month" name="fecha_hasta" id="fecha_desde" class="form-control" value='<?php echo((DateTime::createFromFormat('Y-m-d', ($decimo->decimo_fecha))->format('Y')+1)."-".date("02")); ?>' readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="empleado_id" class="col-sm-2 col-form-label">Empleado</label>
                            <div class="col-sm-10">
                                <label class="form-control" id="idEmpleado" name="idEmpleado" >{{$decimo->empleado->empleado_nombre}}</label>       
                            </div>
                           
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">Fecha de Emision</label>
                            <div class="col-sm-10">
                                <label class="form-control" id="idEmpleado" name="idEmpleado" >{{$decimo->decimo_fecha_emision}}</label>  
                               
                            </div>
                        </div>                
                        <div class="form-group row">
                            <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
                            <div class="col-sm-10">
                            <label class="form-control" id="idEmpleado" name="idEmpleado" >{{number_format($decimo->decimo_valor,2)}}</label>  
                               
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label for="idMensaje" class="col-sm-2 col-form-label">Descripcion</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="idMensaje" name="idMensaje" value="{{$decimo->decimo_descripcion}}" required readonly>
                               
                            </div>
                        </div>  
                        
                
            </div>
                
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Forma de Pago</h5>   
            <div class="card-body">
            <div class="form-group row">
                    <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Pago</label>
                    <div class="col-sm-10">
                    <label class="form-control" id="idEmpleado" name="idEmpleado" >{{$decimo->decimo_tipo}}</label>  
                     <input type="hidden" id="iddecimo" name="iddecimo" value="{{$decimo->decimo_id}}"/>          
                       
                    </div>
                </div>
               
                    <div class="form-group row">
                            <label for="banco_id" class="col-sm-2 col-form-label">Banco</label>
                            <div class="col-sm-10">
                            <label class="form-control" >
                                @if($decimo->decimo_tipo=="Cheque")
                                   {{$cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}
                                @endif
                                @if($decimo->decimo_tipo=="Transferencia")
                                   {{$transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}
                                @endif
                            </label>   
                            </div>
                    </div>
                    <div class="form-group row">
                            <label for="cuenta_id" class="col-sm-2 col-form-label"># de Cuenta</label>
                            <div class="col-sm-10">
                            <label class="form-control" id="ncuenta" name="ncuenta"  >
                            @if($decimo->decimo_tipo=="Cheque")
                                   {{$cheque->cuentaBancaria->cuenta_bancaria_numero}}
                                @endif
                                @if($decimo->decimo_tipo=="Transferencia")
                                   {{$transferencia->cuentaBancaria->cuenta_bancaria_numero}}
                                @endif
                            </label> 
                            </div>
                    </div> 
                    <div class="form-group row">
                            <label for="idCuentaContable" class="col-sm-2 col-form-label">Cuenta Contable</label>
                            <div class="col-sm-10">
                            <label class="form-control" id="idCuentaContable" name="idCuentaContable"  >           
                            @if($decimo->decimo_tipo=="Cheque")
                                   {{$cheque->cuentaBancaria->cuenta->cuenta_numero}}-{{$cheque->cuentaBancaria->cuenta->cuenta_nombre}}
                                   cuenta_nombre
                                @endif
                                @if($decimo->decimo_tipo=="Transferencia")
                                   {{$transferencia->cuentaBancaria->cuenta->cuenta_numero}}-{{$transferencia->cuentaBancaria->cuenta->cuenta_nombre}}
                                @endif
                            </label>   
                            </div>
                    </div>         
                            
                           
                 
            </div>  
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos del Cheque</h5>
            <div class="card-body">
                <div class="form-group row">
                            <label for="idFechaCheque" class="col-sm-2 col-form-label">Fecha</label>
                            <div class="col-sm-10">
                            <label class="form-control" id="idFechaCheque" name="idFechaCheque"  >
                            @if($decimo->decimo_tipo=="Cheque")
                                   {{$cheque->cheque_fecha_emision}}
                                @endif
                                @if($decimo->decimo_tipo=="Transferencia")
                                   {{$transferencia->transferencia_fecha}}
                                @endif
                            </label>   
                            </div>
                </div>                
                <div class="form-group row">
                    <label for="idNcheque" class="col-sm-2 col-form-label"># de Cheque</label>
                    <div class="col-sm-10">
                    <label class="form-control" >
                    @if($decimo->decimo_tipo=="Cheque")
                                   {{$cheque->cheque_numero}}
                                @endif
                    </label>  
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idBeneficiario" class="col-sm-2 col-form-label">Beneficiario</label>
                    <div class="col-sm-10">
                    <label class="form-control" >
                    @if($decimo->decimo_tipo=="Cheque")
                                   {{$cheque->cheque_beneficiario}}
                                @endif
                                @if($decimo->decimo_tipo=="Transferencia")
                                   {{$transferencia->transferencia_beneficiario}}
                                @endif
                    </label>   
                   
                    </div>
                </div>             
                 
            </div>                   
        
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection
