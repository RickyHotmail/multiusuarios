@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("excelPrestamo") }}"  enctype="multipart/form-data">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Cargar Excel con Prestamos</h3>
            <div class="float-right">
                @if(isset($datos))
                <button type="submit" id="guardar" name="guardar" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                @endif
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm "><i class="fa fa-undo"></i>&nbsp;Atras</button>  
                <input type="hidden" class="form-control" id="idprestamo" name="idprestamo" value="{{$ide}}" >

            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <label for="excelPrestamos" class="col-sm-1 col-form-label">Cargar Excel</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" id="excelPrestamos" name="excelPrestamos" accept=".xls,.xlsx" required>
                </div>
                <div class="col-sm-2">
                    <button type="submit" id="cargar" name="cargar" class="btn btn-primary btn-sm"><i class="fas fa-spinner"></i>&nbsp;Cargar</button>
                </div>
            </div>
            </br>          
        </div>         
    </div>
</form>
@endsection