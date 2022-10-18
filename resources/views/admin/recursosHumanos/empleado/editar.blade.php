@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('empleado.update', [$empleado->empleado_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Empleado</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("empleado") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">Datos Generales</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="adicional-tab" data-toggle="tab" href="#adicional" role="tab" aria-controls="adicional" aria-selected="false">Datos Adicionales</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <!--Datos Generales-->
                    <br>
                    <div class="form-group row">
                        <label for="idCedula" class="col-sm-3 col-form-label">Cedula</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="idCedula" name="idCedula" value="{{$empleado->empleado_cedula}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idNombre" class="col-sm-3 col-form-label">Nombre</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="idNombre" name="idNombre" value="{{$empleado->empleado_nombre}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idSexo" class="col-sm-3 col-form-label">Sexo</label>
                        <div class="col-sm-3">
                            <select id="idSexo" name="idSexo" class="form-control show-tick " data-live-search="true" required>
                                <option value="" label>--Seleccione una opcion--</option>
                                <option value="Masculino" @if($empleado->empleado_sexo == "Masculino") selected @endif>Masculino</option>
                                <option value="Femenino" @if($empleado->empleado_sexo == "Femenino") selected @endif>Femenino</option>
                            </select>
                        </div>
                        <label for="idEstatura" class="col-sm-3 col-form-label">Estatura</label>
                        <div class="col-sm-3 input-group mb-2">
                            <input type="text" class="form-control" id="idEstatura" name="idEstatura" value="{{$empleado->empleado_estatura}}" required>
                            <div class="input-group-append">
                                <div class="input-group-text">cm</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idGrupoS" class="col-sm-3 col-form-label">Grupo Sanguineo</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="idGrupoS" name="idGrupoS" value="{{$empleado->empleado_grupo_sanguineo}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idLugarNac" class="col-sm-3 col-form-label">Lugar de Nacimiento</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="idLugarNac" name="idLugarNac" value="{{$empleado->empleado_lugar_nacimiento}}" required>
                        </div>
                        <label for="idFechaNac" class="col-sm-3 col-form-label">Fecha de Nacimiento</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="idFechaNac" name="idFechaNac" value="{{$empleado->empleado_fecha_nacimiento}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idEdad" class="col-sm-3 col-form-label">Edad</label>
                        <div class="col-sm-3">
                            <input type="number" class="form-control" id="idEdad" name="idEdad" value="{{$empleado->empleado_edad}}" required>
                        </div>
                        <label for="idNacionalidad" class="col-sm-3 col-form-label">Nacionalidad</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="idNacionalidad" name="idNacionalidad" value="{{$empleado->empleado_nacionalidad}}" required>
                        </div>

                    </div>
                    <div class="form-group row">
                        <label for="idEstadoCivil" class="col-sm-3 col-form-label">Estado Civil</label>
                        <div class="col-sm-9">
                            <select id="idEstadoCivil" name="idEstadoCivil" class="form-control show-tick " data-live-search="true" required>
                                <option value="" label>--Seleccione una opcion--</option>
                                <option value="SOLTERO/A" @if($empleado->empleado_estado_civil == "SOLTERO/A") selected @endif>SOLTERO/A</option>
                                <option value="UNION DE HECHO" @if($empleado->empleado_estado_civil == "UNION DE HECHO") selected @endif>UNION DE HECHO</option>
                                <option value="CASADO/A" @if($empleado->empleado_estado_civil == "CASADO/A") selected @endif>CASADO/A</option>
                                <option value="DIVORCIADO/A" @if($empleado->empleado_estado_civil == "DIVORCIADO/A") selected @endif>DIVORCIADO/A</option>
                                <option value="VUIDO/A" @if($empleado->empleado_estado_civil == "VUIDO/A") selected @endif>VUIDO/A</option>                                    
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idDireccion" class="col-sm-3 col-form-label">Direccion</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="idDireccion" name="idDireccion" value="{{$empleado->empleado_direccion}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idTelefono" class="col-sm-3 col-form-label">Teléfono</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="idTelefono" name="idTelefono" value="{{$empleado->empleado_telefono}}" required>
                        </div>
                        <label for="idCelular" class="col-sm-3 col-form-label">Celular</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="idCelular" name="idCelular" value="{{$empleado->empleado_celular}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idCorreo" class="col-sm-3 col-form-label">Correo</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="idCorreo" name="idCorreo" value="{{$empleado->empleado_correo}}" required>
                        </div>
                    </div>
                    @if($parametrizacionContable->parametrizacion_cuenta_general == '0')
                    <div class="form-group row">
                        <label for="idCuentaAnti" class="col-sm-3 col-form-label">Cuenta Anticipo</label>
                        <div class="col-sm-9">
                            <select id="idCuentaAnti" name="idCuentaAnti" class="form-control show-tick select2" data-live-search="true">
                                <option value="" label>--Seleccione una opcion--</option>
                                @foreach($cuentas as $cuenta)
                                @if($cuenta->cuenta_id == $empleado->empleado_cuenta_anticipo)
                                <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @else
                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idCuentaPres" class="col-sm-3 col-form-label">Cuenta Prestamo</label>
                        <div class="col-sm-9">
                            <select id="idCuentaPres" name="idCuentaPres" class="form-control show-tick select2" data-live-search="true">
                                <option value="" label>--Seleccione una opcion --</option>
                                @foreach($cuentas as $cuenta)
                                @if($cuenta->cuenta_id == $empleado->empleado_cuenta_prestamo)
                                <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @else
                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="card text-center">
                        <div class="card-header">
                            <h6>Datos Laborales</h6>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idFechaIng" class="col-sm-3 col-form-label">Fecha de Ingreso</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="idFechaIng" name="idFechaIng" value="{{$empleado->empleado_fecha_ingreso}}" required>
                        </div> 
                        <label for="idCargo" class="col-sm-3 col-form-label">Cargo</label>
                        <div class="col-sm-3">
                            <select id="idCargo" name="idCargo" class="form-control show-tick " data-live-search="true" required>
                                <option value="" label>--Seleccione una opcion--</option>
                                @foreach($cargos as $cargo)
                                @if($cargo->empleado_cargo_id == $empleado->cargo_id)
                                <option value="{{$cargo->empleado_cargo_id}}" selected>{{$cargo->empleado_cargo_nombre}}</option>
                                @else
                                <option value="{{$cargo->empleado_cargo_id}}">{{$cargo->empleado_cargo_nombre}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>                       
                    </div>
                    <div class="form-group row">
                        <label for="idDepartamento" class="col-sm-3 col-form-label">Departamento</label>
                        <div class="col-sm-3">
                            <select id="idDepartamento" name="idDepartamento" class="form-control show-tick " data-live-search="true" required>
                                <option value="" label>--Seleccione una opcion--</option>
                                @foreach($departamentos as $departamento)
                                @if($departamento->departamento_id == $empleado->departamento_id)
                                <option value="{{$departamento->departamento_id}}" selected>{{$departamento->departamento_nombre.' - '.$departamento->sucursal->sucursal_nombre}}</option>
                                @else
                                <option value="{{$departamento->departamento_id}}">{{$departamento->departamento_nombre.' - '.$departamento->sucursal->sucursal_nombre}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <label for="idQuincena" class="col-sm-3 col-form-label">Quincena</label>
                        <div class="col-sm-3 input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">$</div>
                            </div>
                            <input type="number" class="form-control derecha-texto" id="idQuincena" name="idQuincena" value="{{$empleado->empleado_quincena}}" step="any" >
                        </div>                        
                    </div>
                    <div class="form-group row">
                        <label for="idSueldo" class="col-sm-3 col-form-label">Sueldo</label>
                        <div class="col-sm-3 input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">$</div>
                            </div>
                            <input type="number" class="form-control derecha-texto" id="idSueldo" name="idSueldo" value="{{$empleado->empleado_sueldo}}" step="any" required>
                        </div>
                        <label for="idCosecha" class="col-sm-3 col-form-label">Cosecha</label>
                        <div class="col-sm-3 ">
                            <input type="text" class="form-control derecha-texto" id="idCosecha" name="idCosecha" value="{{ $empleado->empleado_cosecha}}" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idTipo" class="col-sm-3 col-form-label">Tipo Empleado</label>
                        <div class="col-sm-3">
                            <select id="idTipo" name="idTipo" class="form-control show-tick " data-live-search="true" required>
                                <option value="" label>--Seleccione una opcion--</option>
                                @foreach($tipo as $tipo)
                                @if($tipo->tipo_id == $empleado->tipo_id)
                                <option value="{{$tipo->tipo_id}}" selected>{{$tipo->tipo_descripcion}} - @if(isset($tipo->sucursal_id)) {{$tipo->sucursal->sucursal_nombre}} @endif</option>
                                @else
                                <option value="{{$tipo->tipo_id}}">{{$tipo->tipo_descripcion}} - @if(isset($tipo->sucursal_id)) {{$tipo->sucursal->sucursal_nombre}} @endif</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                    <label for="idJornada" class="col-sm-3 col-form-label">Jornada de Trabajo</label>
                        <div class="col-sm-3">
                            <select id="idJornada" name="idJornada" class="form-control select2" data-live-search="true" required>
                                <option value="" label>--Seleccione --</option>
                                <option value="22-8" @if($empleado->empleado_jornada == "22-8") selected @endif>22-8</option>
                                <option value="20-10" @if($empleado->empleado_jornada == "20-10") selected @endif>20-10</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!--Datos Adicionales-->

                <div class="tab-pane fade" id="adicional" role="tabpanel" aria-labelledby="adicional-tab">
                    <br>
                    <div class="card text-center">
                        <div class="card-header">
                            <h6>Seguro Social</h6>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idAfiliado" class="col-sm-4 col-form-label">Afiliado</label>
                        <div class="col-sm-2 col-form-label">
                            <div class="form-check">
                                @if($empleado->empleado_afiliado=="1")
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idAfiliado" name="idAfiliado" checked>
                                @else
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idAfiliado" name="idAfiliado">
                                @endif
                            </div>
                        </div>
                        <label for="idFechaAfi" class="col-sm-3 col-form-label">Fecha de afiliacion</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="idFechaAfi" name="idFechaAfi" value="{{$empleado->empleado_fecha_afiliacion}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idHorasEx" class="col-sm-4 col-form-label">Horas extras</label>
                        <div class="col-sm-2 col-form-label">
                            <div class="form-check">
                                @if($empleado->empleado_horas_extra=="1")
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idHorasEx" name="idHorasEx" checked>
                                @else
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idHorasEx" name="idHorasEx">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idFondosRes" class="col-sm-4 col-form-label">Acumula Fondos de reserva</label>
                        <div class="col-sm-2 col-form-label">
                            <div class="form-check">
                                @if($empleado->empleado_fondos_reserva=="1")
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idFondosRes" name="idFondosRes" checked>
                                @else
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idFondosRes" name="idFondosRes">
                                @endif
                            </div>
                        </div>
                        <label for="idFechaIni" class="col-sm-3 col-form-label">Fecha de inicio FR</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="idFechaIni" name="idFechaIni" value="{{$empleado->empleado_fecha_inicioFR}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idIessA" class="col-sm-4 col-form-label">IESS Asumido</label>
                        <div class="col-sm-2 col-form-label">
                            <div class="form-check">
                                @if($empleado->empleado_iess_asumido=="1")
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idIessA" name="idIessA" checked>
                                @else
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idIessA" name="idIessA">
                                @endif
                            </div>
                        </div>
                        <label for="idFechaSal" class="col-sm-3 col-form-label">Fecha de Salida</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="idFechaSal" name="idFechaSal" value="{{$empleado->empleado_fecha_salida}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idDecimoTer" class="col-sm-4 col-form-label">Mensualizar Decimo Tercero</label>
                        <div class="col-sm-2 col-form-label">
                            <div class="form-check">
                                @if($empleado->empleado_decimo_tercero=="1")
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idDecimoTer" name="idDecimoTer" checked>
                                @else
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idDecimoTer" name="idDecimoTer">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idDecimoCua" class="col-sm-4 col-form-label">Mensualizar Decimo Cuarto</label>
                        <div class="col-sm-2 col-form-label">
                            <div class="form-check">
                                @if($empleado->empleado_decimo_cuarto=="1")
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idDecimoCua" name="idDecimoCua" checked>
                                @else
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idDecimoCua" name="idDecimoCua">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idImpuestoR" class="col-sm-4 col-form-label">Descontar Impuesto a la Renta</label>
                        <div class="col-sm-2 col-form-label">
                            <div class="form-check">
                                @if($empleado->empleado_impuesto_renta=="1")
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idImpuestoR" name="idImpuestoR" checked>
                                @else
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idImpuestoR" name="idImpuestoR">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idGerente" class="col-sm-4 col-form-label">Gerente Iess</label>
                        <div class="col-sm-2 col-form-label">
                            <div class="form-check">
                                @if($empleado->empleado_iess_gerente=="1")
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idGerente" name="idGerente" checked>
                                @else
                                <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idGerente" name="idGerente">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card text-center">
                        <div class="card-header">
                            <h6>Cuenta Bancaria</h6>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idCuantaTipo" class="col-sm-3 col-form-label">Tipo de Cuenta</label>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col-form-label" style="margin-bottom : 0px;">
                            <div class="demo-checkbox">
                                @if($empleado->empleado_cuenta_tipo=="AHORROS")
                                <input type="radio" value="AHORROS" id="idCuantaTipo" class="with-gap radio-col-deep-orange" name="idCuantaTipo" required checked />
                                <label class="form-check-label" for="check1">Ahorros</label>
                                @else
                                <input type="radio" value="AHORROS" id="idCuantaTipo" class="with-gap radio-col-deep-orange" name="idCuantaTipo" required />
                                <label class="form-check-label" for="check1">Ahorros</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col-form-label" style="margin-bottom : 0px;">
                            <div class="demo-checkbox">
                                @if($empleado->empleado_cuenta_tipo=="CORRIENTE")
                                <input type="radio" value="CORRIENTE" id="idCuantaTipo" class="with-gap radio-col-deep-orange" name="idCuantaTipo" checked required />
                                <label class="form-check-label" for="check1">Corriente</label>
                                @else
                                <input type="radio" value="CORRIENTE" id="idCuantaTipo" class="with-gap radio-col-deep-orange" name="idCuantaTipo" required />
                                <label class="form-check-label" for="check1">Corriente</label>
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idBanco" class="col-sm-3 col-form-label">Banco</label>
                        <div class="col-sm-9">
                            <select id="idBanco" name="idBanco" class="form-control show-tick " data-live-search="true" required>
                                <option value="" label>--Seleccione una opcion--</option>
                                @foreach($banco as $banco)
                                @if($banco->banco_lista_id == $empleado->banco_lista_id)
                                <option value="{{$banco->banco_lista_id}}" selected>{{$banco->banco_lista_nombre}}</option>
                                @else
                                <option value="{{$banco->banco_lista_id}}">{{$banco->banco_lista_nombre}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idCuenta" class="col-sm-3 col-form-label">Numero de Cuenta</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="idCuenta" name="idCuenta" value="{{$empleado->empleado_cuenta_numero}}" required>
                        </div>
                    </div>
                    <div class="card text-center">
                        <div class="card-header">
                            <h6>Datos Familiares</h6>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idContactoNombre" class="col-sm-3 col-form-label">Nombre de Contacto</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="idContactoNombre" name="idContactoNombre" value="{{$empleado->empleado_contacto_nombre}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idContactoTelefono" class="col-sm-3 col-form-label">Teléfono de Contacto</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="idContactoTelefono" name="idContactoTelefono" value="{{$empleado->empleado_contacto_telefono}}" required>
                        </div>
                        <label for="idContactoCelular" class="col-sm-3 col-form-label">Celular de Contacto</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="idContactoCelular" name="idContactoCelular" value="{{$empleado->empleado_contacto_celular}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idContactoDireccion" class="col-sm-3 col-form-label">Direccion de Contacto</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="idContactoDireccion" name="idContactoDireccion" value="{{$empleado->empleado_contacto_direccion}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idCargaF" class="col-sm-3 col-form-label">Carga Familiar</label>
                        <div class="col-sm-3">
                            <input type="number" class="form-control" id="idCargaF" name="idCargaF" value="{{$empleado->empleado_carga_familiar}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idObservacion" class="col-sm-3 col-form-label">Observacion</label>
                        <div class="col-sm-9">
                            <textarea type="text" class="form-control" id="idObservacion" name="idObservacion" required>{{$empleado->empleado_observacion}} </textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idEstado" class="col-sm-3 col-form-label">Estado</label>
                        <div class="col-sm-9">
                            <div class="col-form-label custom-control custom-switch custom-switch-off-danger custom-switch-on-success"> 
                                    <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" @if($empleado->empleado_estado=="1") checked @endif>
                                <label class="custom-control-label" for="idEstado"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection