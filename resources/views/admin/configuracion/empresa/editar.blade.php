@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('empresa.update', [$empresa->empresa_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar empresa</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--     
                <button type="button" onclick='window.location = "{{ url("empresa") }}";'  class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="Ruc" class="col-sm-2 col-form-label">Ruc</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="Ruc" name="Ruc" placeholder="Ej. 9999999999999" value="{{$empresa->empresa_ruc}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Nombre Comercial</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre Comercial" value="{{$empresa->empresa_nombreComercial}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idRazon" class="col-sm-2 col-form-label">Razon Social</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idRazon" name="idRazon" placeholder="Razon Social" value="{{$empresa->empresa_razonSocial}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCiudad" class="col-sm-2 col-form-label">Ciudad</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCiudad" name="idCiudad" placeholder="Ciudad " value="{{$empresa->empresa_ciudad}}" required> 
                </div>
            </div>
            <div class="form-group row">
                <label for="idDireccion" class="col-sm-2 col-form-label">Dirección</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idDireccion" name="idDireccion" placeholder="Direccion " value="{{$empresa->empresa_direccion}}" required> 
                </div>
            </div>
            <div class="form-group row">
                <label for="idTelefono" class="col-sm-2 col-form-label">Teléfono</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idTelefono" name="idTelefono" placeholder="Ej. 022999999" value="{{$empresa->empresa_telefono}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCelular" class="col-sm-2 col-form-label">Celular</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCelular" name="idCelular" placeholder="Ej. 0999999999" value="{{$empresa->empresa_celular}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idcedulaRepresentante" class="col-sm-2 col-form-label">Cedula del Representante</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idcedulaRepresentante" name="idcedulaRepresentante" placeholder="Cedula del Representante" value="{{$empresa->empresa_cedula_representante}}" >
                </div>
            </div>
            <div class="form-group row">
                <label for="idRepresentante" class="col-sm-2 col-form-label">Representante Legal</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idRepresentante" name="idRepresentante" placeholder="Representante Legal" value="{{$empresa->empresa_representante}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idcedulacontador" class="col-sm-2 col-form-label">Cedula del Contador</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idcedulacontador" name="idcedulacontador" placeholder="Cedula del Contador" value="{{$empresa->empresa_cedula_contador}}" >
                </div>
            </div>
            <div class="form-group row">
                <label for="idcontador" class="col-sm-2 col-form-label">Nombre del Contador</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idcontador" name="idcontador" placeholder="Nombre del Contador" value="{{$empresa->empresa_contador}}" >
                </div>
            </div>
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label">Fecha de Ingreso</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="idFecha" name="idFecha" value="{{$empresa->empresa_fecha_ingreso}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEmail" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="idEmail" name="idEmail" placeholder="Email" value="SIN@CORREO" value="{{$empresa->empresa_email}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idContabilidad" class="col-sm-2 col-form-label">Lleva Contabilidad</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    @if($empresa->empresa_llevaContabilidad=="1")    
                        <input type="checkbox" class="custom-control-input" id="idContabilidad" name="idContabilidad" checked>
                    @else
                        <input type="checkbox" class="custom-control-input" id="idContabilidad" name="idContabilidad">
                    @endif
                    <label class="custom-control-label" for="idContabilidad"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Empresa</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="idTipo" name="idTipo"required>
                        <option value="Microempresas" @if($empresa->empresa_tipo == 'Microempresas') selected @endif>Microempresas</option>
                        <option value="Agente de Retención" @if($empresa->empresa_tipo == 'Agente de Retención') selected @endif>Agente de Retención</option>
                        <option value="Contribuyente Especial" @if($empresa->empresa_tipo == 'Contribuyente Especial') selected @endif>Contribuyente Especial</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idContribuyente" class="col-sm-2 col-form-label">Contribuyente Especial No.</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="idContribuyente" name="idContribuyente" value="{{$empresa->empresa_contribuyenteEspecial}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idContabilidad2" class="col-sm-2 col-form-label">Sistema Contable</label>
                <div class=" col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($empresa->empresa_contabilidad=="1") 
                            <input type="checkbox" class="custom-control-input" id="idContabilidad2" name="idContabilidad2" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idContabilidad2" name="idContabilidad2">
                        @endif
                        <label for="idContabilidad2" class="custom-control-label"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="idElectronica" class="col-sm-2 col-form-label">Facturación Electrónica</label>
                <div class=" col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($empresa->empresa_electronica=="1") 
                            <input type="checkbox" class="custom-control-input" id="idElectronica" name="idElectronica" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idElectronica" name="idElectronica">
                        @endif
                        <label for="idElectronica" class="custom-control-label"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNomina" class="col-sm-2 col-form-label">Sistema de Nómina</label>
                <div class=" col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($empresa->empresa_nomina=="1") 
                            <input type="checkbox" class="custom-control-input" id="idNomina" name="idNomina" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idNomina" name="idNomina">
                        @endif
                        <label for="idNomina" class="custom-control-label"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="idMedico" class="col-sm-2 col-form-label">Sistema Médico</label>
                <div class=" col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($empresa->empresa_medico=="1") 
                            <input type="checkbox" class="custom-control-input" id="idMedico" name="idMedico" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idMedico" name="idMedico">
                        @endif
                        <label for="idMedico" class="custom-control-label"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">     
                <label for="idPrecios" class="col-sm-2 col-form-label">Permitir Cambiar Precios</label>
                <div class=" col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($empresa->empresa_estado_cambiar_precio=="1") 
                        <input type="checkbox" class="custom-control-input" id="idPrecios" name="idPrecios" checked>
                        @else
                        <input type="checkbox" class="custom-control-input" id="idPrecios" name="idPrecios">
                        @endif
                        <label for="idPrecios" class="custom-control-label"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    @if($empresa->empresa_estado=="1") 
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                    @else
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                    @endif
                    <label class="custom-control-label" for="idEstado"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection