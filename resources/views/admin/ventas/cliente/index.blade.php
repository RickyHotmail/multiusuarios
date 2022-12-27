@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Clientes</h3>
        <div class="float-right">
            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
            <a class="btn btn-info btn-sm" href="{{ asset('admin/archivos/FORMATO_CLIENTES.xlsx') }}" download="FORMATO_CLIENTES"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
            <a class="btn btn-success btn-sm" href="{{ url("excelCliente")}}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>  
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Tipo de  Identificacion</th>
                    <th>Cedula</th>
                    <th>Nombre</th>
                    <th>Tipo de Cliente</th>
                    <th>Direccion</th>
                    <th>Telefono</th>
                    <th>Celular</th>
                    <th>Email</th>
                    <th>Fecha de Ingreso</th>
                    <th>Lleva Contabilidad</th>
                    <th>Tiene Credito</th>
                    @if(Auth::user()->empresa->empresa_contabilidad == '1')
                    <th>Cuenta por Cobrar</th>
                    <th>Cuenta Anticipo</th>
                    @endif
                    <th>Ciudad</th>
                    <th>Lista Precio</th>
                    <th>Categoria de Cliente</th>       
                    <th>Credito</th>                            
                </tr>
            </thead> 
            <tbody>
                @foreach($clientes as $cliente)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("cliente/{$cliente->cliente_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("cliente/{$cliente->cliente_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("cliente/{$cliente->cliente_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $cliente->tipoIdentificacion->tipo_identificacion_nombre}}</td>
                    <td>{{ $cliente->cliente_cedula}}</td>
                    <td>{{ $cliente->cliente_nombre}}</td>
                    <td>{{ $cliente->tipoCliente->tipo_cliente_nombre}}</td>
                    <td>{{ $cliente->cliente_direccion}}</td>
                    <td>{{ $cliente->cliente_telefono}}</td>   
                    <td>{{ $cliente->cliente_celular}}</td>
                    <td>{{ $cliente->cliente_email}}</td>
                    <td>{{ $cliente->cliente_fecha_ingreso}}</td>
                    <td>
                         @if($cliente->cliente_lleva_contabilidad=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                        @endif
                    </td>
                    <td>
                         @if($cliente->cliente_tiene_credito=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                        @endif
                    </td>
                    @if(Auth::user()->empresa->empresa_contabilidad == '1')
                        @if(isset($cliente->cuentaCobrar->cuenta_nombre))
                            <td>{{ $cliente->cuentaCobrar->cuenta_nombre}}</td>
                        @else
                            <td>Parametrizar cuentas</td>
                        @endif
                        @if(isset($cliente->cuentaAnticipo->cuenta_nombre))
                            <td>{{ $cliente->cuentaAnticipo->cuenta_nombre}}</td>
                        @else
                            <td>Parametrizar cuentas</td>
                        @endif
                    @endif
                    <td>{{ $cliente->ciudad->ciudad_nombre}}</td>
                    <td>@if(isset($cliente->listaPrecio)) {{ $cliente->listaPrecio->lista_nombre}} @endif</td>  
                    <td>{{ $cliente->categoriaCliente->categoria_cliente_nombre}}</td>  
                    <td>{{ $cliente->cliente_credito}}</td>                                   
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("cliente") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idTidentificacion" class="col-sm-3 col-form-label">Tipo de Identificacion</label>
                            <div class="col-sm-9">
                                <select onchange="verificarIdentificacion()" class="custom-select select2" id="idTidentificacion" name="idTidentificacion" require>
                                    @foreach($tipoIdentificacion as $tipoIdentificacion)
                                        <option value="{{$tipoIdentificacion->tipo_identificacion_id}}">{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idCedula" class="col-sm-3 col-form-label">Cedula/Ruc/Pasaporte</label>
                            <div class="col-sm-9">
                                <input onkeyup="verificarIdentificacion()" type="text" class="form-control" id="idCedula" name="idCedula" placeholder="9999999999" maxlength="13" required>
                                <i style="font-size:16px; display: none; margin:5px; padding-left:5px" id="lbl-verificacion" class="fa fa-check-circle"></i>
                            </div>
                        </div>
                                                
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" required>
                            </div>
                        </div>
                       
                        <div class="form-group row">
                            <label for="idTipoCliente" class="col-sm-3 col-form-label">Tipo de Cliente</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idTipoCliente" name="idTipoCliente" onchange="tipo();" required>
                                <option value="" selected disabled hidden></option>
                                    @foreach($tipoCliente as $tipoCliente)
                                        <option value="{{$tipoCliente->tipo_cliente_id}}">{{$tipoCliente->tipo_cliente_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" style="display:none;" id="tiposeguro">
                            <label  class="col-sm-3 col-form-label">Siglas de seguro</label>
                                <div class="col-sm-9">       
                                    <input type="text" class="form-control" id="idAbreviatura" name="idAbreviatura" placeholder="ASGR" required>                                    
                               </div>
                        </div>
                        <div class="form-group row">
                            <label for="idDireccion" class="col-sm-3 col-form-label">Direccion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idDireccion" name="idDireccion" placeholder="Direccion" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idTelefono" class="col-sm-3 col-form-label">Telefono</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idTelefono" name="idTelefono" placeholder="Telefono" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idCelular" class="col-sm-3 col-form-label">Celular</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCelular" name="idCelular" placeholder="Celular" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idEmail" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idEmail" name="idEmail" placeholder="Email" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idFecha" class="col-sm-3 col-form-label">Fecha de Ingreso</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idFecha" name="idFecha" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idLlevacontabilidad" class="col-sm-3 col-form-label">Lleva Contabilidad</label>
                            <div class="col-sm-3">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">                                   
                                    <input type="checkbox" class="custom-control-input" id="idLlevacontabilidad" name="idLlevacontabilidad">                                    
                                    <label class="custom-control-label" for="idLlevacontabilidad"></label>
                               </div>
                            </div>                
                            <label for="idTienecredito" class="col-sm-3 col-form-label">Tiene Credito</label>
                            <div class="col-sm-3">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">                                    
                                    <input type="checkbox" class="custom-control-input" id="idTienecredito" name="idTienecredito">
                                    <label class="custom-control-label" for="idTienecredito"></label>                                 
                               </div>
                            </div>                
                        </div> 
                        <div class="form-group row">
                            <label for="idCupoCredito" class="col-sm-3 col-form-label">Cupo Credito</label>
                            <div class="col-sm-9">                    
                                <input type="number" class="form-control" id="idCupoCredito" name="idCupoCredito" placeholder="0.00" value="0.00" step="any">
                            </div>                    
                        </div>    
                        @if(isset($parametrizacionContable->parametrizacion_cuenta_general))      
                            @if($parametrizacionContable->parametrizacion_cuenta_general == '0')          
                                              
                            <div class="form-group row">
                                <label for="idCuentaxcobrar" class="col-sm-3 col-form-label">Cuenta por Cobrar</label>
                                <div class="col-sm-9">
                                    <select class="custom-select select2" id="idCuentaxcobrar" name="idCuentaxcobrar" require>
                                        @foreach($cuentas as $cuenta)
                                            <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        @endif   
                        @if(isset($parametrizacionContableCliente->parametrizacion_cuenta_general))         
                            @if($parametrizacionContableCliente->parametrizacion_cuenta_general == '0')   
                            <div class="form-group row">
                                <label for="idCuentaAnticipo" class="col-sm-3 col-form-label">Cuenta Anticipo</label>
                                <div class="col-sm-9">
                                    <select class="custom-select select2" id="idCuentaAnticipo" name="idCuentaAnticipo" require>
                                        @foreach($cuentas as $cuenta)
                                            <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        @endif                        
                        <div class="form-group row">
                            <label for="idCategoria" class="col-sm-3 col-form-label">Categoria de Cliente</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idCategoria" name="idCategoria" require>
                                    @foreach($categoriaCliente as $categoriaCliente)
                                        <option value="{{$categoriaCliente->categoria_cliente_id}}">{{$categoriaCliente->categoria_cliente_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                         <div class="form-group row">
                            <label for="idCiudad" class="col-sm-3 col-form-label">Ciudad</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idCiudad" name="idCiudad" require>
                                    @foreach($ciudad as $ciudad)
                                        <option value="{{$ciudad->ciudad_id}}">{{$ciudad->ciudad_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                        
                        <div class="form-group row">
                            <label for="lista_id" class="col-sm-3 col-form-label">Lista de Precio</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="lista_id" name="lista_id">
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($precios as $lista)
                                        <option value="{{$lista->lista_id}}">{{$lista->lista_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script type="text/javascript">
    function verificarIdentificacion(){
        control=document.getElementById('idCedula')
        tam=control.value.length
        tipo=document.getElementById('idTidentificacion').value
        $("#lbl-verificacion").css('display','none')
        console.log("tipo "+tipo)

        if(((tipo==1 || (tipo==4 && control.value=='9999999999999')) && tam==13) || (tipo==2 && tam==10) || (tipo==3 && tam>=8)){
            $.ajax({
                url: "{{ url('comprobarIdentificacion') }}/"+control.value,
                data: '',            
                dataType: "json",
                type: "get",
                success: function (data) {
                    $("#lbl-verificacion").css('display','flex')

                    if(data.result || (tipo==4 && control.value=='9999999999999') || tipo==3){
                        $("#lbl-verificacion").css('color','green')
                        $("#lbl-verificacion").text(' Correcto')
                    }
                    else{
                        $("#lbl-verificacion").css('color','red')
                        $("#lbl-verificacion").text(' Identificación inválida')
                    }
                    
                    console.log("comp "+data.result)
                }
            });
        }
        else if(tam>0){
            $("#lbl-verificacion").css('display','flex')
            $("#lbl-verificacion").css('color','grey')

            if(tipo==1) $("#lbl-verificacion").text('El RUC debe tener 13 dígitos')
            if(tipo==2) $("#lbl-verificacion").text('La cédula debe tener 10 dígitos')
            if(tipo==3) $("#lbl-verificacion").text('El Pasaporte debe tener mínimo 8 dígitos')
            if(tipo==4) $("#lbl-verificacion").text('El Consumidor Final debe contener 13 dígitos: 9999999999999')
        }
        else
            $("#lbl-verificacion").css('display','none')
    }

    function tipo(){
        var combo = document.getElementById("idTipoCliente");
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
</script>
@endsection