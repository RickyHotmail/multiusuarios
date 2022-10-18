@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Reporte de Depreciacion de Activos Fijos</h3>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("depreciacionConsultar") }}">
        @csrf
            <div class="form-group row">
                <label for="idsucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="idsucursal" name="idsucursal">
                          
                        @foreach($sucursales as $sucursal)                            
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($sucursalselect)) @if($sucursalselect == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                       @endforeach
                    </select> 
                </div>
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                @if(isset($fechaselect2))
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value="{{$fechaselect2}}" required>
                @else
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                @endif
                </div>
                <label for="fechames" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                @if(isset($fechaselect))
                    <input type="date" class="form-control" id="fechames" name="fechames"  value="{{$fechaselect}}" required>
                @else
                    <input type="date" class="form-control" id="fechames" name="fechames"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                @endif
                </div>          
                <div class="col-sm-1">
                    <button onclick="girarGif()" type="submit" id="buscarReporte" name="buscarReporte" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>       
            </div>            
            <div class="card-body table-responsive p-0" style="height: 540px;">
            <table id="example4" class="table table-head-fixed text-nowrap">
                <thead>
                    <tr>  
                        <th>Fecha</th>
                        <th>Diario</th>
                        <th>Producto</th>
                        <th>Tipo de Activo</th>                        
                        <th>Valor</th>
                        <th>% Depreciacion</th>
                        <th>Base depreciar</th>
                        <th>Vida util</th>
                        <th>Valor util</th>
                        <th>Depreciacion Mensual</th>
                        <th>Depreciacion Anual</th>
                        <th>Depreciacion Acumulada</th>
                        <th>Depreciacion Historica</th>
                        <th>Valores en Libro</th>
                        <th>Descripcion</th>
                    </tr>
                </thead>            
                <tbody>
                @if(isset($activosFijosMatriz))
                    @for ($i = 1; $i <= count($activosFijosMatriz); ++$i)               
                    <tr class="text-center">
                        <td>{{ $activosFijosMatriz[$i]['Fecha'] }}</td>
                        <td><a href="{{ url("asientoDiario/ver/{$activosFijosMatriz[$i]['Diario']}")}}" target="_blank">{{ $activosFijosMatriz[$i]['Diario']}}</a></td>
                        <td>{{ $activosFijosMatriz[$i]['Producto']}}<input type="hidden" name="activoId[]" id="activoId[]" value="{{ $activosFijosMatriz[$i]['activo_id']}}"></td>
                        <td>{{ $activosFijosMatriz[$i]['TipoActivo']}}</td>                        
                        <td>{{ $activosFijosMatriz[$i]['Valor']}}</td>
                        <td>{{ $activosFijosMatriz[$i]['PorcentajeDepreciacion']}}</td>
                        <td>{{ $activosFijosMatriz[$i]['baseDepreciar']}}</td>
                        <td>{{ $activosFijosMatriz[$i]['VidaUtil']}}</td>
                        <td>{{ $activosFijosMatriz[$i]['ValorUtil']}}</td>
                        <td>{{ $activosFijosMatriz[$i]['DeprecicacionMensual']}}<input type="hidden" name="valorId[]" id="valorId[]" value="{{ $activosFijosMatriz[$i]['DeprecicacionMensual']}}"></td>
                        <td>{{ $activosFijosMatriz[$i]['DeprecicacionAnual']}}</td>
                        <td>{{ $activosFijosMatriz[$i]['DeprecicacionAcumulada']}}</td>
                        <td>{{ $activosFijosMatriz[$i]['DeprecicacionHistorica']}}</td>
                        <td>{{ $activosFijosMatriz[$i]['ValoresLibro']}}</td>
                        <td class="text-left">{{ $activosFijosMatriz[$i]['Descripcion']}}</td>
                    </tr>
                    @endfor
                @endif
                </tbody>
            </table>
            </div>
        </form>    
    </div>
</div>
<div id="div-gif" class="col-md-12 text-center" style="position: absolute;height: 300px; margin-top: 150px; display: none">
    <img src="{{ url('img/loading.gif') }}" width=90px height=90px style="align-items: center">
</div>
<script>
    function girarGif(){
        document.getElementById("div-gif").style.display="inline"
        console.log("girando")
    }
</script>
@endsection