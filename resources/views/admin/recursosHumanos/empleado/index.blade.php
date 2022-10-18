@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Empleados</h3>
        <div class="float-right">
        @if(isset($parametrizacionContable->parametrizacion_cuenta_general))<button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
            <a class="btn btn-info btn-sm" href="{{ asset('admin/archivos/FORMATO_EMPLEADOS.xlsx') }}" download="FORMATO EMPLEADOS"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
            <a class="btn btn-success btn-sm" href="{{ url("excelEmpleado") }}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>@endif
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
    <form class="form-horizontal" method="POST" action="{{ url("listaEmpleadoSucursal") }}">
            @csrf
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select" id="sucursal_id" name="sucursal_id" require>
                        @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($sucursalC)) @if($sucursalC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select> 
                </div>
                <label for="sucursal_id" class="col-sm-1 col-form-label"><center>Estado:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select" id="Estado_id" name="Estado_id" >
                        <option value="TODOS" label>--TODOS--</option>   
                        @foreach($estados as $estado)
                            <option value="{{$estado->empleado_estado}}" @if(isset($Estado_id)) @if($Estado_id == $estado->empleado_estado) selected @endif @endif>@if($estado->empleado_estado=="1") Activo @else Inactivo @endif</option>
                        @endforeach
                    </select> 
                </div>
                <div class="col-sm-1 centrar-texto">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Cédula </th>
                    <th>Nombre </th>
                    <th>Telefono </th>
                    <th>Celular </th>
                    <th>Direccion </th>
                    <th>Sexo </th>
                    <th>Estatura </th>
                    <th>Grupo Sanguineo </th>
                    <th>Lugar Nacimiento </th>
                    <th>Fecha Nacimiento </th>
                    <th>Edad </th>
                    <th>Nacionalidad </th>
                    <th>Estado Civil </th>
                    <th>Correo </th>
                    <th>Jornada </th>
                    <th>Cosecha</th>
                    <th>Carga Familiar </th>
                    <th>Contacto Nombre </th>
                    <th>Contacto Telefono </th>
                    <th>Contacto Celular </th>
                    <th>Contacto Direccion </th>
                    <th>Observacion </th>
                    <th>Sueldo </th>
                    <th>Estado </th>
                    <th>Fecha Ingreso </th>
                    <th>Fecha Salida </th>
                    <th>Horas Extra </th>
                    <th>Afiliado </th>
                    <th>Iess Asumido </th>
                    <th>Acumula Fondos Reserva </th>
                    <th>Impuesto Renta </th>
                    <th>Decimo Tercero </th>
                    <th>Decimo Cuarto </th>
                    <th>Fecha Afiliacion </th>
                    <th>Fecha InicioFR </th>
                    <th>Cuenta Tipo </th>
                    <th>Cuenta Numero </th>
                    <th>Cargo </th>
                    <th>Departamento </th>
                    <th>Tipo </th>
                    <th>Banco </th>
                </tr>
            </thead>
            <tbody>
                @if(isset($empleados))
                    @foreach($empleados as $empleado)
                    <tr class="text-center">
                        <td>
                            <a href="{{ url("empleado/{$empleado->empleado_id}/edit") }}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            <a href="{{ url("empleado/{$empleado->empleado_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a href="{{ url("empleado/{$empleado->empleado_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            <a href="{{ url("fichaEmpeladoPdf/imprimir/{$empleado->empleado_id}") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a>   
                        </td>
                        <td>{{ $empleado->empleado_cedula}}</td>
                        <td>{{ $empleado->empleado_nombre}}</td>
                        <td>{{ $empleado->empleado_telefono}}</td>
                        <td>{{ $empleado->empleado_celular}}</td>
                        <td>{{ $empleado->empleado_direccion}}</td>
                        <td>{{ $empleado->empleado_sexo}}</td>
                        <td>{{ $empleado->empleado_estatura}}</td>
                        <td>{{ $empleado->empleado_grupo_sanguineo}}</td>
                        <td>{{ $empleado->empleado_lugar_nacimiento}}</td>
                        <td>{{ $empleado->empleado_fecha_nacimiento}}</td>
                        <td>{{ $empleado->empleado_edad}}</td>
                        <td>{{ $empleado->empleado_nacionalidad}}</td>
                        <td>{{ $empleado->empleado_estado_civil}}</td>
                        <td>{{ $empleado->empleado_correo}}</td>
                        <td>{{ $empleado->empleado_jornada}}</td>
                        <td>{{ number_format($empleado->empleado_cosecha,2)}}</td>
                        <td>{{ $empleado->empleado_carga_familiar}}</td>
                        <td>{{ $empleado->empleado_contacto_nombre}}</td>
                        <td>{{ $empleado->empleado_contacto_telefono}}</td>
                        <td>{{ $empleado->empleado_contacto_celular}}</td>
                        <td>{{ $empleado->empleado_contacto_direccion}}</td>
                        <td>{{ $empleado->empleado_observacion}}</td>
                        <td>{{ number_format($empleado->empleado_sueldo,2) }}</td>
                      
                        <td> @if($empleado->empleado_estado=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>

                        <td>{{ $empleado->empleado_fecha_ingreso}}</td>
                        <td>
                            @if($empleado->empleado_fecha_salida != null)
                            {{ $empleado->empleado_fecha_salida}}
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>
                        <td>
                            @if($empleado->empleado_horas_extra=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>
                        <td>
                            @if($empleado->empleado_afiliado=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>
                        <td>
                            @if($empleado->empleado_iess_asumido=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>
                        <td>
                            @if($empleado->empleado_fondos_reserva=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>
                        <td>
                            @if($empleado->empleado_impuesto_renta=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>
                        <td>
                            @if($empleado->empleado_decimo_tercero=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>
                        <td>
                            @if($empleado->empleado_decimo_cuarto=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>
                        <td>
                            @if($empleado->empleado_fecha_afiliacion != null)
                            {{ $empleado->empleado_fecha_afiliacion}}
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>
                        <td>
                            @if($empleado->empleado_fecha_inicioFR != null)
                            {{ $empleado->empleado_fecha_inicioFR}}
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>
                        <td>{{ $empleado->empleado_cuenta_tipo}}</td>
                        <td>{{ $empleado->empleado_cuenta_numero}}</td>
                        <td>{{ $empleado->empleado_cargo_nombre}}</td>
                        <td>@if(isset($empleado->departamento->departamento_nombre)) {{ $empleado->departamento->departamento_nombre.' - '.$empleado->departamento->sucursal->sucursal_nombre}} @endif</td>
                        <td>@if(isset($empleado->tipo->tipo_descripcion)){{$empleado->tipo->tipo_descripcion}} - @if(isset($empleado->tipo->sucursal_id)) {{$empleado->tipo->sucursal->sucursal_nombre}} @endif @endif</td>
                        <td>{{ $empleado->banco->banco_lista_nombre}}</td>
                    </tr>
                    @endforeach
                @endif
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
                <h4 class="modal-title">Nuevo Empleado</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("empleado") }} ">
                @csrf
                <div class="modal-body">
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
                                    <label for="idCedula" class="col-sm-3 col-form-label">Cedula*</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="idCedula" name="idCedula" placeholder="#Cedula" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idNombre" class="col-sm-3 col-form-label">Nombre*</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idSexo" class="col-sm-3 col-form-label">Sexo*</label>
                                    <div class="col-sm-4">
                                        <select id="idSexo" name="idSexo" class="form-control show-tick " data-live-search="true" required>
                                            <option value="" label>--Seleccione--</option>
                                            <option value="Masculino">Masculino</option>
                                            <option value="Femenino">Femenino</option>
                                        </select>
                                    </div>
                                    <label for="idEstatura" class="col-sm-2 col-form-label">Estatura*</label>
                                    <div class="col-sm-3 input-group mb-2">
                                        <input type="text" class="form-control" id="idEstatura" name="idEstatura" placeholder="Estatura" >
                                        <div class="input-group-append">
                                            <div class="input-group-text">cm</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idGrupoS" class="col-sm-3 col-form-label">Grupo Sanguineo*</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="idGrupoS" name="idGrupoS" placeholder="Grupo Sanguineo" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idLugarNac" class="col-sm-3 col-form-label">Lugar de Nacimiento*</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="idLugarNac" name="idLugarNac" placeholder="Lugar de Nacimiento" required>
                                    </div>
                                    <label for="idFechaNac" class="col-sm-3 col-form-label">Fecha de Nacimiento*</label>
                                    <div class="col-sm-3">
                                        <input type="date" class="form-control" id="idFechaNac" name="idFechaNac" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idEdad" class="col-sm-3 col-form-label">Edad*</label>
                                    <div class="col-sm-3">
                                        <input type="number" class="form-control" id="idEdad" name="idEdad" placeholder="Edad" required>
                                    </div>
                                    <label for="idNacionalidad" class="col-sm-3 col-form-label">Nacionalidad*</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="idNacionalidad" name="idNacionalidad" placeholder="Nacionalidad" >
                                    </div>

                                </div>
                                <div class="form-group row">
                                    <label for="idEstadoCivil" class="col-sm-3 col-form-label">Estado Civil*</label>
                                    <div class="col-sm-9">
                                        <select id="idEstadoCivil" name="idEstadoCivil" class="form-control show-tick " data-live-search="true" >
                                            <option value="" label>--Seleccione una opcion--</option>
                                            <option value="SOLTERO/A">SOLTERO/A</option>
                                            <option value="UNION DE HECHO">UNION DE HECHO</option>
                                            <option value="CASADO/A">CASADO/A</option>
                                            <option value="DIVORCIADO/A">DIVORCIADO/A</option>
                                            <option value="VUIDO/A">VUIDO/A</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idDireccion" class="col-sm-3 col-form-label">Direccion*</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="idDireccion" name="idDireccion" placeholder="Direccion" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idTelefono" class="col-sm-3 col-form-label">Teléfono*</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="idTelefono" name="idTelefono" placeholder="000000000">
                                    </div>
                                    <label for="idCelular" class="col-sm-1 col-form-label">Celular</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="idCelular" name="idCelular" placeholder="0000000000">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idCorreo" class="col-sm-3 col-form-label">Correo*</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" id="idCorreo" name="idCorreo" placeholder="Correo Electronico" required>
                                    </div>
                                </div>
                                @if(isset($parametrizacionContable->parametrizacion_cuenta_general))
                                    @if($parametrizacionContable->parametrizacion_cuenta_general == '0')
                                    <div class="form-group row">
                                        <label for="idCuentaAnti" class="col-sm-3 col-form-label">Cuenta Anticipo</label>
                                        <div class="col-sm-9">
                                            <select id="idCuentaAnti" name="idCuentaAnti" class="form-control select2" data-live-search="true" >
                                                <option value="" label>--Seleccione una opcion--</option>
                                                @foreach($cuentas as $cuenta)
                                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="idCuentaPres" class="col-sm-3 col-form-label">Cuenta Prestamo</label>
                                        <div class="col-sm-9">
                                            <select id="idCuentaPres" name="idCuentaPres" class="form-control select2" data-live-search="true" >
                                                <option value="" label>--Seleccione una opcion --</option>
                                                @foreach($cuentas as $cuenta)
                                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                @endif
                                <div class="card text-center">
                                    <div class="card-header">
                                        <h6>Datos Laborales</h6>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idFechaIng" class="col-sm-3 col-form-label">Fecha de Ingreso*</label>
                                    <div class="col-sm-3">
                                        <input type="date" class="form-control" id="idFechaIng" name="idFechaIng" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <label for="idFechaSal" class="col-sm-3 col-form-label">Fecha de Salida</label>
                                    <div class="col-sm-3">
                                        <input type="date" class="form-control" id="idFechaSal" name="idFechaSal">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idDepartamento" class="col-sm-2 col-form-label">Departamento</label>
                                    <div class="col-sm-4">
                                        <select id="idDepartamento" name="idDepartamento" class="form-control select2" data-live-search="true">
                                            <option value="" label>--Seleccione --</option>
                                            @foreach($departamentos as $departamento)
                                            <option value="{{$departamento->departamento_id}}">{{$departamento->departamento_nombre.' - '.$departamento->sucursal->sucursal_nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="idCargo" class="col-sm-2 col-form-label">Cargo*</label>
                                    <div class="col-sm-4">
                                        <select id="idCargo" name="idCargo" class="form-control select2" data-live-search="true" required>
                                            <option value="" label>--Seleccione --</option>
                                            @foreach($cargos as $cargo)
                                            <option value="{{$cargo->empleado_cargo_id}}">{{$cargo->empleado_cargo_nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idSueldo" class="col-sm-2 col-form-label">Sueldo*</label>
                                    <div class="col-sm-4 ">
                                        <input type="number" class="form-control derecha-texto" id="idSueldo" name="idSueldo" value="0.00" step="any" placeholder="0.00" required>
                                    </div>
                                    <label for="idSueldo" class="col-sm-2 col-form-label">Quincena</label>
                                    <div class="col-sm-4 ">
                                        <input type="number" class="form-control derecha-texto" id="idQuincena" name="idQuincena" value="0.00" step="any" placeholder="0.00" >
                                    </div>
                                   
                                </div>
                                
                                <div class="form-group row">
                                    <label for="idTipo" class="col-sm-2 col-form-label">Tipo Empleado*</label>
                                    <div class="col-sm-4">
                                        <select id="idTipo" name="idTipo" class="form-control select2" data-live-search="true" required>
                                            <option value="" label>--Seleccione --</option>
                                            @foreach($tipos as $tipo)
                                            <option value="{{$tipo->tipo_id}}">{{$tipo->tipo_descripcion}} - @if(isset($tipo->sucursal_id)) {{$tipo->sucursal->sucursal_nombre}} @endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="idCosecha" class="col-sm-2 col-form-label">Cosecha</label>
                                    <div class="col-sm-4 ">
                                        <input type="text" class="form-control derecha-texto" id="idCosecha" name="idCosecha" placeholder="0.00" value="0" >
                                    </div>
                                   
                                </div>
                                <div class="form-group row">
                                    <label for="idJornada" class="col-sm-3 col-form-label">Jornada de Trabajo</label>
                                    <div class="col-sm-3">
                                        <select id="idJornada" name="idJornada" class="form-control select2" data-live-search="true" >
                                            <option value="" label>--Seleccione --</option>
                                            <option value="22-8">22-8</option>
                                            <option value="20-10">20-10</option>
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
                                            <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idAfiliado" name="idAfiliado">
                                        </div>
                                    </div>
                                    <label for="idFechaAfi" class="col-sm-3 col-form-label">Fecha de afiliacion</label>
                                    <div class="col-sm-3">
                                        <input type="date" class="form-control" id="idFechaAfi" name="idFechaAfi" placeholder="Fecha de afiliacion">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idHorasEx" class="col-sm-4 col-form-label">Horas extras</label>
                                    <div class="col-sm-2 col-form-label">
                                        <div class="form-check">
                                            <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idHorasEx" name="idHorasEx">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idFondosRes" class="col-sm-4 col-form-label">Acumula Fondos de reserva</label>
                                    <div class="col-sm-2 col-form-label">
                                        <div class="form-check">
                                            <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idFondosRes" name="idFondosRes">
                                        </div>
                                    </div>
                                    <label for="idFechaIni" class="col-sm-3 col-form-label">Fecha de inicio FR</label>
                                    <div class="col-sm-3">
                                        <input type="date" class="form-control" id="idFechaIni" name="idFechaIni" placeholder="Fecha de inicio FR">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idIessA" class="col-sm-4 col-form-label">IESS Asumido</label>
                                    <div class="col-sm-2 col-form-label">
                                        <div class="form-check">
                                            <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idIessA" name="idIessA">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idDecimoTer" class="col-sm-4 col-form-label">Mensualizar Decimo Tercero</label>
                                    <div class="col-sm-2 col-form-label">
                                        <div class="form-check">
                                            <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idDecimoTer" name="idDecimoTer">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idDecimoCua" class="col-sm-4 col-form-label">Mensualizar Decimo Cuarto</label>
                                    <div class="col-sm-2 col-form-label">
                                        <div class="form-check">
                                            <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idDecimoCua" name="idDecimoCua">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idImpuestoR" class="col-sm-4 col-form-label">Descontar Impuesto a la Renta</label>
                                    <div class="col-sm-2 col-form-label">
                                        <div class="form-check">
                                            <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idImpuestoR" name="idImpuestoR">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="idImpuestoR" class="col-sm-4 col-form-label">Gerente Iess</label>
                                    <div class="col-sm-2 col-form-label">
                                        <div class="form-check">
                                            <input style="width:20px; height:20px;" class="form-check-input" type="checkbox" id="idGerente" name="idGerente">
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
                                            <input type="radio" value="AHORROS" id="idCuantaTipo" class="with-gap radio-col-deep-orange" name="idCuantaTipo" required />
                                            <label class="form-check-label" for="check1">Ahorros</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col-form-label" style="margin-bottom : 0px;">
                                        <div class="demo-checkbox">
                                            <input type="radio" value="CORRIENTE" id="idCuantaTipo" class="with-gap radio-col-deep-orange" name="idCuantaTipo" checked required />
                                            <label class="form-check-label" for="check1">Corriente</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idBanco" class="col-sm-3 col-form-label">Banco</label>
                                    <div class="col-sm-9">
                                        <select id="idBanco" name="idBanco" class="form-control select2" data-live-search="true">
                                            <option value="" label>--Seleccione una opcion--</option>
                                            @foreach($banco as $banco)
                                            <option value="{{$banco->banco_lista_id}}">{{$banco->banco_lista_nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idCuenta" class="col-sm-3 col-form-label">Numero de Cuenta</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="idCuenta" name="idCuenta" placeholder="Cuenta">
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
                                        <input type="text" class="form-control" id="idContactoNombre" name="idContactoNombre" placeholder="Nombre de Contacto" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idContactoTelefono" class="col-sm-3 col-form-label">Teléfono de Contacto</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="idContactoTelefono" name="idContactoTelefono" placeholder="Número de Teléfono" >
                                    </div>
                                    <label for="idContactoCelular" class="col-sm-3 col-form-label">Celular de Contacto</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="idContactoCelular" name="idContactoCelular" placeholder="Número Celular" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idContactoDireccion" class="col-sm-3 col-form-label">Direccion de Contacto</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="idContactoDireccion" name="idContactoDireccion" placeholder="Direccion" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idCargaF" class="col-sm-3 col-form-label">Carga Familiar</label>
                                    <div class="col-sm-3">
                                        <input type="number" class="form-control" id="idCargaF" name="idCargaF" placeholder="Carga Familiar" value=0>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="idObservacion" class="col-sm-3 col-form-label">Observacion</label>
                                    <div class="col-sm-9">
                                        <textarea type="text" class="form-control" id="idObservacion" name="idObservacion" > </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-contentt -->
                    </div>
                    <!-- /.card-body -->
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="submit" onclick="camposVacios()" class="btn btn-success">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection

<script>
    function camposVacios() {
        var cedula = document.getElementById("idCedula").value;
        var nombre = document.getElementById("idNombre").value;
        var telefono = document.getElementById("idTelefono").value;
        var celular = document.getElementById("idCelular").value;
        var direccion = document.getElementById("idDireccion").value;        
        var sexo = document.getElementById("idSexo").value;
        var estatura = document.getElementById("idEstatura").value;
        var grupoS = document.getElementById("idGrupoS").value;
        var lugarNac = document.getElementById("idLugarNac").value;
        var fechaNac = document.getElementById("idFechaNac").value;
        var edad = document.getElementById("idEdad").value;
        var nacionalidad = document.getElementById("idNacionalidad").value;
        var estadoCivil = document.getElementById("idEstadoCivil").value;
        var correo = document.getElementById("idCorreo").value;
        var cargaF = document.getElementById("idCargaF").value;
        var contactoNombre = document.getElementById("idContactoNombre").value;
        var contactoTelefono = document.getElementById("idContactoTelefono").value;
        var contactoCelular = document.getElementById("idContactoCelular").value;
        var contactoDireccion = document.getElementById("idContactoDireccion").value;
        var observacion = document.getElementById("idObservacion").value;
        var sueldo = document.getElementById("idSueldo").value;
        var fechaIng = document.getElementById("idFechaIng").value;
        var cuantaTipo = document.getElementById("idCuantaTipo").value;
        var cuenta = document.getElementById("idCuenta").value;
        var cargo = document.getElementById("idCargo").value;
        var departamento = document.getElementById("idDepartamento").value;
        var cuentaAnti = document.getElementById("idCuentaAnti").value;
        var cuentaPres = document.getElementById("idCuentaPres").value;
        var tipo = document.getElementById("idTipo").value;
        var banco = document.getElementById("idBanco").value;

        if (cedula == "" || nombre == "" || telefono == "" || celular == "" || direccion == "" ||
            sexo == "" || estatura == "" || grupoS == "" || lugarNac == "" || fechaNac == "" ||
            edad == "" || nacionalidad == "" || estadoCivil == "" || correo == "" || cargaF == "" ||
            contactoNombre == "" || contactoTelefono == "" || contactoCelular == "" || contactoDireccion == "" || observacion == "" ||
            sueldo == "" || fechaIng == "" || cuantaTipo == "" || cuenta == "" || cargo == "" ||
            cuentaAnti == "" || cuentaPres == "" || tipo == "" || banco == "") {
            toastr.warning('Debe rellenar todos los campos','Alerta!', {
                "progressBar": true,
                "preventDuplicates": true,
                "timeOut": "4000",
                "positionClass": "toast-bottom-right"
            });
        }
    }
</script>
