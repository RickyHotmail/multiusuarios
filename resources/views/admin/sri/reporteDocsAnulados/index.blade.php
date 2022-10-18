@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Reporte de Documentos Anulados</h3>                       
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("reporteDocsAnulados") }}">
        @csrf
            <div class="form-group row">
                <label for="idBanco" class="col-lg-1 col-md-1 col-form-label">Sucursal :</label>
                <div class="col-lg-5 col-md-5">
                    <select class="custom-select" id="sucursal_id" name="sucursal_id" onclick="cargarCuenta();"
                        required>
                        <option value="0" label>Todas</option>
                        @foreach($sucursales as $sucursal)
                        <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>
                
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                </div>
            </div>
            <div class="form-group row">
                <label for="tipo_documento" class="col-lg-1 col-md-1 col-form-label">Documento :</label>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                    <div class="form-group">
                        <select id="tipo_documento" name="tipo_documento"
                            class="form-control select2" data-live-search="true">
                            <option value="0">Todos</option>
                            <option value="1">Factura</option>
                            <option value="2">Nota de crédito</option>
                            <option value="3">Nota de débito</option>
                            <option value="4">Comprobante de Retención</option>
                            <option value="5">Liquidación de compra de Bienes o Prestación de servicios
                            </option>
                            <option value="6">Guías de Remisión</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-1">
                    <center><button onclick="girarGif()" type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>            
        </form>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">  
                    <th></th>    
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Serie</th>
                    <th>Secuencial</th>
                    <th>Autorización</th>  
                    <th>Total</th>
                    <th>Fecha Anul.</th>
                    <th>Motivo Anul.</th>
                    <th>Usuario Anul.</th>  
                    
                </tr>
            </thead>
            <tbody> 
                
                @if(isset($documentos)) 
                    @foreach($documentos as $documento)
                    <?php $bandera = false; ?>
                    @if($documento->facturaVenta) @if($documento->facturaVenta->rangoDocumento->puntoEmision->sucursal_id == $sucursalAux or $sucursalAux == '0') <?php $bandera = true; ?> @endif  @endif
                    @if($documento->notaCredito) @if($documento->notaCredito->rangoDocumento->puntoEmision->sucursal_id == $sucursalAux or $sucursalAux == '0') <?php $bandera = true; ?> @endif  @endif
                    @if($documento->notaDebito) @if($documento->notaDebito->rangoDocumento->puntoEmision->sucursal_id == $sucursalAux or $sucursalAux == '0') <?php $bandera = true; ?> @endif  @endif
                    @if($documento->retencion) @if($documento->retencion->rangoDocumento->puntoEmision->sucursal_id == $sucursalAux or $sucursalAux == '0') <?php $bandera = true; ?> @endif  @endif
                    @if($documento->liquidacion) @if($documento->liquidacion->rangoDocumento->puntoEmision->sucursal_id == $sucursalAux or $sucursalAux == '0') <?php $bandera = true; ?> @endif  @endif
                    @if($bandera)
                    <tr>
                        <td>
                            @if($documento->facturaVenta)<a href="{{ url("/documentosanulados/ver/{$documento->documento_anulado_id}") }}" style="color: #BF2929;" class=""  data-toggle="tooltip" data-placement="top" title="PDF" target="_blank"><i class="fas fa-2x fa-file-pdf " aria-hidden="true"></i></a> @endif
                            @if($documento->notaCredito)<a href="{{ url("/documentosanulados/ver/{$documento->documento_anulado_id}") }}" style="color: #BF2929;" class=""  data-toggle="tooltip" data-placement="top" title="PDF" target="_blank"><i class="fas fa-2x fa-file-pdf " aria-hidden="true"></i></a>   @endif
                            @if($documento->notaDebito)<a href="{{ url("/documentosanulados/ver/{$documento->documento_anulado_id}") }}" style="color: #BF2929;" class=""  data-toggle="tooltip" data-placement="top" title="PDF" target="_blank"><i class="fas fa-2x fa-file-pdf " aria-hidden="true"></i></a>   @endif
                            @if($documento->retencion)  <a href="{{ url("/documentosanulados/ver/{$documento->documento_anulado_id}") }}" style="color: #BF2929;" class=""  data-toggle="tooltip" data-placement="top" title="PDF" target="_blank"><i class="fas fa-2x fa-file-pdf " aria-hidden="true"></i></a>  @endif
                            @if($documento->liquidacion)<a href="{{ url("/documentosanulados/ver/{$documento->documento_anulado_id}") }}" style="color: #BF2929;" class=""  data-toggle="tooltip" data-placement="top" title="PDF" target="_blank"><i class="fas fa-2x fa-file-pdf " aria-hidden="true"></i></a>   @endif
                        </td>
                        <td class="text-rigth">
                            @if($documento->facturaVenta) FACTURA @endif
                            @if($documento->notaCredito) NOTA DE CRÉDITO  @endif
                            @if($documento->notaDebito) NOTA DE DÉBITO  @endif
                            @if($documento->retencion) COMPROBANTE DE RETENCIÓN  @endif
                            @if($documento->liquidacion) LIQUIDACIÓN DE COMPRA  @endif

                        </td>
                        <td class="text-center">
                            @if($documento->facturaVenta) {{ $documento->facturaVenta->factura_fecha }} @endif
                            @if($documento->notaCredito) {{ $documento->notaCredito->nc_fecha }} @endif
                            @if($documento->notaDebito) {{ $documento->notaDebito->nd_fecha }}  @endif
                            @if($documento->retencion)  {{ $documento->retencion->retencion_fecha }} @endif
                            @if($documento->liquidacion) {{ $documento->liquidacion->lc_fecha }}  @endif
                        </td>
                        <td class="text-center">
                            @if($documento->facturaVenta) {{ $documento->facturaVenta->factura_serie }} @endif
                            @if($documento->notaCredito) {{ $documento->notaCredito->nc_serie }} @endif
                            @if($documento->notaDebito) {{ $documento->notaDebito->nd_serie }}  @endif
                            @if($documento->retencion)  {{ $documento->retencion->retencion_serie }} @endif
                            @if($documento->liquidacion) {{ $documento->liquidacion->lc_serie }}  @endif
                        </td>
                        <td class="text-center">
                            @if($documento->facturaVenta) {{ substr(str_repeat(0, 9).$documento->facturaVenta->factura_secuencial, - 9) }} @endif
                            @if($documento->notaCredito) {{ substr(str_repeat(0, 9).$documento->notaCredito->nc_secuencial, - 9) }} @endif
                            @if($documento->notaDebito) {{ substr(str_repeat(0, 9).$documento->notaDebito->nd_secuencial, - 9) }} @endif
                            @if($documento->retencion)  {{ substr(str_repeat(0, 9).$documento->retencion->retencion_secuencial, - 9) }} @endif
                            @if($documento->liquidacion) {{ substr(str_repeat(0, 9).$documento->liquidacion->lc_secuencial, - 9) }} @endif
                        </td>
                        <td class="text-center">
                            @if($documento->facturaVenta) {{ $documento->facturaVenta->factura_autorizacion }} @endif
                            @if($documento->notaCredito) {{ $documento->notaCredito->nc_autorizacion  }} @endif
                            @if($documento->notaDebito) {{ $documento->notaDebito->nd_autorizacion  }}  @endif
                            @if($documento->retencion)  {{ $documento->retencion->retencion_autorizacion  }} @endif
                            @if($documento->liquidacion) {{ $documento->liquidacion->lc_autorizacion  }}  @endif
                        </td>
                        <td class="text-center">
                            @if($documento->facturaVenta) {{ number_format($documento->facturaVenta->factura_tota,2) }} @endif
                            @if($documento->notaCredito) {{ number_format($documento->notaCredito->nc_total,2)  }} @endif
                            @if($documento->notaDebito) {{ number_format($documento->notaDebito->nd_tota,2)  }}  @endif
                            @if($documento->retencion)  {{ number_format($documento->retencion->retencion_total,2)  }} @endif
                            @if($documento->liquidacion) {{ number_format($documento->liquidacion->lc_total,2)  }}  @endif
                        </td> 
                        
                        <td>{{ $documento->documento_anulado_fecha }}</td>
                        <td>{{ $documento->documento_anulado_motivo }}</td>
                        <td>{{ $documento->documento_anulado_motivo }}</td>
                       
                    </tr>      
                    @endIf                   
                    @endforeach
                @endif
            </tbody>
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

<script>
     <?php
    if(isset($fecha_hasta)){  
        ?>
         document.getElementById("idHasta").value='<?php echo($fecha_hasta); ?>';
         <?php
    }
    if(isset($fecha_desde)){  
        ?>
         document.getElementById("idDesde").value='<?php echo($fecha_desde); ?>';
         <?php
    }
    if(isset($tipo)){  
        ?>
         document.getElementById("tipo_documento").value='<?php echo($tipo); ?>';
         <?php
    }
    if(isset($idsucursal)){  
     ?>

      document.getElementById("sucursal_id").value='<?php echo($idsucursal); ?>';
          <?php
    }
        ?>
   
    
</script>
@endsection