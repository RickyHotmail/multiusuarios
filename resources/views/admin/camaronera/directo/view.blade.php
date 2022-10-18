@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal" ">
        @method('DELETE')
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Costo Directo</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                                <!--     
                                <a href="{{ url("egresoBodega") }}" class="btn btn-danger btn-sm not-active-neo"><i
                                class="fas fa-times-circle"></i><span> Cancelar</span></a>  
                                --> 
                                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Numero</label>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                            <div class="form-group">
                                <div class="form-line">
                                    
                                    <input id="egreso_id" name="egreso_id" value="{{$egreso->cabecera_egreso_id}}" type="hidden">   
                                    <label class="form-control" id="egreso_serie" name="egreso_serie">{{$egreso->cabecera_egreso_serie}}</label>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="egreso_numero" name="egreso_numero"  value="{{substr(str_repeat(0, 9). $egreso->cabecera_egreso_numero , - 9)}}">{{substr(str_repeat(0, 9). $egreso->cabecera_egreso_numero , - 9)}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Fecha :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                <label class="form-control" id="egreso_fecha" name="egreso_fecha"  value="{{$egreso->cabecera_egreso_fecha}}">{{$egreso->cabecera_egreso_fecha}}</label>
                                 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1  form-control-label  centrar-texto"
                            style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label>Bodega :</label>
                            </div>
                        </div>  
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 alinear-izquierda" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control"   value="{{$egreso->bodega->bodega_nombre}}">{{$egreso->bodega->bodega_nombre}}</label>  
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Destino :</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-bottom : 0px;">
                            <div class="form-group">
                                 <label class="form-control"  value="{{$egreso->cabecera_egreso_destino}}">{{$egreso->cabecera_egreso_destino}}</label>
                                
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Destinatario :</label>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control"   value="{{$egreso->cabecera_egreso_destinatario}}">{{$egreso->cabecera_egreso_destinatario}}</label>           
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Motivo :</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control"   value="{{$egreso->cabecera_egreso_motivo}}">{{$egreso->cabecera_egreso_motivo}}</label>  
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Movimiento:</label>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control"   value="{{$egreso->tipo->tipo_nombre}}">{{$egreso->tipo->tipo_nombre}}</label>  
                            </div>
                        </div>         
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                            <div class="table-responsive">
                                @include ('admin.inventario.egresoBodega.itemEgresoPresentar')
                                <table id="cargarItemegreso"
                                    class="table table-striped table-hover boder-sar tabla-item-orden"
                                    style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                            <th>Cantidad</th>
                                            <th>Codigo</th>
                                            <th>Producto</th>
                                            <th>Siembra</th>
                                            <th>P.U.</th>
                                            <th>Total</th> 
                                        </tr>
                                    </thead>
                                    <tbody id="plantillaItemEgreso">
                                        @foreach($egreso->detalles as $detalle)
                                        <tr id="row_{{$egreso->cabecera_egreso_id}}">
                                            <td>{{ $detalle->detalle_egreso_cantidad}}</td>  
                                            <td>{{ $detalle->producto->producto_codigo}}</td>  
                                            <td>{{ $detalle->producto->producto_nombre}}</td>  
                                            <td>{{ $detalle->movimiento->siembra->siembra_codigo}}</td>  
                                            <td>{{ $detalle->detalle_egreso_precio_unitario}}</td>
                                            <td>{{ $detalle->detalle_egreso_total}}</td>   
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row" >
                        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                            <div class="card card-primary card-outline card-tabs">
                                <div class="card-header p-0 pt-1 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist"
                                        style="border-bottom: 1px solid #c3c4c5;">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-four-otros-tab"
                                                data-toggle="pill" href="#custom-tabs-four-otros" role="tab"
                                                aria-controls="custom-tabs-four-otros"
                                                aria-selected="false"><b>Otros</b></a>
                                        </li>
                                      
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-four-otros"
                                            role="tabpanel" aria-labelledby="custom-tabs-four-otros-tab">
                                            <div class="row clearfix form-horizontal">
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                                    style="margin-bottom : 0px;">
                                                    <label>Comentario:</label>
                                                </div>
                                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10"
                                                    style="margin-bottom : 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <div class="form-control ">{{ $egreso->cabecera_egreso_comentario }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                     
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                            <table class="table table-totalVenta">
                                <tr>
                                    <td class="letra-blanca fondo-azul-claro negrita" width="90">Sub-Total
                                    </td>
                                    <td id="subtotal" width="100" class="derecha-texto negrita">{{ $egreso->cabecera_egreso_total }}</td>
                                    <input id="idSubtotal" name="idSubtotal" type="hidden" />
                                </tr>
                    
                               
                                <tr>
                                    <td class="letra-blanca fondo-azul-claro negrita">Total</td>
                                    <td id="total" class="derecha-texto negrita">{{ $egreso->cabecera_egreso_total }}</td>
                                    <input id="idTotal" name="idTotal" type="hidden" />
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- /.card -->


@endsection