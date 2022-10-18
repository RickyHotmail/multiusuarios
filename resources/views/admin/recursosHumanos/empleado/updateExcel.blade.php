@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("updateEmpleado") }}"  enctype="multipart/form-data">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Actualiza Excel con Empleados</h3>
            <div class="float-right">
                @if(isset($datos))
                <button type="submit" id="guardar1" name="guardar1" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                @endif
                <button type="button" onclick='window.location = "{{ url("empleado") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            @if(!isset($datos))
            <div class="row">
                <label for="excelProd" class="col-sm-1 col-form-label">Cargar Excel</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" id="excelEmpl" name="excelEmpl" accept=".xls,.xlsx" required>
                </div>
                <div class="col-sm-2">
                    <button type="submit" id="cargar1" name="cargar1" class="btn btn-primary btn-sm"><i class="fas fa-spinner"></i>&nbsp;Cargar</button>
                </div>
            </div>
            </br>
            @endif            
        </div>         
    </div>
</form>
@endsection