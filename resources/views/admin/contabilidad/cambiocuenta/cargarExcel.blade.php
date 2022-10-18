@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("excelCambioCuentas") }}"  enctype="multipart/form-data">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Cargar Excel Cambio de Cuentas</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <label for="excelProd" class="col-sm-1 col-form-label">Cargar Excel</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" id="excelCuenta" name="excelCuenta" accept=".xls,.xlsx" required>
                </div>
                <div class="col-sm-2">
                    <button type="submit" id="cargar" name="cargar" class="btn btn-primary btn-sm"><i class="fas fa-spinner"></i>&nbsp;Cargar</button>
                </div>
                
            </div>    
            <div class="row">
                <div class="col-sm-5" style="margin-bottom: 0px;">
                    <label>Cuenta Contable Actual</label>
                    <div class="form-group">
                        <div class="form-line">
                            <select class="form-control select2" id="cuenta" name="cuenta" style="width: 100%;">
                                @foreach($cuentas as $cuenta)
                                    <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>   
            </div>     
            <div class="row">
                <div class="col-sm-5" style="margin-bottom: 0px;">
                    <label>Cuenta Contable Cambio</label>
                    <div class="form-group">
                        <div class="form-line">
                            <select class="form-control select2" id="cuentanew" name="cuentanew" style="width: 100%;">
                                @foreach($cuentas as $cuenta)
                                    <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>   
            </div>    
        </div>         
    </div>
</form>
@endsection