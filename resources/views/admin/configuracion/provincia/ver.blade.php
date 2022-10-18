@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Provincia</h3>
        <button onclick='window.location = "{{ url("provincia") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <div class="card-body">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Nombre de Provincia</label>
            <div class="col-sm-10">
                <label class="form-control">{{$provincia->provincia_nombre}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Codigo de Provincia</label>
            <div class="col-sm-10">
                <label class="form-control">{{$provincia->provincia_codigo}}</label>
            </div>
        </div>               
        <div class="form-group row">
            <label for="pais_id" class="col-sm-2 col-form-label">Nombre de Pais</label>
            <div class="col-sm-10">                        
                <label class="form-control">{{$provincia->pais_nombre}}</label>                          
            </div>
        </div>                      
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Estado</label>
            <div class="col-sm-10">
                @if($provincia->provincia_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection