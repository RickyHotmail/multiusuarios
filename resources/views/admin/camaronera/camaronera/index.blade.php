@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("camaronera") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Camaronera</h3>
            <button type="submit" class="btn btn-success btn-sm float-right"><i class="fa fa-save"></i>&nbsp;Guardar</button>
            <input type="hidden" class="form-control" id="idCamaronera" name="idCamaronera"  @if(isset($camaronera->camaronera_id)) value="{{$camaronera->camaronera_id}}" @else value="0" @endif   required>
        </div>
        <div class="card-body">                       
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Nombre de Camaronera</label>
                <div class="col-sm-9">
                <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre de Camaronera" value="@if(isset($camaronera->camaronera_nombre)){{$camaronera->camaronera_nombre}}@endif"   required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idArea" class="col-sm-2 col-form-label">Area</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idArea" name="idArea" placeholder="Area" value="@if(isset($camaronera->camaronera_area)){{$camaronera->camaronera_area}}@endif"  placeholder="Area" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idUbicacion" class="col-sm-2 col-form-label">Ubicación</label>
                <div class="col-sm-9">
                    <textarea  class="form-control" id="idUbicacion" name="idUbicacion" placeholder="Ubicación">@if(isset($camaronera->camaronera_ubicacion)){{$camaronera->camaronera_ubicacion}}@endif</textarea>
                </div>
            </div>
        </div>              
    </div>
</form>
@endsection