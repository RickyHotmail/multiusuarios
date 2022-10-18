@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-primary ">
    <form method="POST" action="{{ url("roloperativoCM/cheque") }} ">
    @csrf
        <div class="row">
        <!-- Tabla de empelados -->
        
            <div class="col-sm-2">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Empleados</h3>
                    </div>
                    <div class="card-body">
                        <table id="tableBuscar" class="table table-hover table-responsive">
                            <thead class="invisible">
                                <tr class="text-center">
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                    <td>
                                        {{$rol->empleado->empleado_nombre}}
                                    </td>
                                </tr>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
                <!-- Tabla de detalles -->
                <div class="col-sm-10">
                    <br>
                    <div  class="row">  
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            
                            <div class="float-right">
                                <button id="guardarID" type="submit" class="btn btn-success btn-sm" ><i
                                            class="fa fa-save"></i><span> Guardar</span></button>
                                <!-- 
                                <button type="button" onclick='window.location = "{{ url("listaRolCM") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                                --> 
                                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                                    
                                <br>

                            </div>
                            
                            <br>
                        </div>  
                    </div> 
                    <div id="ulprueba" class="row">  
                        
                        <div  class="col-md-5">
                            <div class="card card-secondary"> 
                                <div class="card-header">
                                    <h3 class="card-title ">Sueldo</h3>
                                    <button type="button" id="add" name="add" onclick="cargar();" class="btn btn-default btn-sm float-right"><i
                                        class="fas fa-plus"></i>Cargar</button > 
                                    <input type="hidden" id="DiaN" name="DiaN" class="form-control "
                                                    value="0"  required>
                                        
                                </div> 
                                <div class="card-body" >    
                                                
                                    <div class="card-body table-responsive p-0" style="height: 280px;">
                                        <table  id="exampleingresos" class="table table-head-fixed text-nowrap">
                                            <thead>  
                                                <tr >
                                                <th  class="text-center-encabesado">Fecha desde</th>
                                                <th class="text-center-encabesado">Fecha hasta</th> 
                                                <th class="text-center-encabesado">Porcentaje</th>                       
                                                <th class="text-center-encabesado">Dias</th> 
                                                <th class="text-center-encabesado">Sueldo</th>   
                                                </tr>
                                            </thead>
                                            <tbody>      

                                            <td> <input type="date" id="fecha_desde" name="fecha_desde" class="form-control "
                                                        placeholder="Seleccione una fecha..."  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>'
                                                        onchange="Extraerdias();" onkeyup="Extraerdias();"  required /> </td>
                                                <td><input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control "
                                                        placeholder="Seleccione una fecha..."  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>'
                                                        onchange="Extraerdia30();" onkeyup="Extraerdia30();"  required>  
                                                </td>
                                                <td>   
                                                    <select class="form-controltext " id="idTipo" name="idTipo" onchange="porcentaje();" >
                                                            <option value="100">100</option>
                                                            <option value="50">50</option> 
                                                            <option value="25">25</option>
                                                            <option value="0">0</option>                                   
                                                        </select>
                                                        
                                                </td>
                                                <td> <input type="number" id="dias" name="dias" class="form-controltext "
                                                value="0" min="0" max="31" onclick="calculosueldo();" onkeyup="calculosueldo();" readonly required> </td> 
                                                <td> <input type="number" id="sueldo" name="sueldo" class="form-controltext"
                                                        value="0.00" min="0"  step="0.01"   required readonly> </td> 
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                        
                                </div>
                            </div>
                        </div>
                        <!-- Tabla de ingresos -->
                        <div  class="col-md-3">
                            <div class="card card-secondary"> 
                                <div class="card-header">
                                    <h3 class="card-title ">Ingresos</h3>
                                    <input type="hidden" id="DiaN" name="DiaN" class="form-control "
                                                    value="0"  required>
                                        
                                </div> 
                                <div class="card-body" >    
                                    @include ('admin.RHCostaMarket.rolOperativo.items')              
                                    <div class="card-body table-responsive p-0" style="height: 280px;">
                                        <table id="tablaingresos" class="table table-head-fixed text-nowrap">
                                            <tbody>    
                                                @if(isset($detalles))
                                                    @for ($i = 1; $i <= count($detalles); ++$i) 
                                                    @if($detalles[$i]['Tipo']=='2')
                                                    <tr class="editable">

                                                        <td >{{ $detalles[$i]['Descripcion'] }} </td>
                                                        <td >{{ number_format($detalles[$i]['Valor'],2) }} </td> 
                                                    </tr> 
                                                    @endif
                                                    @endfor
                                                @endif                       
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <div class="row clearfix form-horizontal">
                                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 form-control-label  "
                                            style="margin-bottom : 0px;">
                                            <label>Total Ingr.:</label>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <label id="Total_Inl" name="Total_Inl">{{ number_format($datos[1]['ingresos'],2) }}</label>
                                                    <input type="hidden" id="Total_In" name="Total_In" class="form-control "
                                                        value="0.00"   required>
                                                </div>
                                            </div>
                                        </div>          
                                    </div>  
                                </div>
                            </div>
                        </div>
                            
                        <!-- Tabla de egresos -->
                        <div  class="col-md-3">
                                
                                <div class="card card-secondary">  
                                    <div class="card-header">
                                        <h3 class="card-title ">Egresos
                                        </h3>
                                    </div>  
                                    <div class="card-body">
                                         
           
                                        @include ('admin.RHCostaMarket.rolOperativo.items')              
                                        <div class="card-body table-responsive p-0" style="height: 280px;">
                                            <table id="tablaegresos" class="table table-head-fixed text-nowrap">
                                                <tbody>                           
                                                    @if(isset($detalles))
                                                        @for ($i = 1; $i <= count($detalles); ++$i) 
                                                        @if($detalles[$i]['Tipo']=='1')
                                                        <tr class="editable">

                                                            <td >{{ $detalles[$i]['Descripcion'] }} </td>
                                                            <td >{{ number_format($detalles[$i]['Valor'],2) }} </td> 
                                                        </tr> 
                                                        @endif
                                                        @endfor
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                       
                                        
                                        
                                        <div class="row clearfix form-horizontal">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 form-control-label  "
                                                style="margin-bottom : 0px;">
                                                <label>Total Egr.:</label>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <label id="Total_Egl" name="Total_Egl" >{{ number_format($datos[1]['egresos'],2) }}</label>
                                                        <input type="hidden" id="Total_Eg" name="Total_Eg" class="form-control "
                                                        value="0.00"  required>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div> 
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            
                        <!-- Tabla de detalle -->
                            <div  class="col-md-12">  
                                <div class="card">       
                                    <div class="card-body table-responsive p-0" style="height: 200px;" > 
                                    @include ('admin.RHCostaMarket.rolIndividual.itemsindividual') 
                                        <table id="tabladetallle" class="table table-head-fixed text-nowrap">
                                            <thead>
                                                <tr >
                                                    <th  class="text-center-encabesado">Fecha inicio </th>
                                                    <th class="text-center-encabesado">Fecha fin </th>       
                                                    <th class="text-center-encabesado">Porcentaje </th>                        
                                                    <th class="text-center-encabesado">Dias</th>
                                                   
                                                    <th class="text-center-encabesado">Sueldo </th>
                                                    
                                                 
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($detalles))
                                                    @for ($i = 1; $i <= count($detalles); ++$i) 
                                                    @if($detalles[$i]['identificacion']=='sueldos')
                                                    <tr class="editable">
                                                        <td class="text-center-encabesado">{{ $detalles[$i]['fechaincio'] }} </td>
                                                        <td class="text-center-encabesado">{{ $detalles[$i]['fechafin'] }} </td>
                                                        <td class="text-center-encabesado">100 </td>
                                                        <td class="text-center-encabesado">30 </td>
                                                        <td class="text-center-encabesado">{{ number_format($detalles[$i]['Valor'],2) }} </td> 
                                                    </tr> 
                                                    @endif
                                                    @endfor
                                                @endif 
                                            </tbody>
                                        </table>
                                    </div> 
                                </div>   
                            </div>
                        <!-- Tabla de adelantos y forma de pagos -->
                        <div  class="col-md-9">
                                <!-- Tabla de adelantos -->
                                    <div class="card card-secondary">
                                        <div class="card-header">
                                            <h3 class="card-title">Anticipos y Prestamos</h3>
                                        </div>
                                    
                                        <div class="card-body table-responsive p-0" style="height: 150px;"> 
                                            @include ('admin.recursosHumanos.rolIndividual.itemsAnticipos')           
                                            <table id="tablaanticipos" class="table table-head-fixed text-nowrap">
                                                <thead>  
                                                    <tr >
                                                        <th  class="text-center-encabesado">Fecha </th>
                                                        <th class="text-center-encabesado">Anticipo</th>                        
                                                        <th class="text-center-encabesado">Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @if(isset($anticipo))
                                                    @for ($i = 1; $i <= count($anticipo); ++$i) 
                                                    <tr >
                                                        <td class="text-center-encabesado">{{$anticipo[$i]['descuento_fecha']}}
                                                        </td>
                                                        <td class="text-center-encabesado">{{number_format($anticipo[$i]['Valor_Anticipó'],2)}}
                                                        </td>
                                                        <td class="text-center-encabesado">{{number_format($anticipo[$i]['descuento_valor'],2)}}
                                                        </td>
                                                        
                                                    </tr>
                                                    @endfor
                                                @endif 
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="card card-secondary">
                                        <div class="card-header">
                                            <h3 class="card-title">Quincena</h3>
                                        </div>
                                    
                                        <div class="card-body table-responsive p-0" style="height: 150px;"> 
                                        @include ('admin.RHCostaMarket.rolOperativo.itemsquincena')      
                                            <table id="tablaquincena" class="table table-head-fixed text-nowrap">
                                                <thead>  
                                                    <tr >
                                                        <th  class="text-center-encabesado">Fecha </th>
                                                        <th class="text-center-encabesado">Quincena</th>                        
                                                        <th class="text-center-encabesado">Valor</th>
                                                           
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @if(isset($quincenas))
                                                    @for ($i = 1; $i <= count($quincenas); ++$i) 
                                                    <tr >
                                                        <td class="text-center-encabesado">{{$quincenas[$i]['descuento_fecha']}}
                                                        </td>
                                                        <td class="text-center-encabesado">{{number_format($quincenas[$i]['Valor_Anticipó'],2)}}
                                                        </td>
                                                        <td class="text-center-encabesado">{{number_format($quincenas[$i]['descuento_valor'],2)}}
                                                        </td>
                                                       
                                                    </tr>
                                                    @endfor
                                                @endif 
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card card-secondary">
                                        <div class="card-header">
                                            <h3 class="card-title">Comisariato</h3>
                                        </div>
                                    
                                        <div class="card-body table-responsive p-0" style="height: 150px;"> 
                                        @include ('admin.RHCostaMarket.rolIndividual.items')    
                                            <table id="tablacomida" class="table table-head-fixed text-nowrap">
                                                <thead>  
                                                    <tr >
                                                    <th  class="text-center-encabesado">Fecha </th>
                                                        <th class="text-center-encabesado">Valor</th>                        
                                                        <th class="text-center-encabesado">Factura</th>
                                                           
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @if(isset($alimentacion))
                                                    @for ($i = 1; $i <= count($alimentacion); ++$i) 
                                                    <tr >
                                                        <td class="text-center-encabesado">{{$alimentacion[$i]['fecha']}}
                                                        </td>
                                                        <td class="text-center-encabesado">{{number_format($alimentacion[$i]['valor'],2)}}
                                                        </td>
                                                        <td class="text-center-encabesado">{{$alimentacion[$i]['factura']}}
                                                        </td>
                                                    </tr>
                                                    @endfor
                                                @endif  
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <!-- Tabla de forma de pagos -->
                                    <div  class="card card-secondary">
                                        <div class="card-header">
                                            <h3 class="card-title">Forma de Pago</h3>
                                        </div>
                                    </div>
                                    
                                    <div class="card-header p-2">
                                            <ul id="ul_prueba" class="nav nav-pills">
                                            <li class="nav-item " ><a class="nav-link item active" href="#timeline" data-toggle="tab" >{{ $datos[1]['tipo'] }}</a></li>
            
                                            </ul>
                                           
                                        </div>
                                        <div class="card-body">
                                            <div class="tab-content">

                                               
                                            
                                                @if( $datos[1]['tipo'] =='Cheque')  
                                                <div class="tab-pane active" id="timeline"> 
                                                    <div class="form-group row">
                                                        <label for="banco_id_cheque" class="col-sm-3 col-form-label">Banco</label>
                                                        <div class="col-sm-9">
                                                            <label id="Total_Vacacionesl" name="Total_Vacacionesl">{{ $datos[1]['banco'] }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                            <label for="cuenta_id_cheque" class="col-sm-3 col-form-label">Cuenta</label>
                                                            <div class="col-sm-9">
                                                                <label id="Total_Vacacionesl" name="Total_Vacacionesl">{{ $datos[1]['numero'] }}</label>
                                                               
                                                            </div>
                                                    </div> 
                                                
                                                    <div class="form-group row">
                                                                <label for="idFechaCheque" class="col-sm-3 col-form-label">Fecha</label>
                                                                <div class="col-sm-9">
                                                                <input type="hidden"  id="fechactual" name="fechactual" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                                                                <input type="date" class="form-control" id="idFechaCheque" name="idFechaCheque" value="{{ $datos[1]['fecha'] }}" >
                                                                 
                                                                </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="idNcheque" class="col-sm-3 col-form-label">N° de Cheque Anterior</label>
                                                        <div class="col-sm-9">
                                                            <label id="Total_Vacacionesl" name="Total_Vacacionesl">{{ $datos[1]['cheque'] }}</label>
                                                        </div>
                                                    </div> 
                                                    <div class="form-group row">
                                                        <label for="idNewcheque" class="col-sm-3 col-form-label">Nuevo # de Cheque</label>
                                                        <div class="col-sm-9">
                                                            <input type="number" class="form-control" id="idNewcheque" name="idNewcheque" min="1">
                                                        </div>
                                                    </div>   
                                                </div>
                                                @endif
                                                @if( $datos[1]['tipo'] =='Transferencia')  
                                                <div class="tab-pane active" id="timeline"> 
                                                    <div class="form-group row">
                                                        <label for="banco_id_cheque" class="col-sm-3 col-form-label">Banco</label>
                                                        <div class="col-sm-9">
                                                            <label id="Total_Vacacionesl" name="Total_Vacacionesl">{{ $datos[1]['banco'] }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                            <label for="cuenta_id_cheque" class="col-sm-3 col-form-label">Cuenta</label>
                                                            <div class="col-sm-9">
                                                                <label id="Total_Vacacionesl" name="Total_Vacacionesl">{{ $datos[1]['numero'] }}</label>
                                                               
                                                            </div>
                                                    </div> 
                                                
                                                  
                                                </div>
                                                @endif
                                                @if( $datos[1]['tipo'] =='Efectivo')  
                                                <div class="tab-pane active" id="timeline"> 
                                                    <div class="form-group row">
                                                        <label for="banco_id_cheque" class="col-sm-3 col-form-label">Pago</label>
                                                        <div class="col-sm-9">
                                                            <label id="Total_Vacacionesl" name="Total_Vacacionesl">Efectivo</label>
                                                        </div>
                                                    </div>
                                                    
                                                
                                                  
                                                </div>
                                                @endif
                                                
                                            </div>    
                                        </div>
                                
                            </div>
                           
                        <!-- Tabla de Totaless -->
                            <div class="col-md-3">
                                <div class="card card-primary"> 
                                    
                                   
                                    <br>
                                    <table class="table table-totalVenta">
                                        <tr>
                                            <th  class="text-center-encabesado letra-blanca fondo-gris-oscuro negrita" colspan="2">Totales </th>
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita" width="90">Total Ingresos (+)
                                            </td>
                                            <td id="TotalIngresosV" name="TotalIngresosV" width="100" class="derecha-texto negrita">{{ number_format($datos[1]['ingresos'],2) }}
                                            </td>
                                                                                            
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Total Egresos (-)</td>
                                            <td id="TotalEgresos"  name="TotalEgresos" class="derecha-texto negrita">{{ number_format($datos[1]['egresos'],2) }}</td>
                                            <input type="hidden"   name="TEgresos"  id="TEgresos" value="0" required readonly>
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Ingresos - Egresos
                                            </td>
                                            <td id="TIngreEgreV" name="TIngreEgreV" class="derecha-texto negrita">{{ number_format($datos[1]['ingresos']-$datos[1]['egresos'],2) }}</td>
                                            <input type="hidden"   name="TIngreEgre"  id="TIngreEgre" value="0" required readonly>
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Fondos de Reserva (+)</td>
                                            <td id="TotalFondosV"  name="TotalFondosV" class="derecha-texto negrita">{{ number_format($datos[1]['fondos'],2) }}</td>
                                            <input type="hidden"   name="TFondo"  id="TFondo" value="0" required readonly> 
                                            <input type="hidden"   name="reservat"  id="reservat" value="" required readonly> 
                                            <input type="hidden"   name="reservatacu"  id="reservatacu" value="" required readonly> 
                                            <input type="hidden"   name="Fondoacumulado"  id="Fondoacumulado" value="0" required readonly> 
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Decimo Tercero (+)</td>
                                            <td id="TotalTerceroV"  name="TotalTerceroV" class="derecha-texto negrita">{{ number_format($datos[1]['tercero'],2) }}</td>
                                            <input type="hidden"   name="TTercero"  id="TTercero" value="0" required readonly>           
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Decimo Cuarto (+)</td>
                                            <td id="TotalCuartoV"  name="TotalCuartoV" class="derecha-texto negrita">{{ number_format($datos[1]['cuarto'],2) }}</td>
                                            <input type="hidden"   name="TCuarto"  id="TCuarto" value="0" required readonly>           
                                        </tr>
                                        
                                        
                                    
                                       
                                        <td class="letra-blanca fondo-gris-oscuro negrita">Viaticos (+)</td>
                                        <td id="Tquincena"  name="Tquincena" class="derecha-texto negrita">{{ number_format($datos[1]['viaticos'],2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Liquido a Pagar</td>
                                            <td id="LiquidacionTotal" name="LiquidacionTotal" class="derecha-texto negrita">{{ number_format($datos[1]['pago'],2) }}</td>
                
                                            <input type="hidden"   name="idcheque"  id="idcheque" value="{{ $datos[1]['idcheque'] }}" required readonly> 
                                            <input type="hidden"   name="iddetalle"  id="iddetalle" value="{{ $datos[1]['iddetalle'] }}" required readonly>       
                                            <input type="hidden"   name="descripcion"  id="descripcion" value="{{ $datos[1]['descripcion'] }}" required readonly> 
                                                         
                                        </tr>
                                    </table>
                                    <br>
                                    <table class="table table-totalVenta">
                                        <tr>
                                            <th  class="text-center-encabesado letra-blanca fondo-gris-oscuro negrita" colspan="2">Beneficios y Proviciones </th>
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita" width="90">A. Patronal:
                                            </td>
                                            <td id="Patronall" name="Patronall" width="100" class="derecha-texto negrita">{{ number_format($datos[1]['patronal'],2) }}
                                            </td>
                                            <input type="hidden"   name="Patronal"  id="Patronal" value="0" required readonly> 
                                                                       
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">D. Tercero:</td>
                                            <td id="Tercerol"  name="Tercerol" class="derecha-texto negrita">{{ number_format($datos[1]['terceroacu'] ,2)}}</td>
                                            <input type="hidden"   name="Tercero"  id="Tercero" value="0" required readonly> 
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">D. Cuarto:
                                            </td>
                                            <td id="Cuartol" name="Cuartol" class="derecha-texto negrita">{{ number_format($datos[1]['cuartoacu'],2) }}</td>
                                            <input type="hidden"   name="Cuarto"  id="Cuarto" value="0" required readonly>           
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">F. Res.:</td>
                                            <td id="Fondol"  name="Fondol" class="derecha-texto negrita">{{ number_format($datos[1]['fondosacu'],2) }}</td>
                                          
                                            <input type="hidden"   name="Fondo"  id="Fondo" value="0" required readonly>            
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">IECE/SECAP.:</td>
                                            <td id="IECEl"  name="IECEl" class="derecha-texto negrita">{{ number_format($datos[1]['secap'],2) }}</td>
                                            
                                            <input type="hidden"   name="IECE"  id="IECE" value="0" required readonly>                      
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Vacaciones.:</td>
                                            <td id="VACACIONESPL"  name="VACACIONESPL" class="derecha-texto negrita">{{ number_format($datos[1]['vacaciones'],2) }}</td>
                                            
                                            <input type="hidden"   name="VACACIONESP"  id="VACACIONESP" value="0" required readonly>                      
                                        </tr>
                                    </table>
                                    <br>
                                    <table class="table table-totalVenta">
                                        <tr>
                                            <th  class="text-center-encabesado letra-blanca fondo-gris-oscuro negrita" colspan="2">Datos Banco Empleado </th>
                                        </tr>
                                        <tr>
                                            
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Banco</td>
                                            <td id="bancoEmpleado" name="bancoEmpleado" class="derecha-texto negrita"> </td> 
                                            <input type="hidden"   name="VbancoEmpleado"  id="VbancoEmpleado" value="0" >                                           
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Cuenta</td>
                                            <td id="cuentaempleado" name="cuentaempleado" class="derecha-texto negrita"> </td> 
                                            <input type="hidden"   name="Vcuentaempleado"  id="Vcuentaempleado" value="0" >       
                                        </tr>
                                    </table>
                                </div>   
                            </div>
                    </div>
                </div>
        
        </div>
    </form>
</div>


@endsection