@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('cliente.update', [$cliente->cliente_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar cliente</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--
                <button type="button" onclick='window.location = "{{ url("cliente") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->      
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>  
            </div>
        </div>
        <div class="card-body">
        <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Identificacion</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idTipo" name="idTipo" require>
                        @foreach($tipoIdentificacions as $tipoIdentificacion)
                            @if($tipoIdentificacion->tipo_identificacion_id == $cliente->tipo_identificacion_id)
                                <option value="{{$tipoIdentificacion->tipo_identificacion_id}}" selected>{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                            @else 
                                <option value="{{$tipoIdentificacion->tipo_identificacion_id}}">{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>
            
            <div class="form-group row">
                <label for="idCedula" class="col-sm-2 col-form-label">Cedula/Ruc/Pasaporte</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCedula" name="idCedula" value="{{$cliente->cliente_cedula}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNombre" name="idNombre" value="{{$cliente->cliente_nombre}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idDireccion" class="col-sm-2 col-form-label">Direccion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idDireccion" name="idDireccion" value="{{$cliente->cliente_direccion}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTcliente" class="col-sm-2 col-form-label">Tipo de Cliente</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idTcliente" name="idTcliente" onchange="tipo();" required>
                        @foreach($tipoClientes as $tipoCliente)
                            @if($tipoCliente->tipo_cliente_id == $cliente->tipo_cliente_id)
                                <option value="{{$tipoCliente->tipo_cliente_id}}" selected>{{$tipoCliente->tipo_cliente_nombre}}</option>
                            @else 
                                <option value="{{$tipoCliente->tipo_cliente_id}}">{{$tipoCliente->tipo_cliente_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>
            
            <div class="form-group row"  id="tiposeguro">
                <label  class="col-sm-2 col-form-label">Siglas de seguro</label>
                    <div class="col-sm-10">       
                        <input type="text" class="form-control" id="idAbreviatura" name="idAbreviatura" value="@if($cliente->tipoCliente->tipo_cliente_nombre=='Aseguradora'){{$cliente->cliente_abreviatura}}@endif" placeholder="ASGR" required>                                    
                    </div>
            </div>
            <div class="form-group row">
                <label for="idTelefono" class="col-sm-2 col-form-label">Telefono</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idTelefono" name="idTelefono"  value="{{$cliente->cliente_telefono}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCelular" class="col-sm-2 col-form-label">Celular</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCelular" name="idCelular" value="{{$cliente->cliente_celular}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEmail" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="" class="form-control" id="idEmail" name="idEmail" placeholder="Email" value="{{$cliente->cliente_email}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idAutorizacion" class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="idFecha" name="idFecha" value="{{$cliente->cliente_fecha_ingreso}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idContabilidad" class="col-sm-2 col-form-label">Lleva Contabilidad</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($cliente->cliente_lleva_contabilidad=="1")
                            <input type="checkbox" class="custom-control-input" id="idContabilidad" name="idContabilidad" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idContabilidad" name="idContabilidad">
                        @endif
                        <label class="custom-control-label" for="idContabilidad"></label>
                    </div>
                </div>                
            </div>
            <div class="form-group row">
                <label for="idTienecredito" class="col-sm-2 col-form-label">Tiene Credito</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($cliente->cliente_tiene_credito=="1")
                            <input type="checkbox" class="custom-control-input" id="idTienecredito" name="idTienecredito" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idTienecredito" name="idTienecredito">
                        @endif
                        <label class="custom-control-label" for="idTienecredito"></label>
                    </div>
                </div>                
            </div>
            <div class="form-group row">
                <label for="idCupoCredito" class="col-sm-2 col-form-label">Cupo Credito</label>
                <div class="col-sm-10">                    
                    <input type="number" class="form-control" id="idCupoCredito" name="idCupoCredito" value="{{$cliente->cliente_credito}}" placeholder="0.00" value="0.00" step="any">
                </div>                    
            </div>   
            @if($parametrizacionContable->parametrizacion_cuenta_general == '0')             
                @if(Auth::user()->empresa->empresa_contabilidad == '1')
                <div class="form-group row">
                    <label for="idCobrar" class="col-sm-2 col-form-label">Cuenta por Cobrar</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="idCobrar" name="idCobrar">
                            <option value="" label>--Seleccione una opcion--</option>
                            @foreach($cuentas as $cuenta)
                                @if($cuenta->cuenta_id == $cliente->cliente_cuenta_cobrar)
                                    <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @else 
                                    <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>                    
                </div>
                <div class="form-group row">
                    <label for="idAnticipo" class="col-sm-2 col-form-label">Cuenta de Anticipo</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="idAnticipo" name="idAnticipo">
                            <option value="" label>--Seleccione una opcion--</option>
                            @foreach($cuentas as $cuenta)
                                @if($cuenta->cuenta_id == $cliente->cliente_cuenta_anticipo)
                                    <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @else 
                                    <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>                    
                </div>
                @endif
            @endif             
            <div class="form-group row">
                <label for="idCiudad" class="col-sm-2 col-form-label">Ciudad</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idCiudad" name="idCiudad" require>
                        @foreach($ciudades as $ciudad)
                            @if($ciudad->ciudad_id == $cliente->ciudad_id)
                                <option value="{{$ciudad->ciudad_id}}" selected>{{$ciudad->ciudad_nombre}}</option>
                            @else 
                                <option value="{{$ciudad->ciudad_id}}">{{$ciudad->ciudad_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>   
            <div class="form-group row">
                <label for="idCategoria" class="col-sm-2 col-form-label">Categoria de Cliente</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idCategoria" name="idCategoria" required>
                        @foreach($categoriaClientes as $categoriaCliente)
                            @if($categoriaCliente->categoria_cliente_id == $cliente->categoria_cliente_id)
                                <option value="{{$categoriaCliente->categoria_cliente_id}}" selected>{{$categoriaCliente->categoria_cliente_nombre}}</option>
                            @else 
                                <option value="{{$categoriaCliente->categoria_cliente_id}}">{{$categoriaCliente->categoria_cliente_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>             
            <div class="form-group row">
                <label for="lista_id" class="col-sm-2 col-form-label">Lista de Precio</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="lista_id" name="lista_id">
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($precios as $lista)
                            <option value="{{$lista->lista_id}}" @if($lista->lista_id == $cliente->lista_id) selected @endif>{{$lista->lista_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>                                                
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($cliente->cliente_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                        @endif
                        <label class="custom-control-label" for="bodega_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
<script type="text/javascript">
        function cargarmetodo(){
            var combo = document.getElementById("idTcliente");
            var idTipoCliente = combo.options[combo.selectedIndex].text; 
            div = document.getElementById('tiposeguro');   
          
            if(idTipoCliente=="Aseguradora"){
                div.style.display = '';  
                $('#idAbreviatura').prop("required", true);     
            }
            else{
                document.getElementById('idAbreviatura').value=" "; 
                div.style.display = 'none';
                $('#idAbreviatura').removeAttr("required"); 
               
                 
            }
        }
        function tipo(){
            var combo = document.getElementById("idTcliente");
            var idTipoCliente = combo.options[combo.selectedIndex].text;
            div = document.getElementById('tiposeguro');
            if(idTipoCliente=="Aseguradora"){
                div.style.display = '';  
                $('#idAbreviatura').prop("required", true);     
            }
            else{
                document.getElementById('idAbreviatura').value="";
                div.style.display = 'none';
                $('#idAbreviatura').removeAttr("required");  
                
                 
            }   
        }
</script>
@endsection