@extends ('admin.layouts.admin')
@section('principal')

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Permisos Empresa</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-cog"></i>&nbsp;Editar Permisos</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("/administracion/empresa/{$empresa->empresa_id}") }}/cambiarPermiso" enctype="multipart/form-data" onsubmit="return verificarFoto()">
            @csrf

            <select class="form-select" name="permiso_general">
                <option value=1>General</option>
                <option value=2>Camaronero</option>
                <option value=3>Médico</option>
                <option value=4>Facturación</option>
            </select>

            <div class="modal-footer justify-content-between">
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection