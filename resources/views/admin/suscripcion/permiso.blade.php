@extends ('admin.layouts.admin')
@section('principal')

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Permisos Empresa <strong>{{$empresa->empresa_razonSocial}}</strong></h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-cog"></i>&nbsp;Editar Permisos</button>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("/administracion/empresa/{$empresa->empresa_id}") }}/guardarPermisos" enctype="multipart/form-data" onsubmit="return verificarFoto()">
            @csrf

            <select class="form-select" name="permiso_general">
                @foreach($grupos as $grupo)
                    <option value={{$grupo->parametrizaciong_id}} @if($empresa->suscripcion) @if($empresa->suscripcion->suscripcion_permiso==$grupo->parametrizaciong_id) selected @endif @endif>
                        {{$grupo->parametrizaciong_nombre}}
                    </option>
                @endforeach
            </select>

            <div class="modal-footer justify-content-between">
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection