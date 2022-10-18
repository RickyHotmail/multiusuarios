@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Empresas</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Ruc</th>
                    <th>Nombre Comercial</th>
                    <th>Razon Social</th>
                    <th>Ciudad</th>
                    <th>Direccion</th>
                    <th>Teléfono</th>
                    <th>Celular</th>
                    <th>Representante</th>
                    <th>Fecha de Ingreso</th>
                    <th>Email</th>
                    <th>Contabilidad</th>
                    <th>Tipo</th>
                    <th>Cont. Especial</th>
                    <th>Sistema Contable</th>
                    <th>Facturación Electrónica</th>
                    <th>Sistema de Nómina</th>
                    <th>Sistema Médico</th>
                </tr>
            </thead>
            <tbody>
                @foreach($empresas as $empresa)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("empresa/{$empresa->empresa_id}/edit") }}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("empresa/{$empresa->empresa_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("empresa/{$empresa->empresa_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $empresa->empresa_ruc}}</td>
                    <td>{{ $empresa->empresa_nombreComercial}}</td>
                    <td>{{ $empresa->empresa_razonSocial}}</td>
                    <td>{{ $empresa->empresa_ciudad}}</td>
                    <td>{{ $empresa->empresa_direccion}}</td>
                    <td>{{ $empresa->empresa_telefono}}</td>
                    <td>{{ $empresa->empresa_celular}}</td>
                    <td>{{ $empresa->empresa_representante}}</td>
                    <td>{{ $empresa->empresa_fecha_ingreso}}</td>
                    <td>{{ $empresa->empresa_email}}</td>
                    <td>
                        @if($empresa->empresa_llevaContabilidad=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                        @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                        @endif
                    </td>
                    <td>{{ $empresa->empresa_tipo}}</td>
                    <td>{{ $empresa->empresa_contribuyenteEspecial}}</td>
                    <td>
                        @if($empresa->empresa_contabilidad=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                        @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                        @endif
                    </td>
                    <td>
                        @if($empresa->empresa_electronica=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                        @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                        @endif
                    </td>
                    <td>
                        @if($empresa->empresa_nomina=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                        @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                        @endif
                    </td>
                    <td>
                        @if($empresa->empresa_medico=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                        @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                        @endif
                    </td>
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
                <h4 class="modal-title">Nueva Empresa</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="/empresa">
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="Ruc" class="col-sm-3 col-form-label">Ruc</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="Ruc" name="Ruc" placeholder="Ej. 9999999999999" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Nombre Comercial</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre Comercial" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idRazon" class="col-sm-3 col-form-label">Razon Social</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idRazon" name="idRazon" placeholder="Razon Social" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idCiudad" class="col-sm-3 col-form-label">Ciudad</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCiudad" name="idCiudad" placeholder="Ciudad" value="MACHALA" required> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idDireccion" class="col-sm-3 col-form-label">Dirección</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idDireccion" name="idDireccion" placeholder="Direccion" value="S/D" required> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idTelefono" class="col-sm-3 col-form-label">Teléfono</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idTelefono" name="idTelefono" placeholder="Ej. 022999999" value="0" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idCelular" class="col-sm-3 col-form-label">Celular</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCelular" name="idCelular" placeholder="Ej. 0999999999" value="0" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idcedulaRepresentante" class="col-sm-3 col-form-label">Cedula del Representante</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idcedulaRepresentante" name="idcedulaRepresentante" placeholder="Cedula del Representante" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idRepresentante" class="col-sm-3 col-form-label">Representante Legal</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idRepresentante" name="idRepresentante" placeholder="Representante Legal" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idcedulacontador" class="col-sm-3 col-form-label">Cedula del Contador</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idcedulacontador" name="idcedulacontador" placeholder="Cedula del Contador" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idcontador" class="col-sm-3 col-form-label">Nombre del Contador</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idcontador" name="idcontador" placeholder="Nombre del Contador" >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="idFecha" class="col-sm-3 col-form-label">Fecha de Ingreso</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idFecha" name="idFecha" value="<?php echo(date("Y")."-".date("m")."-".date("d")); ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idEmail" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="idEmail" name="idEmail" placeholder="Email" value="SIN@CORREO" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <div class="icheck-success d-inline">
                                    <input type="checkbox" id="idContabilidad" name="idContabilidad" checked>
                                    <label for="idContabilidad">Lleva Contabilidad</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idTipo" class="col-sm-3 col-form-label">Tipo de Empresa</label>
                            <div class="col-sm-9">
                                <select class="custom-select" id="idTipo" name="idTipo" require>
                                    <option value="Microempresas">Microempresas</option>
                                    <option value="Agente de Retención">Agente de Retención</option>
                                    <option value="Contribuyente Especial">Contribuyente Especial</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idContribuyente" class="col-sm-3 col-form-label">Contri. Especial No.</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="idContribuyente" name="idContribuyente" value="0" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idContabilidad2" class="col-sm-3 col-form-label">Sistema Contable</label>
                            <div class=" col-sm-9">
                                <div class="icheck-success d-inline">
                                    <input type="checkbox" id="idContabilidad2" name="idContabilidad2" >
                                    <label for="idContabilidad2"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idElectronica" class="col-sm-3 col-form-label">Facturación Electrónica</label>
                            <div class=" col-sm-9">
                                <div class="icheck-success d-inline">
                                    <input type="checkbox" id="idElectronica" name="idElectronica" >
                                    <label for="idElectronica"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNomina" class="col-sm-3 col-form-label">Sistema de Nómina</label>
                            <div class=" col-sm-9">
                                <div class="icheck-success d-inline">
                                    <input type="checkbox" id="idNomina" name="idNomina" >
                                    <label for="idNomina"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idMedico" class="col-sm-3 col-form-label">Sistema Médico</label>
                            <div class=" col-sm-9">
                                <div class="icheck-success d-inline">
                                    <input type="checkbox" id="idMedico" name="idMedico" >
                                    <label for="idMedico"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idPrecios" class="col-sm-3 col-form-label">Permitir Cambiar Precios</label>
                            <div class=" col-sm-9">
                                <div class="icheck-success d-inline">
                                    <input type="checkbox" id="idPrecios" name="idPrecios" >
                                    <label for="idPrecios"></label>
                                </div>
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
@endsection