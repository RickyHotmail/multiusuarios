@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar esta empresa?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('empresa.destroy', [$empresa->empresa_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                 <!--     
                <button type="button" onclick='window.location = "{{ url("empresa") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Ruc</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_ruc}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre Comercial</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_nombreComercial}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Razon Social</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_razonSocial}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Ciudad</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_ciudad}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Dirección</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_direccion}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Teléfono</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_telefono}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Celular</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_celular}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Cedula del Representante</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_cedula_representante}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Representante Legal</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_representante}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Cedula del Contador</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_cedula_contador}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre del Contador</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_contador}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Fecha de Ingreso</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_fecha_ingreso}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_email}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Lleva Contabilidad</label>
                <div class="col-sm-10">
                    @if($empresa->empresa_llevaContabilidad=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tipo de Empresa</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_tipo}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Contribuyente Especial No.</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$empresa->empresa_contribuyenteEspecial}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Sistema Contable</label>
                <div class="col-sm-10">
                    @if($empresa->empresa_contabilidad=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Facturación Electrónica</label>
                <div class="col-sm-10">
                    @if($empresa->empresa_electronica=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Sistema de Nómina</label>
                <div class="col-sm-10">
                    @if($empresa->empresa_nomina=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Sistema Médico</label>
                <div class="col-sm-10">
                    @if($empresa->empresa_medico=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Permitir Cambiar Precios</label>
                <div class="col-sm-10">
                    @if($empresa->empresa_estado_cambiar_precio=="1") 
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($empresa->empresa_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection