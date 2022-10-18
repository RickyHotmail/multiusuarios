@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Nauplio</h3>
        <button type="button" onclick='window.location = "{{ url("laboratorioC") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>

    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Nombre</th>
                   
                </tr>
            </thead> 
            <tbody>
                @if(isset($nauplios))
                    @foreach($nauplios as $nauplio)
                    <tr class="text-center">
                        <td>
                            <a href="{{ url("nauplio/{$nauplio->nauplio_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            <a href="{{ url("nauplio/{$nauplio->nauplio_id}/ver") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a href="{{ url("nauplio/{$nauplio->nauplio_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                          
                        </td>
                        <td>{{ $nauplio->nauplio_nombre}}</td>
                       
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>

<!-- /.modal -->
@endsection