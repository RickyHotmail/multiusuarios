@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar esta especialidad?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('especialidad.destroy', [$especialidad->especialidad_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <button type="button" onclick='window.location = "{{ url("especialidad") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Codigo</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$especialidad->especialidad_codigo}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$especialidad->especialidad_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="producto_tipo" class="col-sm-2 col-form-label">Tipo</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="producto_tipo" name="producto_tipo" disabled>
                        <option value="1" @if($especialidad->especialidad_tipo == '1') selected @endif>ESPECIALISTA</option>
                        <option value="2" @if($especialidad->especialidad_tipo == '2') selected @endif>GENERAL</option>
                        <option value="3" @if($especialidad->especialidad_tipo == '3') selected @endif>ODONTOLOGIA</option>                                                      
                    </select>
                </div>
            </div>             
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Duración </label>
                <div class="col-sm-10">
                    <label class="form-control">{{$especialidad->especialidad_duracion.' min'}}</label>
                </div>
            </div>  
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Duración Flexible</label>
                <div class="col-sm-10">
                    @if($especialidad->especialidad_flexible=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>                
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($especialidad->especialidad_estado=="1")
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