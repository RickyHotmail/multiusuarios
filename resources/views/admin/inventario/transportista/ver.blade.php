@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Transportista</h3>
        <!--   
        <button onclick='window.location = "{{ url("transportista") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Cedula</label>
            <div class="col-sm-10">
                <label class="form-control">{{$transportista->transportista_cedula}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Nombre</label>
            <div class="col-sm-10">
                <label class="form-control">{{$transportista->transportista_nombre}}</label>
            </div>
        </div>            
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Placa</label>
            <div class="col-sm-10">                        
                <label class="form-control">{{$transportista->transportista_placa}}</label>                          
            </div>
        </div>
        @if($transportista->transportista_embarcacion!=Null)
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Embarcacion</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$transportista->transportista_embarcacion}}</label>                          
                </div>
            </div>
        @endif                     
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Estado</label>
            <div class="col-sm-10">
                @if($transportista->transportista_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                @endif
            </div>
        </div>  
        <!-- /.card-footer -->
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection