@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Parametrizar Casillero Producto.</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("productoCasillaTributaria") }} "> 
        @csrf
            <div class="form-group row">                
                <label for="idTipoProd" class="col-sm-1 col-form-label"><center>Tipo Producto:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="idTipoProd" name="idTipoProd" required>
                        <option value="0">--Seleccione una opcion--</option>
                        <option value="2" @if($cc =="2") selected @endif>SERVICIO</option>
                        <option value="1" @if($cc =="1") selected @endif>ARTICULO</option>
                    </select> 
                </div>   
                <label for="idCompraVenta" class="col-sm-1 col-form-label"><center>Tipo Compra/Venta:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="idCompraVenta" name="idCompraVenta" required>
                        <option value="0">--Seleccione una opcion--</option>                      
                        <option value="1">Compra</option>
                        <option value="2">Venta</option>
                        <option value="3">Compra/Venta</option>
                    </select> 
                </div>              
                    <label for="sucursal_id" class="col-sm-1 col-form-label">Sucursal</label>   
                    <div class="col-sm-2">                         
                            <select class="custom-select select2" id="sucursal_id" name="sucursal_id" require>
                                <option value="0">Todas</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                @endforeach
                            </select>
                </div>
                <div class="col-sm-1">
                    <center><button id="buscarID" name="buscarID" type="submit"  class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div> 
                <div class="col-sm-5">
                    @if(isset($productos))
                        <div class="col-sm-2">
                            <button id="guardarID" name="guardarID" type="submit" class="btn btn-success" ><i
                                        class="fa fa-save"></i><span> Guardar</span></button>
                        </div>
                    @endif
                </div>               
            </div>
        <hr>
        <table id="example4"  class="table table-bordered table-hover table-responsive sin-salto" >
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Codigo</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Seleccione un Casillero</th>
                </tr>
            </thead> 
            <tbody>
                <?php $count = 0; ?>
                @if(isset($productos))
                    @foreach($productos as $producto)
                
                        <tr class="text-center">
                            <td>
                                <input class="invisible" name="idcc[]" value="{{ $producto->producto_id}}" />
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="item{{$count}}"  name="contador[]"  value="{{ $count }}" > 
                                    <label for="item{{$count}}">
                                    </label>
                                </div>
                            </td>                                                      
                            <td>{{$producto->producto_codigo}}</td>
                            <td>{{$producto->producto_nombre}}</td>
                            @if($producto->producto_tipo == '1')                                
                                <td>ARTICULO</td>
                            @endif
                            @if($producto->producto_tipo == '2')                                
                                <td>SERVICIO</td>
                            @endif
                            <td>
                                <select class="form-control select2" id="idCasillero[]" name="idCasillero[]" data-live-search="true">
                                            <option value="" selected>                                
                                                ---Seleccione Opcion---
                                            </option> 
                                    @foreach($casilleros as $casillero)                                       
                                            <option value="{{$casillero->casillero_id}}" @if($casillero->casillero_id==$producto->casillero_id) selected @endif>                                
                                                {{$casillero->casillero_codigo.' - '.$casillero->casillero_tipo}}
                                            </option>                                                                                 
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