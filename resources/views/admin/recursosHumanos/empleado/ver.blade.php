@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Empleado</h3>
        <button onclick='window.location = "{{ url("empleado") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
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
                        <label class="form-control">{{$empleado->empleado_cedula}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idNombre" class="col-sm-3 col-form-label">Nombre</label>
                    <div class="col-sm-9">
                        <label class="form-control">{{$empleado->empleado_nombre}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idSexo" class="col-sm-3 col-form-label">Sexo</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_sexo}}</label>
                    </div>
                    <label for="idEstatura" class="col-sm-3 col-form-label">Estatura</label>
                    <div class="col-sm-3 input-group mb-2">
                        <label class="form-control">{{$empleado->empleado_estatura}}</label>
                        <div class="input-group-append">
                            <div class="input-group-text">cm</div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idGrupoS" class="col-sm-3 col-form-label">Grupo Sanguineo</label>
                    <div class="col-sm-9">
                        <label class="form-control">{{$empleado->empleado_grupo_sanguineo}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idLugarNac" class="col-sm-3 col-form-label">Lugar de Nacimiento</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_lugar_nacimiento}}</label>
                    </div>
                    <label for="idFechaNac" class="col-sm-3 col-form-label">Fecha de Nacimiento</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_fecha_nacimiento}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idEdad" class="col-sm-3 col-form-label">Edad</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_edad}}</label>
                    </div>
                    <label for="idNacionalidad" class="col-sm-3 col-form-label">Nacionalidad</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_nacionalidad}}</label>
                    </div>

                </div>
                <div class="form-group row">
                    <label for="idEstadoCivil" class="col-sm-3 col-form-label">Estado Civil</label>
                    <div class="col-sm-9">
                        <label class="form-control">{{$empleado->empleado_estado_civil}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idDireccion" class="col-sm-3 col-form-label">Direccion</label>
                    <div class="col-sm-9">
                        <label class="form-control">{{$empleado->empleado_direccion}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idTelefono" class="col-sm-3 col-form-label">Teléfono</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_telefono}}</label>
                    </div>
                    <label for="idCelular" class="col-sm-3 col-form-label">Celular</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_celular}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idCorreo" class="col-sm-3 col-form-label">Correo</label>
                    <div class="col-sm-9">
                        <label class="form-control">{{$empleado->empleado_correo}}</label>
                    </div>
                </div>
                @if($parametrizacionContable->parametrizacion_cuenta_general == '0')
                <div class="form-group row">
                    <label for="idCuentaAnti" class="col-sm-3 col-form-label">Cuenta Anticipo</label>
                    <div class="col-sm-9">
                        <select id="idCuentaAnti" name="idCuentaAnti" class="form-control show-tick " data-live-search="true" disabled>
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
                        <select id="idCuentaPres" name="idCuentaPres" class="form-control show-tick " data-live-search="true" disabled>
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
                        <label class="form-control">{{$empleado->empleado_fecha_ingreso}}</label>
                    </div>
                    <label for="idCargo" class="col-sm-3 col-form-label">Cargo</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_cargo_nombre}}</label>
                    </div>                    
                </div>
                <div class="form-group row">
                    <label for="idDepartamento" class="col-sm-3 col-form-label">Departamento</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->departamento->departamento_nombre.' - '.$empleado->departamento->sucursal->sucursal_nombre}}</label>
                    </div>
                    <label for="idTipo" class="col-sm-3 col-form-label">Tipo Empleado</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->tipo->tipo_categoria}} - @if(isset($empleado->tipo->sucursal_id)) {{$empleado->tipo->sucursal->sucursal_nombre}} @endif</label>
                    </div>                    
                </div>
                <div class="form-group row">
                    <label for="idSueldo" class="col-sm-3 col-form-label">Sueldo</label>
                    <div class="col-sm-3 input-group mb-2">
                        <label class="form-control">{{$empleado->empleado_sueldo}}</label>
                    </div> 
                    <label for="idSueldo" class="col-sm-3 col-form-label">Cosecha</label>
                    <div class="col-sm-3 input-group mb-2">
                        <label class="form-control">{{$empleado->empleado_cosecha}}</label>
                    </div>                                        
                </div>
                <div class="form-group row">
                    <label for="idTipo" class="col-sm-3 col-form-label">Jornada de Trabajo</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_jornada}}</label>
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
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idAfiliado" name="idAfiliado" checked disabled>
                            @else
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idAfiliado" name="idAfiliado" disabled>
                            @endif
                        </div>
                    </div>
                    <label for="idFechaAfi" class="col-sm-3 col-form-label">Fecha de afiliacion</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_fecha_afiliacion}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idHorasEx" class="col-sm-4 col-form-label">Horas extras</label>
                    <div class="col-sm-2 col-form-label">
                        <div class="form-check">
                            @if($empleado->empleado_horas_extra=="1")
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idHorasEx" name="idHorasEx" checked disabled>
                            @else
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idHorasEx" name="idHorasEx" disabled>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idFondosRes" class="col-sm-4 col-form-label">Fondos de reserva</label>
                    <div class="col-sm-2 col-form-label">
                        <div class="form-check">
                            @if($empleado->empleado_fondos_reserva=="1")
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idFondosRes" name="idFondosRes" checked disabled>
                            @else
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idFondosRes" name="idFondosRes" disabled>
                            @endif
                        </div>
                    </div>
                    <label for="idFechaIni" class="col-sm-3 col-form-label">Fecha de inicio FR</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_fecha_inicioFR}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idIessA" class="col-sm-4 col-form-label">IESS Asumido</label>
                    <div class="col-sm-2 col-form-label">
                        <div class="form-check">
                            @if($empleado->empleado_iess_asumido=="1")
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idIessA" name="idIessA" checked disabled>
                            @else
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idIessA" name="idIessA" disabled>
                            @endif
                        </div>
                    </div>
                    <label for="idFechaSal" class="col-sm-3 col-form-label">Fecha de Salida</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_fecha_salida}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idDecimoTer" class="col-sm-4 col-form-label">Mensualizar Decimo Tercero</label>
                    <div class="col-sm-2 col-form-label">
                        <div class="form-check">
                            @if($empleado->empleado_decimo_tercero=="1")
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idDecimoTer" name="idDecimoTer" checked disabled>
                            @else
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idDecimoTer" name="idDecimoTer" disabled>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idDecimoCua" class="col-sm-4 col-form-label">Mensualizar Decimo Cuarto</label>
                    <div class="col-sm-2 col-form-label">
                        <div class="form-check">
                            @if($empleado->empleado_decimo_cuarto=="1")
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idDecimoCua" name="idDecimoCua" checked disabled>
                            @else
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idDecimoCua" name="idDecimoCua" disabled>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idImpuestoR" class="col-sm-4 col-form-label">Descontar Impuesto a la Renta</label>
                    <div class="col-sm-2 col-form-label">
                        <div class="form-check">
                            @if($empleado->empleado_impuesto_renta=="1")
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idImpuestoR" name="idImpuestoR" checked disabled>
                            @else
                            <input style="width:20px; height:20px; cursor: not-allowed;" class="form-check-input" type="checkbox" id="idImpuestoR" name="idImpuestoR" disabled>
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
                            <label class="form-control">{{$empleado->empleado_cuenta_tipo}}</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idBanco" class="col-sm-3 col-form-label">Banco</label>
                    <div class="col-sm-9">
                        <label class="form-control">{{$empleado->banco->banco_lista_nombre}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idCuenta" class="col-sm-3 col-form-label">Numero de Cuenta</label>
                    <div class="col-sm-9">
                        <label class="form-control">{{$empleado->empleado_cuenta_numero}}</label>
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
                        <label class="form-control">{{$empleado->empleado_contacto_nombre}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idContactoTelefono" class="col-sm-3 col-form-label">Teléfono de Contacto</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_contacto_telefono}}</label>
                    </div>
                    <label for="idContactoCelular" class="col-sm-3 col-form-label">Celular de Contacto</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_contacto_celular}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idContactoDireccion" class="col-sm-3 col-form-label">Direccion de Contacto</label>
                    <div class="col-sm-9">
                        <label class="form-control">{{$empleado->empleado_contacto_direccion}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idCargaF" class="col-sm-3 col-form-label">Carga Familiar</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$empleado->empleado_carga_familiar}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idObservacion" class="col-sm-3 col-form-label">Observacion</label>
                    <div class="col-sm-9">
                        <label class="form-control">{{$empleado->empleado_observacion}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Estado</label>
                    <div class="col-sm-9">
                        @if($empleado->empleado_estado=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                        @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- /.tab-contentt -->
    </div>
    <!-- /.card-body -->
    <!-- /.card-footer -->
</div>
<!-- /.card-body -->
</div>
<!-- /.card -->
@endsection