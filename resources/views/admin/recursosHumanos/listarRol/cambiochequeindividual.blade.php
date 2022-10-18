@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-primary ">
    <form method="POST" action="{{ url("roloperativo/cheque") }} ">
    @csrf
        <div class="row">
        <!-- Tabla de empelados -->
        
            <div class="col-sm-2">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Empleado</h3>
                    </div>
                    <div class="card-body">
                        <table  class="table table-hover table-responsive">
                            <thead class="invisible">
                                <tr class="text-center">
                                    <tr></tr>
                                </tr>
                            </thead>
                            <tbody>
                            <tbody>
                                
                                <tr>
                                    <td>
                                        {{$rol->empleado->empleado_nombre}}
                                    </td>  
                                </tr>
                               
                            </tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
                <!-- Tabla de detalles -->
                <div class="col-sm-10">
                    <div  class="row">  
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            
                            <div class="float-right">

                                <button id="guardarID" type="submit" class="btn btn-success btn-sm" ><i
                                        class="fa fa-save"></i><span> Guardar</span></button>
                                <a href="{{ url("listaroles") }}" class="btn btn-danger btn-sm"><i
                                        class="fas fa-times-circle" ></i><span> Cancelar</span></a>
                                <br>

                            </div>
                            <br>
                        </div>  
                    </div> 
                    <div id="ulprueba" class="row">  
                        
                    
                              <div  class="col-md-12">  
                                <div class="card">       
                                    <div class="card-body table-responsive p-0" style="height: 200px;" > 
                                        <table id="tabla" class="table table-head-fixed text-nowrap">
                                            <thead>
                                                <tr >
                                                    <th  class="text-center-encabesado">Fecha inicio </th>
                                                    <th class="text-center-encabesado">Fecha fin </th>       
                                                    <th class="text-center-encabesado">Porcentaje </th>                        
                                                    <th class="text-center-encabesado">Dias</th>
                                                    <th class="text-center-encabesado">Sueldo </th>
                                                    <th class="text-center-encabesado">Vacaciones </th>
                                                    <th class="text-center-encabesado">Horas Extras </th>
            
                                                    <th class="text-center-encabesado">Transporte </th>              
                                                    <th class="text-center-encabesado">otros Bonificaciones </th>
                                                    <th class="text-center-encabesado">otros Ingresos </th>
                                                    <th class="text-center-encabesado">Total Ingresos </th>
                                                    <th class="text-center-encabesado">EXT. Salud</th>
                                                  
                                                    <th class="text-center-encabesado">PPQQ </th>
                                                    <th class="text-center-encabesado">Prestamos Hipotecarios</th>
                                                    <th class="text-center-encabesado">Prestamos </th>
                                                    <th class="text-center-encabesado">Multas</th>
                                                    <th class="text-center-encabesado">Otros Egresos</th>
                                                    <th class="text-center-encabesado">ley salud</th>
                                                    <th class="text-center-encabesado">Total Egresos </th>
                                                    <th class="text-center-encabesado">Ingresos - Egresos</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    @if(isset($datos[1]['fecha_inicio']))
                                                        @for ($i = 1; $i <= count($datos); ++$i) 
                                                        <tr >
                                                        <td class="text-center">  {{$datos[$i]['fecha_inicio']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['fecha_fin']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['porcentaje']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['dias']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['sueldo']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['vaca']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['extra']}}</td>
        
                                                        <td class="text-center">  {{$datos[$i]['transporte']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['otrasbonificaciones']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['otrosingre']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['ingresos']}}</td>

                                                        <td class="text-center">  {{$datos[$i]['salud']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['ppqq']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['hipoteca']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['prestamos']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['multas']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['otro_egre']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['salud']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['egresos']}}</td>
                                                        <td class="text-center">  {{$datos[$i]['ingresos']-$datos[$i]['egresos']}}</td>
                                                        </tr>
                                                        @endfor
                                                    @endif 
                                                </tr>
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
                                            <h3 class="card-title">Anticipos</h3>
                                        </div>
                                    
                                        <div class="card-body table-responsive p-0" style="height: 150px;"> 
                                            @include ('admin.recursosHumanos.rolIndividual.itemsAnticipos')           
                                            <table id="tablaanticipos" class="table table-head-fixed text-nowrap">
                                                <thead>  
                                                    <tr >
                                                        <th  class="text-center-encabesado"></th>
                                                        <th  class="text-center-encabesado">Fecha </th>
                                                        <th class="text-center-encabesado">Valor</th>                        
                                                        <th class="text-center-encabesado">Saldo</th>
                                                        <th class="text-center-encabesado">Descontar </th>
                                                        <th class="text-center-encabesado">Diario </th>    
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @if(isset($anticipo))
                                                    @for ($i = 1; $i <= count($anticipo); ++$i) 
                                                    <tr >
                                                        <td class="text-center-encabesado">{{$anticipo[$i]['descuento_fecha']}}
                                                        </td>
                                                        <td class="text-center-encabesado">{{$anticipo[$i]['descuento_valor']}}
                                                        </td>
                                                        <td class="text-center-encabesado">{{$anticipo[$i]['Valor_Anticipó']}}
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
                                            <li class="nav-item " onclick="Selection('Cheque')" ><a class="nav-link item active" href="#timeline" data-toggle="tab" >{{ $datos[1]['tipo'] }}</a></li>
            
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
                                                                    <label id="Total_Vacacionesl" name="Total_Vacacionesl">{{ $datos[1]['fecha'] }}</label>
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
                                                            <input type="number" class="form-control" id="idNewcheque" name="idNewcheque" >
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
                                                @if($datos[1]['tipo'] =='Efectivo')  
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
                                    <table class="table table-totalVenta">
                                        <tr>
                                            <th  class="text-center-encabesado letra-blanca fondo-gris-oscuro negrita" colspan="2">Totales </th>
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita" width="90">Total Ingresos
                                            </td>
                                            <td id="TotalIngresosV" name="TotalIngresosV" width="100" class="derecha-texto negrita">{{ $datos[1]['tingresos'] }}
                                           </td>
                                                      
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Total Egresos</td>
                                            <td id="TotalEgresos"  name="TotalEgresos" class="derecha-texto negrita">{{ $datos[1]['tegresos'] }}</td>
                                           
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Ingre. - Egre.
                                            </td>
                                            <td id="TIngreEgreV" name="TIngreEgreV" class="derecha-texto negrita">{{ $datos[1]['tingresos']-$datos[1]['tegresos'] }}</td>
                                            
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Fond. Reser.</td>
                                            <td id="TotalFondosV"  name="TotalFondosV" class="derecha-texto negrita">{{ $datos[1]['fondos'] }}</td>
                                           
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Decimo Tercero</td>
                                            <td id="TotalTerceroV"  name="TotalTerceroV" class="derecha-texto negrita">{{ $datos[1]['Tercero'] }}</td>
                                                      
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Decimo Cuarto</td>
                                            <td id="TotalCuartoV"  name="TotalCuartoV" class="derecha-texto negrita">{{ $datos[1]['Cuarto'] }}</td>
                                            
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita">Vac. Pagadas
                                            </td>
                                            <td id="LVac_pagadas"  name="LVac_pagadas" class="derecha-texto negrita">{{ $datos[1]['vaca_acu'] }}</td>
                                           
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita">Quincena
                                            </td>
                                            <td id="Tquincena"  name="Tquincena" class="derecha-texto negrita">{{ $datos[1]['quincena'] }}</td>
                                                    
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita">Total Anticipos
                                            </td>
                                            <td id="TotalAdelantosV"  name="TotalAdelantosV" class="derecha-texto negrita">{{ $datos[1]['anticipos'] }}</td>
                                            
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita">Total Alimentacion
                                            </td>
                                            <td id="TotalAlimentacionV"  name="TotalAlimentacionV" class="derecha-texto negrita">{{ $datos[1]['alimentacion'] }}</td>
                                           
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">IESS</td>
                                            <td id="TotalIess"  name="TotalIess" class="derecha-texto negrita">{{ $datos[1]['iess'] }}</td>
                                         
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">IESS Asumido</td>
                                            <td id="TotalAsumido"  name="TotalAsumido" class="derecha-texto negrita">{{ $datos[1]['iessasumidao'] }}</td>
                                           
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Imp. Renta</td>
                                            <td id="TRenta"  name="TRenta" class="derecha-texto negrita">{{ $datos[1]['renta'] }}</td>
                                           
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Liquido a Pagar</td>
                                            <td id="LiquidacionTotal" name="LiquidacionTotal" class="derecha-texto negrita">{{ $datos[1]['pago'] }}</td>
                                            <input type="hidden"   name="idcheque"  id="idcheque" value="{{ $datos[1]['idcheque'] }}" required readonly> 
                                            <input type="hidden"   name="iddetalle"  id="iddetalle" value="{{ $datos[1]['iddetalle'] }}" required readonly>       
                                            <input type="hidden"   name="descripcion"  id="descripcion" value="{{ $datos[1]['descripcion'] }}" required readonly>  
                                                      
                                        </tr>
                                    </table>
                                </div>   
                            </div>
                    </div>
                </div>
        
        </div>
    </form>
</div>

<script type="text/javascript">

</script>
@endsection