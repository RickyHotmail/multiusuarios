@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Tarifa de Iva</h3>
         <!--  
        <button onclick='window.location = "{{ url("tarifaIva") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <div class="card-body">        
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Codigo</label>
            <div class="col-sm-10">
                <label class="form-control">{{$tarifaIva->tarifa_iva_codigo}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Porcentaje</label>
            <div class="col-sm-10">
                <label class="form-control">{{$tarifaIva->tarifa_iva_porcentaje}}</label>
            </div>
        </div>    
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Estado</label>
            <div class="col-sm-10">
                @if($tarifaIva->tarifa_iva_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                @endif
            </div>
        </div>        
    </div>
</div>
@endsection