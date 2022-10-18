@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Nauplio</h3>
        <button onclick='window.location = "{{ url("nauplio/{$nauplio->laboratorio_id}") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$nauplio->nauplio_nombre}}</label>
                </div>
            </div>
           
        </div>        
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection