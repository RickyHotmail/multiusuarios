@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("excelInventario") }}"  enctype="multipart/form-data">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Cargar Excel Inventario</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <label for="excelProd" class="col-sm-1 col-form-label">Cargar Excel</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" id="excelProd" name="excelProd" accept=".xls,.xlsx" required>
                </div>
                <div class="col-sm-2">
                    <button type="submit" id="cargar" name="cargar" class="btn btn-primary btn-sm"><i class="fas fa-spinner"></i>&nbsp;Cargar</button>
                </div>
            </div>
            </br>
            <div class="form-group row">
                <label for="fecha" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha" name="fecha"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>

                <label for="bodega_id" class="col-sm-1 col-form-label"><center>Bodega:</center></label>
                <div class="col-sm-3">
                    <select class="custom-select" id="bodega_id" name="bodega_id" >                         
                        @foreach($bodega as $bodegas)
                            <option id="{{$bodegas->bodega_id}}" name="{{$bodegas->bodega_id}}" value="{{$bodegas->bodega_id}}">{{$bodegas->bodega_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <label for="centro_consumo_id" class="col-sm-1 col-form-label"><center>Centro Comsumo:</center></label>
                <div class="col-sm-3">
                    <select class="custom-select" id="centro_consumo_id" name="centro_consumo_id" >                         
                        @foreach($centro as $centros)
                            <option id="{{$centros->centro_consumo_id}}" name="{{$centros->centro_consumo_id}}" value="{{$centros->centro_consumo_id}}">{{$centros->centro_consumo_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
              
            </div>   
          
        </div>         
    </div>
</form>
@endsection