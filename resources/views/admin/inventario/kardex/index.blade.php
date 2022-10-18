@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Kardex</h3>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("kardex") }}">
        @csrf
            <div class="form-group row">
                <label for="nombre_cuenta" class="col-sm-1 col-form-label"><center>Producto:</center></label>
                <div class="col-sm-5">
                    <select class="custom-select select2" id="productoID" name="productoID" require>
                        <option value="0">Todos</option>
                        @foreach($productos as $producto)
                            <option value="{{$producto->producto_id}}" @if(isset($productoC)) @if($productoC == $producto->producto_id) selected @endif @endif>{{$producto->producto_nombre}}</option>
                        @endforeach
                    </select>                    
                </div>
                <label for="nombre_cuenta" class="col-sm-1 col-form-label"><center>Categoria:</center></label>
                <div class="col-sm-3">
                    <select class="custom-select select2" id="categoriaID" name="categoriaID" require>
                        <option value="0">Todas</option>
                        @foreach($categorias as $categoria)
                            <option value="{{$categoria->categoria_id}}" @if(isset($categoriaC)) @if($categoriaC == $categoria->categoria_id) selected @endif @endif>{{$categoria->categoria_nombre}}</option>
                        @endforeach
                    </select>                    
                </div>
                <div class="col-sm-2 centrar-texto">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <button type="submit" id="excel" name="excel" class="btn btn-success"><i class="fas fa-file-excel   "></i></button>
                    <button type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                </div>
            </div>
            <div class="form-group row">
                <label for="nombre_cuenta" class="col-sm-1 col-form-label"><center>Bodega:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="bodegaID" name="bodegaID" require>
                        <option value="0">Todas</option>
                        @foreach($bodegas as $bodega)
                            <option value="{{$bodega->bodega_id}}" @if(isset($bodegaC)) @if($bodegaC == $bodega->bodega_id) selected @endif @endif>{{$bodega->bodega_nombre}}</option>
                        @endforeach
                    </select>                    
                </div>
                <div class="col-sm-5">
                    <div class="row">
                        <label for="fecha_desde" class="col-sm-2 col-form-label"><center>Desde :</center></label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fDesde)){echo $fDesde;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                        </div>
                        <label for="fecha_hasta" class="col-sm-2 col-form-label"><center>Hasta :</center></label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fHasta)){echo $fHasta;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2" style="border-radius: 5px; border: 1px solid #ccc9c9;padding-top: 7px;">
                    <div class=" row" >
                        <div class="col-sm-6">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="radioKardex1" name="radioKardex" value="1" @if(isset($tipo)) @if($tipo == 1) checked @endif @else checked @endif>
                                <label for="radioKardex1" class="custom-control-label">Resumen</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="radioKardex2" name="radioKardex" value="2" @if(isset($tipo)) @if($tipo == 2) checked @endif @endif>
                                <label for="radioKardex2" class="custom-control-label">Detalle</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="icheck-secondary">
                        <input type="checkbox" id="saldo_cero" name="saldo_cero" @if(isset($saldo_cero)) @if($saldo_cero == 1) checked @endif @endif>
                        <label for="saldo_cero" class="custom-checkbox"><center>Incliur saldo Cero</center></label>
                    </div>                    
                </div>
            </div>
            <hr>
            <table id="example5" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center">
                        @if(isset($tipo))
                            @if($tipo <> 1)
                            <th colspan="2"></th>
                            @else
                                <th></th>
                            @endif
                        @else   
                            <th colspan="2"></th>
                        @endif
                        <th class="fondo-naranja letra-blanca" colspan="3">ENTRADAS</th>
                        <th class="fondo-azul-claro letra-blanca" colspan="3">SALIDAS</th>
                        @if(isset($tipo))
                            @if($tipo <> 1)
                            <th colspan="7"></th>
                            @else
                            <th colspan="3"></th>
                            @endif
                        @else
                        <th colspan="7"></th>
                        @endif
                    </tr>
                    <tr class="text-center">
                        <th>Código</th>
                        <th>Producto</th>
                        @if(isset($tipo))
                            @if($tipo <> 1)
                            <th>Fecha</th>
                            @endif
                        @else   
                            <th>Fecha</th>
                        @endif
                        <th class="fondo-naranja-claro">Cantidad</th>
                        <th class="fondo-naranja-claro">Precio</th>
                        <th class="fondo-naranja-claro">Total</th>
                        <th class="fondo-celeste">Cantidad</th>
                        <th class="fondo-celeste">Precio</th>
                        <th class="fondo-celeste">Total</th>
                        <th class="fondo-verde-claro">Saldo</th>
                        @if(isset($tipo))
                            @if($tipo <> 1)
                                <th>Transaccion</th>
                                <th>Documento</th>
                                <th>Documento No.</th>
                                <th>Cliente/Proveedor</th> 
                                <th>Descripción</th> 
                                <th>Bodega</th> 
                            @else
                                <th>Costo Inv.</th>
                                <th>Utilidad</th>
                            @endif
                        @else
                            <th>Transaccion</th>
                            <th>Documento</th>
                            <th>Documento No.</th>
                            <th>Cliente/Proveedor</th> 
                            <th>Descripción</th> 
                            <th>Bodega</th> 
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)    
                        <tr class="invisible">
                            <td>
                            <input type="hidden" name="idCod[]" value="{{ $datos[$i]['cod'] }}"/>
                            <input type="hidden" name="idNom[]" value="{{ $datos[$i]['nom'] }}"/>
                            <input type="hidden" name="idFec[]" value="{{ $datos[$i]['fec'] }}"/>
                            <input type="hidden" name="idCan1[]" value="{{ $datos[$i]['can1'] }}"/>
                            <input type="hidden" name="idPre1[]" value="{{ $datos[$i]['pre1'] }}"/>
                            <input type="hidden" name="idTot1[]" value="{{ $datos[$i]['tot1'] }}"/>
                            <input type="hidden" name="idCan2[]" value="{{ $datos[$i]['can2'] }}"/>
                            <input type="hidden" name="idPre2[]" value="{{ $datos[$i]['pre2'] }}"/>
                            <input type="hidden" name="idTot2[]" value="{{ $datos[$i]['tot2'] }}"/>
                            <input type="hidden" name="idCan3[]" value="{{ $datos[$i]['can3'] }}"/>
                            <input type="hidden" name="idPre3[]" value="{{ $datos[$i]['pre3'] }}"/>
                            <input type="hidden" name="idTot3[]" value="{{ $datos[$i]['tot3'] }}"/>
                            <input type="hidden" name="idDoc[]" value="{{ $datos[$i]['doc'] }}"/>
                            <input type="hidden" name="idNum[]" value="{{ $datos[$i]['num'] }}"/>
                            <input type="hidden" name="idTra[]" value="{{ $datos[$i]['tra'] }}"/>
                            <input type="hidden" name="idRef[]" value="{{ $datos[$i]['ref'] }}"/>
                            <input type="hidden" name="idDia[]" value="{{ $datos[$i]['dia'] }}"/>
                            <input type="hidden" name="idCos[]" value="{{ $datos[$i]['cos'] }}"/>
                            <input type="hidden" name="idDes[]" value="{{ $datos[$i]['des'] }}"/>
                            <input type="hidden" name="idBod[]" value="{{ $datos[$i]['bod'] }}"/>
                            <input type="hidden" name="idCol[]" value="{{ $datos[$i]['col'] }}"/>
                            </td>
                        </tr>
                            @if($datos[$i]['col'] == "0")
                            <tr style="background: #6DC0CD;">
                                <td><b>{{ $datos[$i]['cod'] }}</b></td>
                                <td colspan="14"><b>{{ $datos[$i]['nom'] }}</b></td>
                            </tr>
                            @elseif($datos[$i]['col'] == "1")
                            <tr style="background: #EDC28B;" class="centrar-texto">
                                <td colspan="8">SALDO ANTERIOR</td>
                                <td>{{ $datos[$i]['can3'] }}</td>
                                <td colspan="6"></td>
                            </tr>
                            @elseif($datos[$i]['col'] == "3")
                            <tr>
                                <td>{{ $datos[$i]['cod'] }}</td>
                                <td>{{ $datos[$i]['nom'] }}</td>
                                <td class="centrar-texto">{{ $datos[$i]['can1'] }}</td>
                                <td class="centrar-texto">{{ number_format($datos[$i]['pre1'],2) }}</td>
                                <td class="centrar-texto">{{ number_format($datos[$i]['tot1'],2) }}</td>
                                <td class="centrar-texto">{{ $datos[$i]['can2'] }}</td>
                                <td class="centrar-texto">{{ number_format($datos[$i]['pre2'],2) }}</td>
                                <td class="centrar-texto">{{ number_format($datos[$i]['tot2'],2) }}</td>
                                <td class="centrar-texto fondo-verde-claro">{{ $datos[$i]['can3'] }}</td>
                                <td class="centrar-texto">{{ number_format($datos[$i]['pre3'],2) }}</td>
                                <td class="centrar-texto">{{ number_format($datos[$i]['tot3'],2) }}</td>
                            </tr>
                            @else
                            <tr class="centrar-texto">
                                <td>{{ $datos[$i]['cod'] }}</td>
                                <td>{{ $datos[$i]['nom'] }}</td>
                                <td>{{ $datos[$i]['fec'] }}</td>
                                <td>@if($datos[$i]['can1'] <> 0) {{ $datos[$i]['can1'] }} @endif</td>
                                <td>@if($datos[$i]['pre1'] <> 0) {{ number_format($datos[$i]['pre1'],2) }} @endif</td>
                                <td>@if($datos[$i]['tot1'] <> 0) {{ number_format($datos[$i]['tot1'],2) }} @endif</td>
                                <td>@if($datos[$i]['can2'] <> 0) {{ $datos[$i]['can2'] }} @endif</td>
                                <td>@if($datos[$i]['pre2'] <> 0) {{ number_format($datos[$i]['pre2'],2) }} @endif</td>
                                <td>@if($datos[$i]['tot2'] <> 0) {{ number_format($datos[$i]['tot2'],2) }} @endif</td>
                                <td class="fondo-verde-claro">{{ $datos[$i]['can3']}}</td>
                                <td>{{ $datos[$i]['tra'] }}</td>
                                <td>{{ $datos[$i]['doc'] }}</td>
                                <td>{{ $datos[$i]['num'] }}</td>
                                <td>{{ $datos[$i]['ref'] }}</td>
                                <td>{{ $datos[$i]['des'] }}</td>
                                <td>{{ $datos[$i]['bod'] }}</td>
                            </tr>
                            @endif
                        @endfor
                    @endif
                </tbody>
            </table>
        </form>
    </div>
</div>
@endsection