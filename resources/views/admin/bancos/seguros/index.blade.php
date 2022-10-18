@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Amortización de Seguros</h3>
        <div class="float-right">
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
        </div>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("seguroBuscar") }}">
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
                <label for="idestado" class="col-sm-1 col-form-label"><center>Estado:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="idestado" name="idestado">
                        <option value="0" label>--TODOS--</option>           
                        @foreach($estados as $estado)                            
                            <option value="{{$estado->amortizacion_estado}}" @if(isset($estadoselect)) @if($estadoselect == $estado->amortizacion_estado) selected @endif @endif>
                                @if($estado->amortizacion_estado == '1')
                                    Por Pagar
                                @else
                                    Cancelado
                                @endif
                            </option>                           
                        @endforeach
                    </select> 
                </div>                             
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>                    
            </div>            
        </form>        
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr>  
                    <th></th>
                    <th>Fecha</th>
                    <th>Factura</th>              
                    <th>Valor</th>
                    <th>Periodo Amortización</th>   
                    <th>Observacion</th>   
                    <th>Cuenta Debe</th>
                    <th>Cuenta Haber</th>
                    <th>Diario</th>   
                </tr>
            </thead>            
            <tbody>
            @if(isset($seguros))
                @foreach($seguros as $seguro)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("amortizacion/{$seguro->amortizacion_id}/editar") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("amortizacion/{$seguro->amortizacion_id}")}}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("amortizacion/{$seguro->amortizacion_id}/eliminar")}}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        <a href="{{ url("detalleamortizacion/{$seguro->amortizacion_id}/agregar") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Agregar Amortizacion"><i class="fa fa-plus" aria-hidden="true"></i></a>
                        <a href="{{ url("detalleamortizacion/{$seguro->amortizacion_id}") }}" class="btn btn-xs btn-secondary"  data-toggle="tooltip" data-placement="top" title="Lista de Amortizacion"><i class="fa fa-list" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $seguro->amortizacion_fecha}}</td>
                    <td>{{ $seguro->transaccionCompra->transaccion_numero}}</td>
                    <td>{{ $seguro->amortizacion_total}}</td>
                    <td>{{ $seguro->amortizacion_periodo}}</td>          
                    <td>{{ $seguro->amortizacion_observacion}}</td>         
                    <td>{{ $seguro->cuentadebe->cuenta_numero.' -  '.$seguro->cuentadebe->cuenta_nombre}}</td>               
                    <td>@foreach($seguro->transaccionCompra->detalles as $detalle)
                        {{$detalle->producto->cuentaGasto->cuenta_numero.' -  '.$detalle->producto->cuentaGasto->cuenta_nombre}}
                        @endforeach
                    </td>     
                    <td>@if(isset($seguro->transaccionCompra->diaro->diario_id))<a href="{{ url("asientoDiario/ver/{$seguro->transaccionCompra->diaro->diario_codigo}")}}" target="_blank">{{ $seguro->transaccionCompra->diaro->diario_codigo}}</a> @endif</td>
                </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Seguro</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("amortizacion") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idSucursal" class="col-sm-3 col-form-label">Sucursales</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idSucursal" name="idSucursal" required>
                                    <option value="" label>--Seleccione una Sucursal--</option>                                  
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="idProveedor" class="col-sm-3 col-form-label">Proveedor</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idProveedor" name="idProveedor" onchange="cargarFacturas();" required>
                                    <option value="" label>--Seleccione un Proveedor--</option>                                   
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{$proveedor->proveedor_id}}">{{$proveedor->proveedor_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                       
                        <div class="form-group row">
                            <label for="idFactura" class="col-sm-3 col-form-label">Factura</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idFactura" name="idFactura" onchange="cargarFechaFactura();" required>
                                    <option value="" label>--Seleccione una factura--</option>
                                </select>
                            </div>
                            
                        </div>
                        <div class="form-group row">
                                <label for="idValor" class="col-sm-3 col-form-label">Fecha</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idFecha" name="idFecha"  value="" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idValor" class="col-sm-3 col-form-label">Valor</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idValor" name="idValor" value="0" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idValor" class="col-sm-3 col-form-label">Periodo Amortización</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="periodo" name="periodo" value="0" required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idValor" class="col-sm-3 col-form-label">Cuenta Debe</label>
                                <div class="col-sm-9">
                                    <select class="custom-select select2" id="idCuenta" name="idCuenta" require>
                                        @foreach($cuentas as $cuenta)
                                            <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idDescripcion" class="col-sm-3 col-form-label">Observación</label>
                                <div class="col-sm-9">                                    
                                    <textarea type="text" class="form-control" id="idDescripcion" name="idDescripcion" placeholder="Descripcion" ></textarea>
                                </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>             
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script type="text/javascript">


function cargarFacturas(){    
    $.ajax({
        url: '{{ url("facturaCompra/searchN") }}'+ '/' +document.getElementById("idProveedor").value,
        dataType: "json",
        type: "GET",
        data3: {
            buscar: document.getElementById("idProveedor").value
        },        
        success: function(data3){
            document.getElementById("idFactura").innerHTML = "<option value='' label>--Seleccione una factura--</option>"; 
            for (var i=0; i<data3.length; i++) {
                document.getElementById("idFactura").innerHTML += "<option value='"+data3[i].transaccion_id+"'>"+data3[i].transaccion_numero+"</option>";
            }                       
        },
    });
    document.getElementById("idFecha").value = "";
}



function cargarFechaFactura(){    
    $.ajax({
        url: '{{ url("fechaDocumento/searchN") }}'+ '/' +document.getElementById("idFactura").value,
        dataType: "json",
        type: "GET",
        data3: {
            buscar: document.getElementById("idFactura").value
        },        
        success: function(data3){
            document.getElementById("idValor").value = Number(data3.transaccion_subtotal).toFixed(2);
            document.getElementById("idFecha").value = "";                                        
            document.getElementById("idFecha").value = data3.transaccion_fecha;
           /* $('#idDiario').val(data3.diario_id);
            $('#idDiario').trigger('change');*/

        },
    });
}



</script>
@endsection