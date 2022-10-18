@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("excelProducto") }}"  enctype="multipart/form-data">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Cargar Excel con Productos</h3>
            <div class="float-right">
                @if(isset($datos))
                <button type="submit" id="guardar" name="guardar" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                @endif
                <!--   
                <button type="button" onclick='window.location = "{{ url("producto") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            @if(!isset($datos))
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
            @endif
            <table class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Codigo Barras</th>
                        <th>Tipo</th>
                        <th>Costo</th>
                        <th>Stock minimo</th>
                        <th>Stock maximo</th>
                        <th>Fecha Ingreso</th>
                        <th>Iva</th>
                        <th>Descuento</th>
                        <th>Serie</th>
                        <th>Compra Venta</th>
                        <th>Grupo</th>
                        <th>Categoria</th>
                        <th>Marca</th>
                        <th>Unidad Medida</th>
                        <th>Tamaño</th>
                        <th>Precio 1</th>
                        <th>Utilidad 1</th>
                        <th>Descuento 1</th>
                        <th>Cuenta Venta</th>
                        <th>Cuenta Inventario</th>
                        <th>Cuenta Gasto</th>
                        <th>Sucursal</th>
                    </tr>
                </thead> 
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)   
                        <tr>
                            <td>{{ $datos[$i]['cod'] }} <input type="hidden" name="idCod[]" value="{{ $datos[$i]['cod'] }}"/></td>
                            <td>{{ $datos[$i]['nom'] }} <input type="hidden" name="idNom[]" value="{{ $datos[$i]['nom'] }}"/></td>
                            <td>{{ $datos[$i]['bar'] }} <input type="hidden" name="idBar[]" value="{{ $datos[$i]['bar'] }}"/></td>
                            <td>{{ $datos[$i]['tip'] }} <input type="hidden" name="idTip[]" value="{{ $datos[$i]['tip'] }}"/></td>
                            <td>{{ $datos[$i]['cos'] }} <input type="hidden" name="idCos[]" value="{{ $datos[$i]['cos'] }}"/></td>
                            <td>{{ $datos[$i]['min'] }} <input type="hidden" name="idMin[]" value="{{ $datos[$i]['min'] }}"/></td>
                            <td>{{ $datos[$i]['max'] }} <input type="hidden" name="idMax[]" value="{{ $datos[$i]['max'] }}"/></td>
                            <td>{{ $datos[$i]['fec'] }} <input type="hidden" name="idFec[]" value="{{ $datos[$i]['fec'] }}"/></td>
                            <td>{{ $datos[$i]['iva'] }} <input type="hidden" name="idIva[]" value="{{ $datos[$i]['iva'] }}"/></td>
                            <td>{{ $datos[$i]['des'] }} <input type="hidden" name="idDes[]" value="{{ $datos[$i]['des'] }}"/></td>
                            <td>{{ $datos[$i]['ser'] }} <input type="hidden" name="idSer[]" value="{{ $datos[$i]['ser'] }}"/></td>
                            <td>{{ $datos[$i]['com'] }} <input type="hidden" name="idCom[]" value="{{ $datos[$i]['com'] }}"/></td>
                            <td>{{ $datos[$i]['gru'] }} <input type="hidden" name="idGru[]" value="{{ $datos[$i]['gru'] }}"/></td>
                            <td>{{ $datos[$i]['cat'] }} <input type="hidden" name="idCat[]" value="{{ $datos[$i]['cat'] }}"/></td>
                            <td>{{ $datos[$i]['mar'] }} <input type="hidden" name="idMar[]" value="{{ $datos[$i]['mar'] }}"/></td>
                            <td>{{ $datos[$i]['uni'] }} <input type="hidden" name="idUni[]" value="{{ $datos[$i]['uni'] }}"/></td>
                            <td>{{ $datos[$i]['tam'] }} <input type="hidden" name="idTam[]" value="{{ $datos[$i]['tam'] }}"/></td>
                            <td>{{ $datos[$i]['pre'] }} <input type="hidden" name="idPre[]" value="{{ $datos[$i]['pre'] }}"/></td>
                            <td>{{ $datos[$i]['uti'] }} <input type="hidden" name="idUti[]" value="{{ $datos[$i]['uti'] }}"/></td>
                            <td>{{ $datos[$i]['dec'] }} <input type="hidden" name="idDec[]" value="{{ $datos[$i]['dec'] }}"/></td>
                            <td>{{ $datos[$i]['cuentaventa'] }} <input type="hidden" name="idCuentaventa[]" value="{{ $datos[$i]['cuentaventa'] }}"/></td>
                            <td>{{ $datos[$i]['cuentainventario'] }} <input type="hidden" name="idCuentainventario[]" value="{{ $datos[$i]['cuentainventario'] }}"/></td>
                            <td>{{ $datos[$i]['cuentagasto'] }} <input type="hidden" name="idCuentagasto[]" value="{{ $datos[$i]['cuentagasto'] }}"/></td>
                            <td>{{ $datos[$i]['sucursal'] }} <input type="hidden" name="idSucursal[]" value="{{ $datos[$i]['sucursal'] }}"/></td>

                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </div>         
    </div>
</form>
@endsection