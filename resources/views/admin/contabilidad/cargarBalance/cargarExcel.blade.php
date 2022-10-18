@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("cargaBalances") }}"  enctype="multipart/form-data">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Cargar Excel Balances</h3>
            <div class="float-right">
                <a class="btn btn-info btn-sm" href="{{ asset('admin/archivos/FORMATO_BALANCES.xlsx') }}" download="FORMATO BALANCES"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="excelProd" class="col-sm-1 col-form-label">Diario</label>
                <div class="col-sm-9">
                        <select class="form-control select2" id="diario" name="diario" style="width: 100%;" required>
                            <option value="" label>--Seleccione--</option>
                            @foreach($diarios as $diario)
                                <option value="{{$diario->diario_id}}">{{$diario->diario_codigo}}</option>
                            @endforeach
                        </select>
                   
                </div>
               
            </div>
            <div class="form-group row">
                <label for="excelProd" class="col-sm-1 col-form-label">Cargar Excel</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" id="excelEmpl" name="excelEmpl" accept=".xls,.xlsx" required>
                </div>
                <div class="col-sm-2">
                    <button type="submit" id="cargar" name="cargar" class="btn btn-primary btn-sm"><i class="fas fa-spinner"></i>&nbsp;Cargar</button>
                </div>
            </div>         
        </div>         
    </div>
</form>
@endsection