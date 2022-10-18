@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar esta Bodega?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('bodega.destroy', [$bodega->bodega_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("bodega") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
        <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre de Bodega</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$bodega->bodega_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$bodega->bodega_descripcion}}</label>
                </div>
            </div>  
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Direccion</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$bodega->bodega_direccion}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Telefono</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$bodega->bodega_telefono}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Fax</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$bodega->bodega_fax}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="ciudad_id" class="col-sm-2 col-form-label">Ciudad</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$bodega->ciudad->ciudad_nombre}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$bodega->sucursal_nombre}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($bodega->bodega_estado=="1")
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