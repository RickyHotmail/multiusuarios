@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST"  action="{{ url("inicializarCXC") }} " enctype="multipart/form-data">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Inicializar Cuentas por Cobrar</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="form-group row">
                <label for="diario_id" class="col-sm-1 col-form-label">Diarios : </label>
                <div class="col-sm-3">
                    <select class="custom-select select2" id="diario_id" name="diario_id" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($diarios as $diario)
                            <option value="{{$diario->diario_id}}" @if(isset($diarioC)) @if($diarioC->diario_id == $diario->diario_id) selected @endif @endif>{{$diario->diario_codigo}}</option>
                        @endforeach
                    </select>                    
                </div>
                <label for="diario_id" class="col-sm-2 col-form-label">Cargar excel con cuentas : </label>
                <div class="col-sm-4">
                    <input type="file" id="file_cuentas" name="file_cuentas" class="form-control"/>                  
                </div>
                <div class="col-sm-2">
                    <a class="btn btn-success" href="{{ asset('admin/archivos/FORMATO-CUENTAS.xlsx') }}" download="FORMATO NEOPAGUPA"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
            </div>          
            <hr>
            <div class="row table-ini-cuentas">
                <div class="col-sm-12">
                    <table id="item" class="table table-striped table-hover boder-sar tabla-item-factura" style="white-space: normal!important; border-collapse: collapse;">
                        <thead>
                            <tr class="letra-blanca" style="background-color: #0c7181;">
                                <th></th>
                                <th>CÓDIGO</th>
                                <th>CUENTA</th>
                                <th>DESCRIPCIÓN </th>
                                <th class="centrar-texto">DEBE</th>
                                <th class="centrar-texto">HABER</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection