@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Listado de Documentos Electronicos - <h style="color: #ffb86f;">Su firma electronica caduca {{ date('d/m/Y H:i:s', $caduca) }}<h></h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form onsubmit="girarGif()" class="form-horizontal" method="POST" action="{{ url("docsElectronicos") }}">
            @csrf
            <div class="post">    
             
                <h3 class="card-title">
                  <i class="fas fa-search"></i>
                 Busqueda 1
                </h3>
                    <div class="form-group row">
                        <label for="idDesde" class="col-sm-2 col-form-label">
                            <center>Desde :</center>
                        </label>
                        <div class="col-sm-2">
                            <input type="date" class="form-control" id="idDesde" name="idDesde" value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                        </div>
                        <label for="idHasta" class="col-sm-1 col-form-label">
                            <center>Hasta :</center>
                        </label>
                        <div class="col-sm-2">
                            <input type="date" class="form-control" id="idHasta" name="idHasta" value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                        </div>
                        <label for="nombre_sucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                        <div class="col-sm-4">
                        <select class="custom-select select2" id="sucursal" name="sucursal" >
                            <option value="--TODOS--" label>--TODOS--</option>                       
                            @foreach($sucursal as $sucursales)
                                <option id="{{$sucursales->sucursal_nombre}}" name="{{$sucursales->sucursal_nombre}}" value="{{$sucursales->sucursal_nombre}}" @if(isset($idsucursal)) @if($sucursales->sucursal_nombre==$idsucursal) selected @endif @endif>{{$sucursales->sucursal_nombre}}</option>
                            @endforeach
                        </select> 
                        </div>
                    </div>
                    <div class="form-group row">
                                                            
                        <label for="idHasta" class="col-sm-2 col-form-label">
                            <center>Tipo de Documento :</center>
                        </label>
                        <div class="col-sm-5">
                           
                                <select id="tipo_documento" name="tipo_documento" class="form-control select2"
                                    data-live-search="true">
                                    <option value="--TODOS--" label>--TODOS--</option>             
                                    <option value="1" @if(isset($docC)) @if($docC == '1') selected @endif @endif>Factura</option>
                                    <option value="2" @if(isset($docC)) @if($docC == '2') selected @endif @endif>Nota de crédito</option>
                                    <option value="3" @if(isset($docC)) @if($docC == '3') selected @endif @endif>Nota de débito</option>
                                    <option value="4" @if(isset($docC)) @if($docC == '4') selected @endif @endif>Comprobante de Retención</option>
                                    <option value="5" @if(isset($docC)) @if($docC == '5') selected @endif @endif>Liquidación de compra de Bienes o Prestación de servicios</option>
                                    <option value="6" @if(isset($docC)) @if($docC == '6') selected @endif @endif>Guías de Remisión</option>
                                </select>
                           
                        </div>
                        <div class="col-sm-1">
                            
                                <button type="submit" name="consultar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                <button type="submit" name="autorizar" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Autorización Masiva"><i class="fa fa-clipboard-check"></i></button>
                           
                        </div>
                    </div>
             
              <!-- /.card -->
            </div>
            <div class="post">    
             
                <h3 class="card-title">
                  <i class="fas fa-search"></i>
                 Busqueda 2
                </h3>
                <div class="form-group row">
                    <label for="busc" class="col-sm-2 col-form-label">
                        <center>Buscar :</center>
                    </label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="idNumeroDoc" name="idNumeroDoc" value="@if(isset($idNumeroDoc)) {{$idNumeroDoc}} @endif" placeholder="Ingrese aqui numero de documento">
                    </div>
                   
                    <div class="col-sm-1">
                        <center>
                            <button type="submit" name="consultarxnuemero" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </center>
                    </div>
                </div>

              <!-- /.card -->
            </div>
            
            <div class="card-body">   
            <?php $count = 1; ?>
            <table class="table table-bordered table-hover sin-salto">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th>No.</th>
                        <th><input type="checkbox" id="toggle" value="select" onClick="do_thisC()"/> </th>
                        <th>Documento</th>
                        <th>Fecha</th>
                        <th>No. Documento</th>
                        <th>Estado</th>
                        <th>Estado SRI</th>                       
                        <th></th>
                        <th>Clave de Acceso</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($facturas))
                        @foreach($facturas as $factura)
                            <tr>
                                <td>{{$count}}</td>
                                <td>@if($factura->factura_xml_estado != 'AUTORIZADO')<input type="checkbox" name="checkbox1[]" value="{{ $factura->factura_id}}">@endif </td>  
                                <td>FACTURA</td>
                                <td>{{ $factura->factura_fecha }}</td>
                                <td>{{ $factura->factura_numero }}</td>
                                <td> <center>
                                    @if($factura->factura_estado=="1")
                                        <i class="fa fa-lg fa-check-circle neo-verde"></i>
                                        @else
                                        <i class="fa fa-lg fa-times-circle neo-rojo"></i>
                                    @endif</center>
                                </td>
                                <td>{{ $factura->factura_xml_estado}}</td>
                                <td>
                                    @if($factura->factura_emision == 'ELECTRONICA')
                                        <?php $fecha = date('d-m-Y', strtotime($factura->factura_fecha)); ?>
                                        <a href="{{ url("respuesSRIFac/{$factura->factura_id}") }}" style="color: #6c757d;" class=""  data-toggle="tooltip" data-placement="top" title="Respuesta SRI" target="_blank"><i class="fas fa-2x fa-file " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        <a href="{{ url("documentosElectronicos/{$factura->empresa->empresa_ruc}/{$fecha}/{$factura->factura_xml_nombre}.xml") }}" style="color: #49A42C;" class=""  data-toggle="tooltip" data-placement="top" title="XML" target="_blank"><i class="fas fa-2x fa-file-alt " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        @if($factura->factura_xml_estado == 'AUTORIZADO')
                                            <a href="{{ url("PdfFactura/{$factura->factura_id}") }}" style="color: #BF2929;" class=""  data-toggle="tooltip" data-placement="top" title="PDF" target="_blank"><i class="fas fa-2x fa-file-pdf " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                            @if($factura->factura_estado == 1)
                                            <a href="{{ url("emailFactura/{$factura->factura_id}") }}" style="color: #1B6BB2;" class=""  data-toggle="tooltip" data-placement="top" title="Enviar por Email"><i class="fas fa-2x fa-file-export " aria-hidden="true"></i></a>
                                            @endif
                                        @elseif($factura->factura_estado == 1)
                                            <a href="{{ url("autorizarFactura/{$factura->factura_id}") }}" style="color: #D6861A;" class=""  data-toggle="tooltip" data-placement="top" title="Autorizar"><i class="fas fa-2x fa-file-import " aria-hidden="true"></i></a> 
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $factura->factura_autorizacion }}</td>
                            </tr>                            
                            <?php $count ++; ?>
                        @endforeach
                    @endif
                    @if(isset($ncs))
                        @foreach($ncs as $nc)
                            <tr>
                                <td>{{$count}}</td>
                                <td>@if($nc->nc_xml_estado != 'AUTORIZADO')<input type="checkbox" name="checkbox2[]" value="{{ $nc->nc_id}}"> @endif</td>
                                <td>NOTA DE CRÉDITO</td>
                                <td>{{ $nc->nc_fecha }}</td>
                                <td>{{ $nc->nc_numero }}</td>
                                <td> <center>
                                    @if($nc->nc_estado=="1")
                                        <i class="fa fa-lg fa-check-circle neo-verde"></i>
                                        @else
                                        <i class="fa fa-lg fa-times-circle neo-rojo"></i>
                                    @endif</center>
                                </td>
                                <td>{{ $nc->nc_xml_estado}}</td>
                                <td>
                                    @if($nc->nc_emision == 'ELECTRONICA')
                                        <?php $fecha = date('d-m-Y', strtotime($nc->nc_fecha)); ?>
                                        <a href="{{ url("respuesSRINC/{$nc->nc_id}") }}" style="color: #6c757d;" class=""  data-toggle="tooltip" data-placement="top" title="Respuesta SRI" target="_blank"><i class="fas fa-2x fa-file " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        <a href="{{ url("documentosElectronicos/{$nc->rangoDocumento->empresa->empresa_ruc}/{$fecha}/{$nc->nc_xml_nombre}.xml") }}" style="color: #49A42C;" class=""  data-toggle="tooltip" data-placement="top" title="XML" target="_blank"><i class="fas fa-2x fa-file-alt " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        @if($nc->nc_xml_estado == 'AUTORIZADO')                                        
                                        <a href="{{ url("documentosElectronicos/{$nc->rangoDocumento->empresa->empresa_ruc}/{$fecha}/{$nc->nc_xml_nombre}.pdf") }}" style="color: #BF2929;" class=""  data-toggle="tooltip" data-placement="top" title="PDF" target="_blank"><i class="fas fa-2x fa-file-pdf " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                            @if($nc->nc_estado == 1)
                                            <a href="{{ url("emailNC/{$nc->nc_id}") }}" style="color: #1B6BB2;" data-toggle="tooltip" data-placement="top" title="Enviar por Email"><i class="fas fa-2x fa-file-export " aria-hidden="true"></i></a>
                                            @endif
                                        @elseif($nc->nc_estado == 1)
                                        <a href="{{ url("autorizarNC/{$nc->nc_id}") }}" style="color: #D6861A;" data-toggle="tooltip" data-placement="top" title="Autorizar"><i class="fas fa-2x fa-file-import " aria-hidden="true"></i></a> 
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $nc->nc_autorizacion }}</td>
                            </tr>
                            <?php $count ++; ?>
                        @endforeach
                    @endif
                    @if(isset($nds))
                        @foreach($nds as $nd)
                            <tr>
                                <td>{{$count}}</td>
                                <td>@if($nd->nd_xml_estado != 'AUTORIZADO')<input type="checkbox" name="checkbox3[]" value="{{ $nd->nd_id}}"> @endif</td>
                                <td>NOTA DE DÉBITO</td>
                                <td>{{ $nd->nd_fecha }}</td>
                                <td>{{ $nd->nd_numero }}</td>
                                <td> <center>
                                    @if($nd->nd_estado=="1")
                                        <i class="fa fa-lg fa-check-circle neo-verde"></i>
                                        @else
                                        <i class="fa fa-lg fa-times-circle neo-rojo"></i>
                                    @endif</center>
                                </td>
                                <td>{{ $nd->nd_xml_estado}}</td>
                                <td>
                                    @if($nd->nd_emision == 'ELECTRONICA')
                                        <?php $fecha = date('d-m-Y', strtotime($nd->nd_fecha)); ?>
                                        <a href="{{ url("respuesSRIND/{$nd->nd_id}") }}" style="color: #6c757d;" class=""  data-toggle="tooltip" data-placement="top" title="Respuesta SRI" target="_blank"><i class="fas fa-2x fa-file " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        <a href="{{ url("documentosElectronicos/{$nd->rangoDocumento->empresa->empresa_ruc}/{$fecha}/{$nd->nd_xml_nombre}.xml") }}" style="color: #49A42C;" class=""  data-toggle="tooltip" data-placement="top" title="XML" target="_blank"><i class="fas fa-2x fa-file-alt " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        @if($nd->nd_xml_estado == 'AUTORIZADO')
                                        <a href="{{ url("documentosElectronicos/{$nd->rangoDocumento->empresa->empresa_ruc}/{$fecha}/{$nd->nd_xml_nombre}.pdf") }}" style="color: #BF2929;" class=""  data-toggle="tooltip" data-placement="top" title="PDF" target="_blank"><i class="fas fa-2x fa-file-pdf " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                            @if($nd->nd_estado == 1)
                                            <a href="{{ url("emailND/{$nd->nd_id}") }}" style="color: #1B6BB2;" class=""  data-toggle="tooltip" data-placement="top" title="Enviar por Email"><i class="fas fa-2x fa-file-export " aria-hidden="true"></i></a>
                                            @endif
                                        @elseif($nd->nd_estado == 1)
                                        <a href="{{ url("autorizarND/{$nd->nd_id}") }}" style="color: #D6861A;" class=""  data-toggle="tooltip" data-placement="top" title="Autorizar"><i class="fas fa-2x fa-file-import " aria-hidden="true"></i></a> 
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $nd->nd_autorizacion }}</td>
                            </tr>
                            <?php $count ++; ?>
                        @endforeach
                    @endif
                    @if(isset($rets))
                        @foreach($rets as $ret)
                            <tr>
                                <td>{{$count}}</td>
                                <td>@if($ret->retencion_xml_estado != 'AUTORIZADO')<input type="checkbox" name="checkbox4[]" value="{{ $ret->retencion_id}}"> @endif</td>
                                <td>COMPROBANTE DE RETENCIÓN</td>
                                <td>{{ $ret->retencion_fecha }}</td>
                                <td>{{ $ret->retencion_numero }}</td>
                                <td> <center>
                                    @if($ret->retencion_estado=="1")
                                        <i class="fa fa-lg fa-check-circle neo-verde"></i>
                                        @else
                                        <i class="fa fa-lg fa-times-circle neo-rojo"></i>
                                    @endif</center>
                                </td>
                                <td>{{ $ret->retencion_xml_estado}}</td>
                                <td>
                                    @if($ret->retencion_emision == 'ELECTRONICA')
                                        <?php $fecha = date('d-m-Y', strtotime($ret->retencion_fecha)); ?>
                                        <a href="{{ url("respuesSRIRet/{$ret->retencion_id}") }}" style="color: #6c757d;" class=""  data-toggle="tooltip" data-placement="top" title="Respuesta SRI" target="_blank"><i class="fas fa-2x fa-file " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        <a href="{{ url("documentosElectronicos/{$ret->rangoDocumento->empresa->empresa_ruc}/{$fecha}/{$ret->retencion_xml_nombre}.xml") }}" style="color: #49A42C;" class=""  data-toggle="tooltip" data-placement="top" title="XML" target="_blank"><i class="fas fa-2x fa-file-alt " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        @if($ret->retencion_xml_estado == 'AUTORIZADO')
                                        <a href="{{ url("documentosElectronicos/{$ret->rangoDocumento->empresa->empresa_ruc}/{$fecha}/{$ret->retencion_xml_nombre}.pdf") }}" style="color: #BF2929;" class=""  data-toggle="tooltip" data-placement="top" title="PDF" target="_blank"><i class="fas fa-2x fa-file-pdf " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                            @if($ret->retencion_estado == 1)
                                            <a href="{{ url("emailRet/{$ret->retencion_id}") }}" style="color: #1B6BB2;" class=""  data-toggle="tooltip" data-placement="top" title="Enviar por Email"><i class="fas fa-2x fa-file-export " aria-hidden="true"></i></a>
                                            @endif
                                        @elseif($ret->retencion_estado == 1)
                                        <a href="{{ url("autorizarRet/{$ret->retencion_id}") }}" style="color: #D6861A;" class=""  data-toggle="tooltip" data-placement="top" title="Autorizar"><i class="fas fa-2x fa-file-import " aria-hidden="true"></i></a> 
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $ret->retencion_autorizacion }}</td>
                            </tr>
                            <?php $count ++; ?>
                        @endforeach
                    @endif
                    @if(isset($lcs))
                        @foreach($lcs as $lc)
                            <tr>
                                <td>{{$count}}</td>
                                <td> @if($lc->lc_xml_estado != 'AUTORIZADO')<input type="checkbox" name="checkbox5[]" value="{{ $lc->lc_id}}">@endif </td>
                                <td>LIQUIDACIÓN DE COMPRA</td>
                                <td>{{ $lc->lc_fecha }}</td>
                                <td>{{ $lc->lc_numero }}</td>
                                <td> <center>
                                    @if($lc->lc_estado=="1")
                                        <i class="fa fa-lg fa-check-circle neo-verde"></i>
                                        @else
                                        <i class="fa fa-lg fa-times-circle neo-rojo"></i>
                                    @endif</center>
                                </td>
                                <td>{{ $lc->lc_xml_estado}}</td>
                                <td>
                                    @if($lc->lc_emision == 'ELECTRONICA')
                                        <?php $fecha = date('d-m-Y', strtotime($lc->lc_fecha)); ?>
                                        <a href="{{ url("respuesSRILC/{$lc->lc_id}") }}" style="color: #6c757d;" class=""  data-toggle="tooltip" data-placement="top" title="Respuesta SRI" target="_blank"><i class="fas fa-2x fa-file " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        <a href="{{ url("documentosElectronicos/{$lc->rangoDocumento->empresa->empresa_ruc}/{$fecha}/{$lc->lc_xml_nombre}.xml") }}"style="color: #49A42C;" class=""  data-toggle="tooltip" data-placement="top" title="XML" target="_blank"><i class="fas fa-2x fa-file-alt " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        @if($lc->lc_xml_estado == 'AUTORIZADO')
                                        <a href="{{ url("documentosElectronicos/{$lc->rangoDocumento->empresa->empresa_ruc}/{$fecha}/{$lc->lc_xml_nombre}.pdf") }}" style="color: #BF2929;" class=""  data-toggle="tooltip" data-placement="top" title="PDF" target="_blank"><i class="fas fa-2x fa-file-pdf " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                            @if($lc->lc_estado == 1)
                                            <a href="{{ url("emailLC/{$lc->lc_id}") }}" style="color: #1B6BB2;" class=""  data-toggle="tooltip" data-placement="top" title="Enviar por Email"><i class="fas fa-2x fa-file-export " aria-hidden="true"></i></a>
                                            @endif
                                        @elseif($lc->lc_estado == 1)
                                        <a href="{{ url("autorizarLC/{$lc->lc_id}") }}" style="color: #D6861A;" class=""  data-toggle="tooltip" data-placement="top" title="Autorizar"><i class="fas fa-2x fa-file-import " aria-hidden="true"></i></a> 
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $lc->lc_autorizacion }}</td>
                            </tr>
                            <?php $count ++; ?>
                        @endforeach
                    @endif
                    @if(isset($guias))
                        @foreach($guias as $guia)
                            <tr>
                                <td>{{$count}}</td>
                                <td>@if($guia->gr_xml_estado != 'AUTORIZADO')<input type="checkbox" name="checkbox6[]" value="{{ $guia->gr_id}}"> @endif</td>
                                <td>GUIA DE REMISIÓN</td>
                                <td>{{ $guia->gr_fecha }}</td>
                                <td>{{ $guia->gr_numero }}</td>
                                <td> <center>
                                    @if($guia->gr_estado=="0")
                                        <i class="fa fa-lg fa-times-circle neo-rojo"></i>
                                    @else
                                        <i class="fa fa-lg fa-check-circle neo-verde"></i>
                                    @endif</center>
                                </td>
                                <td>{{ $guia->gr_xml_estado}}</td>
                                <td>
                                    @if($guia->gr_emision == 'ELECTRONICA')
                                        <?php $fecha = date('d-m-Y', strtotime($guia->gr_fecha)); ?>
                                        <a href="{{ url("respuesSRIGR/{$guia->gr_id}") }}" style="color: #6c757d;" class=""  data-toggle="tooltip" data-placement="top" title="Respuesta SRI" target="_blank"><i class="fas fa-2x fa-file " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        <a href="{{ url("documentosElectronicos/{$guia->rangoDocumento->empresa->empresa_ruc}/{$fecha}/{$guia->gr_xml_nombre}.xml") }}" style="color: #49A42C;" class=""  data-toggle="tooltip" data-placement="top" title="XML" target="_blank"><i class="fas fa-2x fa-file-alt " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        @if($guia->gr_xml_estado == 'AUTORIZADO')
                                        <a href="{{ url("documentosElectronicos/{$guia->rangoDocumento->empresa->empresa_ruc}/{$fecha}/{$guia->gr_xml_nombre}.pdf") }}" style="color: #BF2929;" class=""  data-toggle="tooltip" data-placement="top" title="PDF" target="_blank"><i class="fas fa-2x fa-file-pdf " aria-hidden="true"></i></a>&nbsp;&nbsp;
                                            @if($guia->gr_estado != 0)
                                            <a href="{{ url("emailGR/{$guia->gr_id}") }}" style="color: #1B6BB2;" class=""  data-toggle="tooltip" data-placement="top" title="Enviar por Email"><i class="fas fa-2x fa-file-export " aria-hidden="true"></i></a>
                                            @endif
                                        @elseif($guia->gr_estado == 1)
                                        <a href="{{ url("autorizarGR/{$guia->gr_id}") }}" style="color: #D6861A;" class=""  data-toggle="tooltip" data-placement="top" title="Autorizar"><i class="fas fa-2x fa-file-import " aria-hidden="true"></i></a> 
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $guia->gr_autorizacion }}</td>
                            </tr>
                            <?php $count ++; ?>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    function do_thisC(){
        var checkboxes = document.getElementsByName('checkbox1[]');
        var button = document.getElementById('toggle');
        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
        }
        var checkboxes = document.getElementsByName('checkbox2[]');
        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
        }
        var checkboxes = document.getElementsByName('checkbox3[]');
        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
        }
        var checkboxes = document.getElementsByName('checkbox4[]');
        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
        }
        var checkboxes = document.getElementsByName('checkbox5[]');
        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
        }
        var checkboxes = document.getElementsByName('checkbox6[]');
        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
        }
        if(button.value == 'select'){
            button.value = 'deselect';
        }else{
            button.value = 'select';
        }
    }
</script>
<div id="div-gif" class="col-md-12 text-center" style="position: absolute;height: 300px; margin-top: 150px; display: none">
    <img src="{{ url('img/loading.gif') }}" width=90px height=90px style="align-items: center">
</div>
<script>
    function girarGif(){
        document.getElementById("div-gif").style.display="inline"
        console.log("girando")
    }
</script>
@endsection