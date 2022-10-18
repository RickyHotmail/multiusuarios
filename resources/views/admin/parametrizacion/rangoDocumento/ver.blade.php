@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Rango de Documentos</h3>
        <button onclick='window.location = "{{ url("rangoDocumento") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <div class="card-body">
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Tipo de Comprobante</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$rangoDocumento->tipoComprobante->tipo_comprobante_nombre}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Punto de Emision</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$rangoDocumento->puntoEmision->sucursal->sucursal_codigo}}{{$rangoDocumento->puntoEmision->punto_serie}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$rangoDocumento->rango_descripcion}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Rango de Inicio</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$rangoDocumento->rango_inicio}}</label>
                </div>
            </div>  
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Rango de Fin</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$rangoDocumento->rango_fin}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Fecha de Inicio</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$rangoDocumento->rango_fecha_inicio}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Fecha de Fin</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$rangoDocumento->rango_fecha_fin}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Autorizacion</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$rangoDocumento->rango_autorizacion}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($rangoDocumento->rango_estado=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection