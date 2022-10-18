@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Informe Histórico</h3>
        
    </div>
    <!-- /.card-header -->
    <div class="card-body">
    <form class="form-horizontal" method="POST" action="{{ url("generarreportehistoricoplano") }}">
        @csrf
        <div class="col-md-12">
            <div class="form-group row">
                <div class="col-sm-12">
                    <div class="row mb-2">
                        <label for="fecha_desde" class="col-sm-1 col-form-label text-right">Desde :</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fDesde)){echo $fDesde;}else{ echo(date("Y")."-".date("m")."-"."01");} ?>' required>
                        </div>
                        <label for="fecha_hasta" class="col-sm-1 col-form-label text-right">Hasta :</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fHasta)){echo $fHasta;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                        </div>

                        <div class="custom-control custom-checkbox col-sm2 pt-2">
                            <input class="custom-control-input" type="checkbox" id="incluirFechas" name="incluirFechas" value="1" <?php if(isset($fechasI)) echo "checked"; ?>>
                            <label for="incluirFechas" class="custom-control-label">Todo</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <label for="fecha_desde" class="col-sm-1 col-form-label text-right">Sucursal :</label>
                <div class="col-sm-3">
                    <select class="form-control" name="sucursal">
                        @foreach($sucursales as $sucursal)
                            <option value={{$sucursal->sucursal_id}}>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-right">
                <input class="btn btn-success" type="submit" value="Generar">
            </div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.modal -->
@endsection