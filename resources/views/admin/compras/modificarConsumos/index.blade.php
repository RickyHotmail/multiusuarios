@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Reporte de Centro de Consumos</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("modificarConsumo") }} "> 
        @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idDescripcion" class="col-sm-2 col-form-label"><center>C. Consumo:</center></label>
                <div class="col-sm-3">
                    <select class="form-control select2" id="idCentroc" name="idCentroc" data-live-search="true">
                        <option value="0" label>--TODOS--</option>      
                        @foreach($CentroConsumos as $CentroConsumo)
                            <option value="{{$CentroConsumo->centro_consumo_id}}" @if(isset($cc)) @if($cc == $CentroConsumo->centro_consumo_id) selected @endif  @endif>                                
                                {{$CentroConsumo->centro_consumo_nombre}}
                            </option>
                        @endforeach
                    </select>  
                </div>
                
                <div class="col-sm-1">
                    <center><button id="buscarID" name="buscarID" type="submit"  class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div> 
            @if(isset($productos))
            <div class="form-group row">
            <label for="idDescripcion" class="col-sm-2 col-form-label"><center>Cambiar C. Consumo : </center></label>
                <div class="col-sm-3">
                    <select class="form-control select2" id="idConsumo" name="idConsumo" data-live-search="true">
                      
                        @foreach($CentroConsumos as $CentroConsumo)
                            <option value="{{$CentroConsumo->centro_consumo_id}}">                                
                                {{$CentroConsumo->centro_consumo_nombre}}
                            </option>
                        @endforeach
                    </select>  
                </div>
                <div class="col-sm-2">
                    <button id="guardarID" name="guardarID" type="submit" class="btn btn-success" ><i
                                class="fa fa-save"></i><span> Guardar</span></button>
                </div>
            </div>  
            @endif                    
        
        <hr>
        <table id="example4"  class="table table-bordered table-hover table-responsive sin-salto" >
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Fecha</th>
                    <th>Numero</th>
                    <th>Proveedor</th>
                    <th>Producto</th>
                    <th>Centro Consumo</th>
                
                </tr>
            </thead> 
            <tbody>
                <?php $count = 0; ?>
                @if(isset($productos))
                    @foreach($productos as $producto)
                
                        <tr class="text-center">
                            <td>
                                <input class="invisible" name="idcc[]" value="{{ $producto->transaccion_id }}" />
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="item{{$count}}"  name="contador[]"  value="{{ $count }}" > 
                                    <label for="item{{$count}}">
                                    </label>
                                </div>
                            </td>
                            <td>{{$producto->transaccion_fecha}}</td> 
                            <td>{{$producto->transaccion_numero}}</td>
                            <td>
                            {{$producto->proveedor->proveedor_nombre}}
                            </td>
                            
                            <td>{{$producto->producto_nombre}}</td>                    
                            <td>{{$producto->centro_consumo_nombre}}</td>                    
                            
                            <?php $count++; ?>   
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        
        </form>
    </div>
    <!-- /.card-body -->
</div>
@endsection