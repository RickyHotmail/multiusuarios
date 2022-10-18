@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Venta de Activos Fijos</h3>
        <a href= "ventaActivo/create"><button class="btn btn-default btn-sm float-right"><i class="fa fa-plus"></i>&nbsp;Nuevo</button></a>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("ActivoVentaBuscar") }}">
        @csrf
            <div class="form-group row">
                <label for="idsucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="idsucursal" name="idsucursal" onchange="cargarActivo();">
                        <option value="" label>--Seleccione una Sucursal--</option>
                        @foreach($sucursales as $sucursal)                            
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($sucursalselect)) @if($sucursalselect == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                                                     
                        @endforeach
                    </select> 
                </div>              
                    <label for="idActivo" class="col-sm-1 col-form-label">Activo Fijo</label>
                    <div class="col-sm-4">
                        <select class="custom-select select2" id="idActivo" name="idActivo" require>
                            <option value="" label>--Seleccione un Activo Fijo--</option>
                            @if(isset($activosFijo))
                                @foreach($activosFijo as $activoFijo)                            
                                    <option value="{{$activoFijo->activo_id}}" @if(isset($activoselect)) @if($activoselect == $activoFijo->activo_id) selected @endif @endif>{{$activoFijo->producto_nombre.'-'.$activoFijo->activo_descripcion}}</option>
                                @endforeach 
                            @endif                                   
                        </select>
                    </div>                                              
                <div class="col-sm-1">
                    <center><button onclick="girarGif()" type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>                    
            </div>            
        </form>        
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">  
                    <th></th>
                    <th>Fecha</th>
                    <th>Descripcion</th>                  
                    <th>Activo Fijo</th>
                    <th>Monto</th>                                                              
                </tr>
            </thead>            
            <tbody>
            @if(isset($ventasActivo))
                @foreach($ventasActivo as $ventaActivo)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("ventaActivo/{$ventaActivo->venta_id}")}}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("ventaActivo/{$ventaActivo->venta_id}/eliminar")}}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $ventaActivo->venta_fecha}}</td>
                    <td>{{ $ventaActivo->venta_descripcion}}</td>
                    <td>{{ $ventaActivo->activoFijo->activo_descripcion}}</td>
                    <td>{{ number_format($ventaActivo->venta_monto, 2)}}</td>
                </tr>               
                @endforeach
            @endif
            </tbody>
            <tfoot>
            @if(isset($sumatoriaActivo))
                <tr>
                    <td colspan="4"><strong>Total en Ventas</strong></td>
                    <td class="text-right"><strong>{{number_format($sumatoriaActivo->venta_monto, 2)}}</strong></td>
                </tr>
            @endif
            </tfoot>
        </table>
    </div>
    <!-- /.card-body -->
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

<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nueva Venta Activo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="/ventaActivo">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idFecha" class="col-sm-3 col-form-label">Fecha</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idFecha" name="idFecha"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idSucursal" class="col-sm-3 col-form-label">Sucursal</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idSucursal" name="idSucursal" onchange="cargarActivoxSucursal();" required>
                                    <option value="" label>--Seleccione una Sucursal--</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idActivoFijo" class="col-sm-3 col-form-label">Activo Fijo</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idActivoFijo" name="idActivoFijo" require>
                                    <option value="" label>--Seleccione un Activo Fijo--</option>                                    
                                </select>
                            </div>
                        </div>                        
                            <div class="form-group row">
                                    <label for="idMonto" class="col-sm-3 col-form-label">Monto</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" id="idMonto" name="idMonto" value="0" required>
                                    </div>
                            </div>
                            <div class="form-group row">
                                <label for="idDescripcion" class="col-sm-3 col-form-label">Descripcion</label>
                                <div class="col-sm-9">                                    
                                    <textarea type="text" class="form-control" id="idDescripcion" name="idDescripcion" placeholder="Descripcion" required></textarea>
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
function cargarActivoxSucursal(){
    $.ajax({
        url: '{{ url("activoSucursal/searchN") }}'+ '/' +document.getElementById("idSucursal").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("idSucursal").value
        },
        success: function(data){
            document.getElementById("idActivoFijo").innerHTML = "<option value='' label>--Seleccione un Activo--</option>";
            for (var i=0; i<data.length; i++) {
                document.getElementById("idActivoFijo").innerHTML += "<option value='"+data[i].activo_id+"'>"+data[i].producto_nombre+"-"+data[i].activo_descripcion+"</option>";
            }           
        },
    });
}

function cargarActivo(){
    $.ajax({
        url: '{{ url("activoSucursal/searchN") }}'+ '/' +document.getElementById("idsucursal").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("idsucursal").value
        },
        success: function(data){
            $('#idActivo').val(null).trigger('change');
            document.getElementById("idActivo").innerHTML = "<option value='' label>--Seleccione un Activo--</option>";
            for (var i=0; i<data.length; i++) {
                document.getElementById("idActivo").innerHTML += "<option value='"+data[i].activo_id+"'>"+data[i].producto_nombre+"-"+data[i].activo_descripcion+"</option>";
            }                    
        },
    });
}
</script>
@endsection