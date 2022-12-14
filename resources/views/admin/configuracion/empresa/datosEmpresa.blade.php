@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('datosEmpresa', [$empresa->empresa_id]) }}" enctype="multipart/form-data">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Datos de empresa</h3>
            <button type="submit" class="btn btn-success btn-sm float-right"><i class="fa fa-save"></i>&nbsp;Guardar</button>
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
                <label for="idDireccion" class="col-sm-2 col-form-label">Direcci??n</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idDireccion" name="idDireccion" placeholder="Direccion " value="{{$empresa->empresa_direccion}}" required> 
                </div>
            </div>
            <div class="form-group row">
                <label for="idTelefono" class="col-sm-2 col-form-label">Tel??fono</label>
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
                    <input type="email" class="form-control" id="idEmail" name="idEmail" placeholder="Email" value="{{$empresa->empresa_email}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idFacturaCorreo" class="col-sm-2 col-form-label">Facturas al Correo</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    @if($empresa->empresa_factura_correo==1)    
                        <input type="checkbox" class="custom-control-input" id="idFacturaCorreo" name="idFacturaCorreo" checked>
                    @else
                        <input type="checkbox" class="custom-control-input" id="idFacturaCorreo" name="idFacturaCorreo">
                    @endif
                    <label class="custom-control-label" for="idFacturaCorreo"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="idContabilidad" class="col-sm-2 col-form-label">Lleva Inventario</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($empresa->empresa_control_inventario=="1")    
                            <input type="checkbox" class="custom-control-input" id="idInventario" name="idInventario" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idInventario" name="idInventario">
                        @endif
                        <label class="custom-control-label" for="idInventario"></label>
                    </div>
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
                <label for="idPrecios" class="col-sm-2 col-form-label">Permitir Cambiar Precios</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    @if($empresa->empresa_estado_cambiar_precio=="1") 
                        <input type="checkbox" class="custom-control-input" id="idPrecios" name="idPrecios" checked>
                    @else
                        <input type="checkbox" class="custom-control-input" id="idPrecios" name="idPrecios">
                    @endif
                    <label class="custom-control-label" for="idPrecios"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Empresa</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="idTipo" name="idTipo"required>
                        <option value="Microempresas" @if($empresa->empresa_tipo == 'Microempresas') selected @endif>Microempresas</option>
                        <option value="Agente de Retenci??n" @if($empresa->empresa_tipo == 'Agente de Retenci??n') selected @endif>Agente de Retenci??n</option>
                        <option value="Contribuyente R??gimen Rimpe" @if($empresa->empresa_tipo == 'Contribuyente R??gimen Rimpe') selected @endif>Contribuyente R??gimen Rimpe</option>
                        <option value="Contribuyente Rimpe y Agente de Retenci??n" @if($empresa->empresa_tipo == 'Contribuyente Rimpe y Agente de Retenci??n') selected @endif>Contribuyente RIMPE y Agente de Retenci??n</option>
                        <option value="Contribuyente Negocio Popular - R??gimen Rimpe" @if($empresa->empresa_tipo == 'Contribuyente Negocio Popular - R??gimen Rimpe') selected @endif>Contribuyente Negocio Popular - R??gimen Rimpe</option>
                        <option value="Contribuyente R??gimen General" @if($empresa->empresa_tipo == 'Contribuyente R??gimen General') selected @endif>Contribuyente R??gimen General</option>
                        <option value="Contribuyente R??gimen General y Agente de Retenci??n" @if($empresa->empresa_tipo == 'Contribuyente R??gimen General y Agente de Retenci??n') selected @endif>Contribuyente R??gimen General y Agente de Retenci??n</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idContribuyente" class="col-sm-2 col-form-label">Contribuyente Especial No.</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idContribuyente" name="idContribuyente" value="{{$empresa->empresa_contribuyenteEspecial}}" required>
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
            <div class="form-group row">
                <label for="idLogo" class="col-sm-2 col-form-label">Logo</label>
                <div class="col-sm-10">
                    <div class="file-loading">
                        <input id="file-es" name="file-es" type="file" data-theme="fas" accept="image/png, .jpeg, .jpg">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@section('scriptCode')
    $('#file-es').fileinput({
        theme: 'fas',
        language: 'es',
        showUpload: false,
        browseClass: "btn btn-secondary",
        initialPreviewShowDelete: false,
        allowedFileExtensions: ['jpg', 'png', 'jpeg'],
        @if(!empty($empresa->empresa_logo))
            initialPreviewAsData: true,
            initialPreview: [
                "logos/{{$empresa->empresa_logo}}",
            ]
        @endif
    });
@endsection