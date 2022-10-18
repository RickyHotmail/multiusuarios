@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header text-right">
        <h3 class="card-title">Stock de Producto para Orden °{{ $orden->orden_id }}</h3>
        <button onclick="history.back()" class="btn btn-default"><i class="fa fa-undo"></i>&nbsp;Atrás</button>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-redo"></i>&nbsp;Actualizar</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">  
                    <th></th>
                    <th>Producto</th>
                    <th>Cantidad Requerida</th>
                    <th>Cantidad en Stock</th>  
                    <th>Diferencia</th>
                    <th>Estado</th>
                </tr>
            </thead>            
            <tbody>
                <?php
                    $paso=true;

                    if(count($orden->detallesOrden)<=0) $paso=false;
                ?>
                @foreach($orden->detallesOrden as $det)
                <tr class="text-center">
                    <td></td>
                    <td>{{ $det->producto->producto_nombre }}</td>
                    <td>{{ $det->detalle_orden_cantidad }}</td>
                    <td>{{ $det->producto->producto_stock }}</td>
                    
                    @if($det->producto->producto_stock>=$det->detalle_orden_cantidad)
                        <td style="color: #00ff00">
                            +{{ $det->producto->producto_stock-$det->detalle_orden_cantidad }}
                        </td>
                        <td>
                            <i class="fa fa-check neo-verde"></i>
                        </td>
                    @else
                    <?php $paso=false ?>
                        <td style="color: #ff0000">
                            -{{ $det->detalle_orden_cantidad-$det->producto->producto_stock }}
                        </td>
                        <td>
                            <i class="fa fa-check neo-rojo"></i>
                        </td>
                    @endif
                </tr>
                @endforeach
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
                <h4 class="modal-title">Cambiar el Estado de Orden</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal p-5" method="POST" action="{{ url("actualizarEstadoMantenimiento") }}">
                @csrf

                <input type="hidden" name="orden_id" value="{{ $orden->orden_id }}">

                @if($paso)
                    <h3>La Orden °{{ $orden->orden_id }} cuenta con el stock Disponible, proceder a cambiar el estado a GENERADA</h3>
                    <br><br>
                    <div class="modal-footer text-end">
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div> 

                @else
                    <h3>No existe el stock disponible para dar paso esta Orden, compruebe las Cantidades de los productos</h3>
                @endif       
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
</div>
<!-- /.modal -->
@endsection