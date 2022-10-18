@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Consultar Documentos Electronico en el SRI</h3>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("sriDocElec") }}">
            @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-2 col-form-label">
                    Clave de Acceso
                </label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="clave" name="clave" placeholder="clave de acceso" required>
                </div>                
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>
        </form>
        <textarea class="form-control" rows="25">@if(isset($resp)) {{print_r($resp)}} @endif</textarea>
    </div>
</div>
@endsection