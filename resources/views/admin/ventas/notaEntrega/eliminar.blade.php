
@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST" action="{{ route('notaentrega.destroy', [$nentrega->nt_id]) }}">
        @method('DELETE')
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Nota de Entrega</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                    <!--
                    <button type="button" onclick='window.location = "{{ url("notaentrega") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
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
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label ">
                            <label>NUMERO</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="nt_serie" name="nt_serie">{{$nentrega->nt_serie}}</label>             
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                                   
                                    <label class="form-control" id="guia_numero" name="guia_numero"  value="{{substr(str_repeat(0, 9). $nentrega->nt_numero , - 9)}}">{{substr(str_repeat(0, 9). $nentrega->nt_numero , - 9)}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label" style="padding-left: 55px;">
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label derecha-texto">
                            <label>TOTAL</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                               
                                <input type="text" id="idTotalnota" name="idTotalnota"
                                        class="form-control campo-total-global derecha-texto" placeholder="Total"
                                        disabled style="background-color: black" value="{{$nentrega->nt_total}}">  
                                </div>
                            </div>
                        </div>
                       
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label "
                            style="margin-bottom : 0px;">
                            <label>CLIENTE :</label>
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input id="clienteID" name="clienteID" type="hidden">
                                <label class="form-control" id="buscarCliente" name="buscarCliente"  value="{{$nentrega->cliente->cliente_nombre}}">{{$nentrega->cliente->cliente_nombre}}</label>     
                            </div>
                        </div>
                       
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Fecha :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="guia_fecha" name="guia_fecha"  value="{{$nentrega->nt_fecha}}">{{$nentrega->nt_fecha}}</label>   
                                </div>
                            </div>
                        </div>                                
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>RUC/CI :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                <label class="form-control"  value="{{$nentrega->cliente->cliente_cedula}}">{{$nentrega->cliente->cliente_cedula}}</label>           
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>TIPO :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                <label class="form-control"  >{{$nentrega->cliente->tipoCliente->tipo_cliente_nombre}}</label>           
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3  form-control-label  centrar-texto"
                            style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label>BODEGA</label>
                            </div>
                        </div>           
                    </div>                  
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>DIRECCION :</label>
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control"  >{{$nentrega->cliente->cliente_direccion}}</label>        
                                </div>
                            </div>
                        </div>  
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3  form-control-label  centrar-texto"
                            style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control">{{$nentrega->bodega->bodega_nombre}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>TELEFONO :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control"  >{{$nentrega->cliente->cliente_telefono}}</label>
                                </div>
                            </div>
                        </div> 
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>PAGO :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control"  >{{$nentrega->nt_tipo_pago}}</label>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                            <div class="table-responsive">
                                <table id="cargarItemFactura"
                                    class="table table-striped table-hover boder-sar tabla-item-factura"
                                    style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                            <th>Cantidad</th>
                                            <th>Codigo</th>
                                            <th>Producto</th>    
                                            <th>P.U.</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($nentrega->detalle))
                                        @foreach($nentrega->detalle as $x)
                                        <tr>
                                            <td class="text-center">{{ $x->detalle_cantidad}}</td>
                                            <td class="text-center">{{ $x->producto->producto_codigo}}</td>           
                                            <td class="text-center">{{ $x->producto->producto_nombre}}</td>
                                            <td class="text-center">{{ $x->detalle_precio_unitario}}</td>
                                            <td class="text-center">{{ $x->detalle_total}}</td>
                                        </tr>   
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <br>
                    <div class="row">
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
                                                            <label class="form-control"  >{{$nentrega->nt_comentario}}</label>
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
                                    <td id="subtotal" width="100" class="derecha-texto negrita">{{$nentrega->nt_total}}</td>
                                    
                                </tr>                      
                                <tr>
                                    <td class="letra-blanca fondo-azul-claro negrita">Total</td>
                                    <td id="total" class="derecha-texto negrita">{{$nentrega->nt_total}}</td>
                                    
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