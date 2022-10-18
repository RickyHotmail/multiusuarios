@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Compras por Producto.</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("reporteComprasxProducto") }} "> 
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
                <label for="idDescripcion" class="col-sm-2 col-form-label"><center>Producto:</center></label>
                <div class="col-sm-3">
                    <select class="form-control select2" id="idProducto" name="idProducto" data-live-search="true">
                        @foreach($productos as $producto)
                            <option value="{{$producto->producto_id}}" @if(isset($cc)) @if($cc == $producto->producto_id) selected @endif  @endif>                                
                                {{$producto->producto_nombre}}
                            </option>
                        @endforeach
                    </select>  
                </div>
                <div class="col-sm-1">
                    <center><button id="buscarID" name="buscarID" type="submit"  class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div> 
            <div class="form-group row">
            <label for="idDescripcion" class="col-sm-2 col-form-label"><center>Cambiar Sustento. Tributario: </center></label>
                <div class="col-sm-3">
                    <select class="form-control select2" id="idSustento" name="idSustento"  data-live-search="true">
                      
                        @foreach($sustentos as $sustento)
                            <option value="{{$sustento->sustento_id}}" @if(isset($st)) @if($st == $sustento->sustento_id) selected @endif  @endif>                                
                                {{$sustento->sustento_codigo.' - '.$sustento->sustento_nombre}}
                            </option>
                        @endforeach
                    </select>  
                </div>
                <!--<label for="idDescripcion" class="col-sm-2 col-form-label"><center>C. Consumo:</center></label>
                <div class="col-sm-3">                    
                    <input type="text" class="form-control" id="idCentroc" name="idCentroc" required> 
                </div>-->
                @if(isset($detallesTC))
                    <div class="col-sm-2">
                        <button id="guardarID" name="guardarID" type="submit" class="btn btn-success" ><i
                                    class="fa fa-save"></i><span> Guardar</span></button>
                    </div>
                @endif
            </div>
        <hr>
        <table id="example4"  class="table table-bordered table-hover table-responsive sin-salto" >
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Fecha</th>
                    <th>Factura</th>
                    <th>Proveedor</th>
                    <th>Producto</th>
                    <th>Sustento Tributario</th>
                    <th>Centro Consumo</th>
                    <th>Seleccione nuevo Centro Consumo</th>
                
                </tr>
            </thead> 
            <tbody>
                <?php $count = 0; ?>
                @if(isset($detallesTC))
                    @foreach($detallesTC as $detalleTC)
                
                        <tr class="text-center">
                            <td>
                                <input class="invisible" name="idcc[]" value="{{ $detalleTC->transaccionCompra->transaccion_id }}" />
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="item{{$count}}"  name="contador[]"  value="{{ $count }}" > 
                                    <label for="item{{$count}}">
                                    </label>
                                </div>
                            </td>
                            <td>{{$detalleTC->transaccionCompra->transaccion_fecha}}</td> 
                            <td><a href="{{ url("transaccioncompra/{$detalleTC->transaccionCompra->transaccion_id}/ver")}}" target="_blank">{{$detalleTC->transaccionCompra->transaccion_numero}}</a></td>
                            <td>
                            {{$detalleTC->transaccionCompra->proveedor->proveedor_nombre}}
                            </td>                            
                            <td>{{$detalleTC->producto->producto_nombre}}</td>
                            <td>{{$detalleTC->transaccionCompra->sustentoTributario->sustento_codigo.' - '.$detalleTC->transaccionCompra->sustentoTributario->sustento_nombre}}</td>                                
                            <td>{{$detalleTC->centroConsumo->centro_consumo_nombre}}</td>
                            <td>
                                <select class="form-control select2" id="idCentroc[]" name="idCentroc[]" data-live-search="true">
                                    @foreach($centrosConsumo as $centroConsumo)
                                        @if($detalleTC->centroConsumo->centro_consumo_id == $centroConsumo->centro_consumo_id)
                                            <option value="{{$centroConsumo->centro_consumo_id}}" selected>                                
                                                {{$centroConsumo->centro_consumo_nombre}}
                                            </option> 
                                        @else
                                            <option value="{{$centroConsumo->centro_consumo_id}}">                                
                                                {{$centroConsumo->centro_consumo_nombre}}
                                            </option>
                                        @endif
                                    @endforeach                                  
                                </select>
                            </td>                               
                            
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
<script type="text/javascript">
    function cargarCentroCC(){
    var centroConsumoSustento = document.getElementById("idSustento");
    document.getElementById("idCentroc").value = centroConsumoSustento.options[centroConsumoSustento.selectedIndex].value;
    $("#idCentroc").val(centroConsumoSustento.options[centroConsumoSustento.selectedIndex].value).trigger('change');
}
function cargarCentroC() {
        $.ajax({
            url: '{{ url("cargarCentroConsumo/searchN") }}'+ '/' +document.getElementById("idSustento").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("idSustento").value
            },
            success: function(data) {
                document.getElementById("idCentroc").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                for (var i = 0; i < data.length; i++) {
                    console.log(data[i].centro_consumo_nombre);
                    document.getElementById("idCentroc").value = data[i].centro_consumo_nombre;
                }
            },
        });
    }
</script>
@endsection