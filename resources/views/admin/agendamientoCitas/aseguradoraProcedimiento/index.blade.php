@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Aseguradora Procedimiento</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
    <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Cedula</th>
                    <th>Nombre</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clienteAseguradoras as $clienteAseguradora)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("aseguradoraProcedimiento/{$clienteAseguradora->cliente_id }/procedimiento")}}" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Asignar Procedimiento"><i class="fa fa-tasks" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $clienteAseguradora->cliente_cedula }}</td>
                    <td>{{ $clienteAseguradora->cliente_nombre }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
@endsection