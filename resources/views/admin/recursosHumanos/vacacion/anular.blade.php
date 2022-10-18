@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de anular la vacacion del empleado?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ url("vacacionanular") }} " >
                @csrf
                <button type="submit" class="btn btn-success btn-sm ">Anular</button>
                <input type="hidden" id="idvacacion" name="idvacacion" value="{{$vacacion->vacacion_id}}"/>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("lvacaciones") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos</h5>
            <form class="form-horizontal">
            @csrf
            <div class="card-body">
                        <div class="form-group row">
                            <label for="idSucursal" class="col-sm-2 col-form-label">Numero</label>
                            <div class="col-sm-10">
                                <label class="form-control" id="idEmpleado" name="idEmpleado" >{{$vacacion->vacacion_numero}}</label>       
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idSucursal" class="col-sm-2 col-form-label">Empleado</label>
                            <div class="col-sm-10">
                            <label class="form-control" id="idEmpleado" name="idEmpleado" >{{$vacacion->empleado->empleado_nombre}}</label>   
                                
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idFecha" class="col-sm-2 col-form-label">Fecha de Ingreso</label>
                            <div class="col-sm-10">
                                <label class="form-control" id="idFecha" name="idFecha" >{{$vacacion->vacacion_fecha}}</label>   
                            
                               
                            </div>
                        </div>                
                        <div class="form-group row">
                            <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
                            <div class="col-sm-10">
                                <label class="form-control" id="idValor" name="idValor" ">{{$vacacion->vacacion_valor}}</label>   
                            
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label for="idMensaje" class="col-sm-2 col-form-label">Descripcion</label>
                            <div class="col-sm-10">
                            <label class="form-control" id="idValor" name="idValor" ">{{$vacacion->vacacion_descripcion}}</label>   
                            
                            </div>
                        </div>  
                        
                
            </div>
                
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Forma de Pago</h5>   
            <div class="card-body">
            <div class="form-group row">
                    <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Pago</label>
                    <div class="col-sm-10">
                    <label class="form-control" id="idTipo" name="idTipo"  >{{$vacacion->vacacion_tipo}}</label>   
                            
                       
                    </div>
                </div>
               
                    <div class="form-group row">
                            <label for="banco_id" class="col-sm-2 col-form-label">Banco</label>
                            <div class="col-sm-10">
                            <label class="form-control" >
                            @if($vacacion->vacacion_tipo=="Cheque")
                                   {{$cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}
                                @endif
                                @if($vacacion->quincena_tipo=="Transferencia")
                                   {{$transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}
                                @endif
                            </label>   
                    
                                
                            </div>
                    </div>
                    <div class="form-group row">
                            <label for="cuenta_id" class="col-sm-2 col-form-label"># de Cuenta</label>
                            <div class="col-sm-10">
                            <label class="form-control" id="ncuenta" name="ncuenta"  >
                            @if($vacacion->vacacion_tipo=="Cheque")
                                   {{$cheque->cuentaBancaria->cuenta_bancaria_numero}}
                                @endif
                                @if($vacacion->vacacion_tipo=="Transferencia")
                                   {{$transferencia->cuentaBancaria->cuenta_bancaria_numero}}
                                @endif
                            </label>   
                            </div>
                    </div> 
                    <div class="form-group row">
                            <label for="idCuentaContable" class="col-sm-2 col-form-label">Cuenta Contable</label>
                            <div class="col-sm-10">
                            <label class="form-control" id="idCuentaContable" name="idCuentaContable"  >           
                            @if($vacacion->vacacion_tipo=="Cheque")
                                   {{$cheque->cuentaBancaria->cuenta->cuenta_numero}}-{{$cheque->cuentaBancaria->cuenta->cuenta_nombre}}
                                   cuenta_nombre
                                @endif
                                @if($vacacion->vacacion_tipo=="Transferencia")
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
                            @if($vacacion->vacacion_tipo=="Cheque")
                                   {{$cheque->cheque_fecha_emision}}
                                @endif
                                @if($vacacion->vacacion_tipo=="Transferencia")
                                   {{$transferencia->transferencia_fecha}}
                                @endif
                            </label>   
                    
                            </div>
                </div>                
                <div class="form-group row">
                    <label for="idNcheque" class="col-sm-2 col-form-label"># de Cheque</label>
                    <div class="col-sm-10">
                    <label class="form-control" >
                                @if($vacacion->vacacion_tipo=="Cheque")
                                   {{$cheque->cheque_numero}}
                                @endif
                    </label>   
                    
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idBeneficiario" class="col-sm-2 col-form-label">Beneficiario</label>
                    <div class="col-sm-10">
                    <label class="form-control" >
                                @if($vacacion->vacacion_tipo=="Cheque")
                                   {{$cheque->cheque_beneficiario}}
                                @endif
                                @if($vacacion->vacacion_tipo=="Transferencia")
                                   {{$transferencia->transferencia_beneficiario}}
                                @endif
                    </label>   
                   
                    </div>
                </div>             
                 
            </div>                  
            <!-- /.card-body -->
                      
            <!-- /.card-footer -->
        </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection
<script type="text/javascript">
 


</script>