@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal"  method="POST" action="{{ url("eliminacionComprantes") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Eliminacion de Compraobantes</h3>                                 
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>

                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Tipo Documentos:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="documento" name="documento" >  
                            <option id="Facturas" name="Facturas" value="Facturas">Facturas</option>
                            <option id="Guia Remsion" name="Guia Remsion" value="Guia Remsion">Guia Remsion</option>
                            <option id="Nota de Debito" name="Nota de Debito" value="Nota de Debito">Nota de Debito</option>
                            <option id="Nota de Credito" name="Nota de Credito" value="Nota de Credito">Nota de Credito</option>
                            <option id="Liquidacion Compra" name="Liquidacion Compra" value="Liquidacion Compra">Liquidacion Compra</option>
                       
                    </select>    
                </div>
                
            </div>
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Descripcion:</center></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="descripcion" name="descripcion"  value='' >
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
                <div class="col-sm-1">
                    <button type="submit"  class="btn btn-primary btn-sm" data-toggle="modal"><i class="fa fa-search"></i></button>
                </div>
            </div> 

            
            <hr>        
            <table id="example1" class="table table-bordered table-responsive table-hover sin-salto">
                @if(isset($guias))
                    <thead>              
                        <tr class="text-center neo-fondo-tabla">    
                            <th></th>                
                            <th>Numero</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Transportista</th>
                            <th>Partida</th>
                            <th>Destino</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                          
                    <tbody>  
                  
                        @foreach($guias as $x)
                        <tr class="text-center">                
                            <td>    
                                 @if ( $x->gr_estado !=2)  
                                    @if(!isset($x->documentoAnulado))   
                                        <a href="{{ url("guia/{$x->gr_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>             
                                    @endif 
                                @endif  
                            </td>                                                   
                            <td class="text-center">{{ $x->gr_numero}}</td>
                            <td class="text-center">{{ $x->gr_fecha}}</td>
                            <td class="text-center">{{ $x->cliente_nombre}}</td>
                            <td class="text-center">{{ $x->Transportista->transportista_nombre}}</td>
                            <td class="text-center">{{ $x->gr_punto_partida}}</td>
                            <td class="text-center">{{ $x->gr_punto_destino}}</td>
                            <td class="text-center">
                                    @if( $x->gr_estado ==0) Anulado @endif 
                                    @if( $x->gr_estado ==1) Activo @endif    
                                    @if( $x->gr_estado ==2) Facturado @endif             
                            </td>    
                      
                           
                        </tr>
                        @endforeach
                    </tbody>
                @endif                                                                   
                @if(isset($facturas)) 
                    <thead>              
                        <tr class="text-center neo-fondo-tabla">    
                            <th></th>                
                            <th>Numero</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Subtotal</th>
                            <th>Tarifa 0%</th>
                            <th>Tarifa 12%</th>
                            <th>Descuento</th>
                            <th>IVA</th> 
                            <th>Total</th> 
                        </tr>
                    </thead>
                          
                    <tbody> 
                        @foreach($facturas as $x)
                            <tr class="text-center">
                            <td>
                                    @if(!isset($x->documentoAnulado))   
                                        @if(!isset($x->retencion))   
                                            @if(count($x->notaDebito)==0)   
                                                @if(count($x->notacredito)==0)   
                                                    @if($x->factura_tipo_pago=='EN EFECTIVO')
                                                        <a href="{{ url("factura/{$x->factura_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>     
                                                    @else
                                                        @if(($x->cuentaCobrar->cuenta_monto) == $x->cuentaCobrar->cuenta_saldo)
                                                            <a href="{{ url("factura/{$x->factura_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>     
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif
                                        @endif    
                                    @endif
                            </td>
                                <td>{{ $x->factura_fecha}}</td>              
                                <td>{{ $x->factura_numero}}</td>                        
                                <td>{{ $x->cliente_nombre}}</td>
                                <td> <?php echo '$' . number_format($x->factura_subtotal, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->factura_tarifa0, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->factura_tarifa12, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->factura_descuento, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->factura_porcentaje_iva, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->factura_total, 2)?> </td>                            
                            </tr>
                        @endforeach
                   
                    </tbody>
                @endif
                @if(isset($debito)) 
                    <thead>              
                        <tr class="text-center neo-fondo-tabla">    
                            <th></th>               
                            <th>Numero</th>
                            <th>Fecha</th>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Subtotal</th>
                            <th>Tarifa 0%</th>
                            <th>Tarifa 12%</th>
                            <th>Descuento</th>
                            <th>IVA</th> 
                            <th>Total</th> 
                        </tr>
                    </thead>
                          
                    <tbody> 
                        @foreach($debito as $x)
                            <tr class="text-center">
                            <td>
                                    @if(!isset($x->documentoAnulado))   
                                        @if(!isset($x->retencion))   
                                            @if($x->nd_tipo_pago=='EN EFECTIVO')
                                                <a href="{{ url("notaDebito/{$x->nd_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>     
                                            @else
                                                @if(($x->cuentaCobrar->cuenta_monto) == $x->cuentaCobrar->cuenta_saldo)
                                                    <a href="{{ url("notaDebito/{$x->nd_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>     
                                                @endif
                                            @endif  
                                        @endif    
                                    @endif
                            </td>
                                <td>{{ $x->nd_numero}}</td>  
                                <td>{{ $x->nd_fecha}}</td>              
                                <td>{{ $x->factura->factura_numero}}</td>                        
                                <td>{{ $x->factura->cliente->cliente_nombre}}</td>
                                <td> <?php echo '$' . number_format($x->nd_subtotal, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->nd_tarifa0, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->nd_tarifa12, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->nd_descuento, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->nd_porcentaje_iva, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->nd_total, 2)?> </td>                                  
                            </tr>
                        @endforeach
                   
                    </tbody>
                @endif
                @if(isset($credito)) 
                    <thead>              
                        <tr class="text-center neo-fondo-tabla">    
                            <th></th>                
                            <th>Numero</th>
                            <th>Fecha</th>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Subtotal</th>
                            <th>Tarifa 0%</th>
                            <th>Tarifa 12%</th>
                            <th>Descuento</th>
                            <th>IVA</th> 
                            <th>Total</th> 
                            
                        </tr>
                    </thead>
                          
                    <tbody> 
                        @for ($i = 1; $i <= count($credito); ++$i)  
                            <tr class="text-center">
                            <td>
                                @if($credito[$i]['eliminar']=='1')
                                    <a href="{{ url("notaCredito/{$credito[$i]['nc_id']}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>     
                                @endif
                            </td>
                                <td>{{ $credito[$i]['nc_numero']}}</td>  
                                <td>{{ $credito[$i]['nc_fecha']}}</td> 
                                <td>{{ $credito[$i]['factura_numero']}}</td>            
                                <td>{{ $credito[$i]['cliente_nombre']}}</td>                        
                                <td> <?php echo '$' . number_format($credito[$i]['nc_subtotal'], 2)?> </td>
                                <td> <?php echo '$' . number_format($credito[$i]['nc_tarifa0'], 2)?> </td>
                                <td> <?php echo '$' . number_format($credito[$i]['nc_tarifa12'], 2)?> </td>
                                <td> <?php echo '$' . number_format($credito[$i]['nc_descuento'], 2)?> </td>
                                <td> <?php echo '$' . number_format($credito[$i]['nc_porcentaje_iva'], 2)?> </td>
                                <td> <?php echo '$' . number_format($credito[$i]['nc_total'], 2)?> </td>
                               
                           
                            </tr>
                        @endfor
                   
                    </tbody>
                @endif
                @if(isset($liquidacion)) 
                    <thead>              
                        <tr class="text-center neo-fondo-tabla">    
                            <th></th>                
                            <th>Numero</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Subtotal</th>
                            <th>Tarifa 0%</th>
                            <th>Tarifa 12%</th>
                            <th>Descuento</th>
                            <th>IVA</th> 
                            <th>Total</th>  
                        </tr>
                    </thead>
                          
                    <tbody> 
                        @foreach($liquidacion as $x)

                            <tr class="text-center">
                               
                                <td>
                                    @if(!isset($x->documentoAnulado)) 
                                        @if(($x->cuentaPagar->cuenta_monto) == $x->cuentaPagar->cuenta_saldo)  
                                            <a href="{{ url("liquidacioncompra/{$x->lc_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>     
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $x->lc_numero}}</td>  
                                <td>{{ $x->lc_fecha}}</td>                 
                                <td>{{ $x->proveedor->proveedor_nombre}}</td>
                                <td> <?php echo '$' . number_format($x->lc_subtotal, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->lc_tarifa0, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->lc_tarifa12, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->lc_descuento, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->lc_porcentaje_iva, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->lc_total, 2)?> </td>   

                            </tr>
                          
                        @endforeach
                   
                    </tbody>
                @endif
            </table>        
        </div>
    </div>
</form>
<script>
      <?php
   if(isset($fecha_hasta)){  
        ?>
         document.getElementById("fecha_hasta").value='<?php echo($fecha_hasta); ?>';
         <?php
    }
    if (isset($fecha_desde)) {
        ?>
       document.getElementById("fecha_desde").value='<?php echo($fecha_desde); ?>';
    
    <?php
    }
    if(isset($descripcion)){  
        ?>
       document.getElementById("descripcion").value='<?php echo($descripcion); ?>';
        <?php
    }
    ?>
    <?php
    
    if(isset($documento)){  
        ?>
       document.getElementById("documento").value='<?php echo($documento); ?>';
        <?php
    }
    ?>
</script>

@endsection