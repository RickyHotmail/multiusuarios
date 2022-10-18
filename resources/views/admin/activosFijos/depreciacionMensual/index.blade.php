@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Depreciacion de Activos Fijos</h3>
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
                <label for="idDate" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <div class="form-group">
                        <div class="form-line">
                            @if(isset($fechaselect))
                                <input type="month" name="fechames" id="fechames" class="form-control" value="{{$fechaselect}}">
                            @else
                                <input type="month" name="fechames" id="fechames" class="form-control" value='<?php echo(date("Y")."-".date("m")); ?>' >
                            @endif
                        </div>
                    </div> 
                </div>                 
                <div class="col-sm-2">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <button type="submit" id="guardar" name="guardar" class="btn btn-secondary" title="DEPRECIAR"><i class="fas fa-save"></i></button>                  
                </div>
                <label for="idsucursal" class="col-sm-1 col-form-label"><center>Diario:</center></label>
                <div class="col-sm-2"> 
                    @if(isset($diarioexiste))
                        <label class="form-control"><input type="hidden" name="diarioID" id="diarioID" value="{{$diarioexiste->diario_id}}"><a href="{{ url("asientoDiario/ver/{$diarioexiste->diario_codigo}")}}" target="_blank">{{$diarioexiste->diario_codigo}}</a></label>
                        <input type="hidden" name="diarioCodigo" id="diarioCodigo" value="{{$diarioexiste->diario_codigo}}">
                    @else
                        <label class="form-control">MES NO DEPRECIADO</label>
                    @endif
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
                        <td>{{ number_format($activosFijosMatriz[$i]['Valor'],2,'.','')}}</td>
                        <td>{{ $activosFijosMatriz[$i]['PorcentajeDepreciacion']}}</td>
                        <td>{{ number_format($activosFijosMatriz[$i]['baseDepreciar'],2,'.','')}}</td>
                        <td>{{ $activosFijosMatriz[$i]['VidaUtil']}}</td>
                        <td>{{ number_format($activosFijosMatriz[$i]['ValorUtil'],2,'.','')}}</td>
                        <td>{{ number_format($activosFijosMatriz[$i]['DeprecicacionMensual'],2,'.','')}}<input type="hidden" name="valorId[]" id="valorId[]" value="{{ number_format($activosFijosMatriz[$i]['DeprecicacionMensual'],2,'.','')}}"></td>
                        <td>{{ number_format($activosFijosMatriz[$i]['DeprecicacionAnual'],2,'.','')}}</td>
                        <td>{{ number_format($activosFijosMatriz[$i]['DeprecicacionAcumulada'],2,'.','')}}</td>
                        <td>{{ number_format($activosFijosMatriz[$i]['ValoresLibro'],2,'.','')}}</td>
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
@endsection