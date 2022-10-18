@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("nauplio") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Nauplio</h3>
            <div class="float-right">
                <button type="button" onclick='window.location = "{{ url("laboratorioC") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                <button type="submit" class="btn btn-success btn-sm">Guardar</button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="form-group row">
                <label for="idNombre" class="col-sm-3 col-form-label">Nombre</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" required>
                    <input type="hidden" class="form-control" id="idLaboratorio" name="idLaboratorio" value="{{$id}}">
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
</form>
<!-- /.modal -->
@endsection