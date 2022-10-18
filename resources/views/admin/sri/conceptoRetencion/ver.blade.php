@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Concepto de Retencion</h3>
         <!--
        <button onclick='window.location = "{{ url("conceptoRetencion") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        -->      
        <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button> 
    </div>
    <!-- /.card-header -->
    <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$conceptoRetencion->concepto_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Codigo</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$conceptoRetencion->concepto_codigo}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Porcentaje</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$conceptoRetencion->concepto_porcentaje}}</label>
                </div>
            </div>
            <div class="form-group row">
                    <label for="producto_tipo" class="col-sm-2 col-form-label">Tipo</label>
                    <div class="col-sm-10">
                        @if($conceptoRetencion->concepto_tipo == '1')<label class="form-control">Retencion en la fuente </label>@endif
                        @if($conceptoRetencion->concepto_tipo == '2')<label class="form-control">IVA </label> @endif                 
                    </div>
            </div> 
            <div class="form-group row">
                <label for="producto_tipo" class="col-sm-2 col-form-label">Objeto</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$conceptoRetencion->concepto_objeto}}</label>                 
                </div>
            </div>          
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Cuenta Emitida</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$conceptoRetencion->cuentaEmitida->cuenta_numero.' - '.$conceptoRetencion->cuentaEmitida->cuenta_nombre}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Cuenta Recibida</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$conceptoRetencion->cuentaRecibida->cuenta_numero.' - '.$conceptoRetencion->cuentaRecibida->cuenta_nombre}}</label>                          
                </div>
            </div>                           
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($conceptoRetencion->concepto_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
        </div>
        <!-- /.card-body -->        
        <!-- /.card-footer -->
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection