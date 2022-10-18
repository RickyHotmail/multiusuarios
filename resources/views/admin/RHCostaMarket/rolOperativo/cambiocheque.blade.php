@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-primary ">
    <form method="POST" action="{{ url("roloperativoCM/cheque") }} ">
    @csrf
        <div class="row">
        <!-- Tabla de empelados -->
            <div class="col-sm-2">
                <div class="card card-secondary " style="height: 700px;">
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
                <div class="card card-secondary">
                    <table  class="table table-head-fixed text-nowrap">
                        
                        <tbody >     
                            <tr>
                                <td class="filaDelgada20 negrita"> Sueldo Empelado                                 
                                </td>
                                <td class="filaDelgada20" id="Sueldo_Empelado" name="Sueldo_Empelado"> 0.00                                
                                </td>
           
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita"> Valor por Dia                                
                                </td>
                                <td class="filaDelgada20 " id="Valor_Dia" name="Valor_Dia"> 0.00                              
                                </td>
                   
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" > % I.E.S.S.                                 
                                </td>
                                <td class="filaDelgada20 " id="%IESS" name="%IESS"> 0.00                               
                                </td>
                    
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" > % I.E.S.S. Patronal                                
                                </td>
                                <td class="filaDelgada20 " id="%IESS_Pa" name="%IESS_Pa"> 0.00                               
                                </td>
                
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" > % F.RES.                                 
                                </td>
                                <td class="filaDelgada20 " id="%RES" name="%RES"> 0.00                               
                                </td>
                      
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" > % IECE/SECAP                               
                                </td>
                                <td class="filaDelgada20 " id="IECE/SECAP" name="IECE/SECAP"> 0.00                                 
                                </td>
    
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" > %Dia Extra                             
                                </td>
                                <td class="filaDelgada20 " id="Dia_Extra" name="Dia_Extra"> 0.00                                 
                                </td>
             
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" > Jornada                               
                                </td>
                                <td class="filaDelgada20 " id="Jornada" name="Jornada"> 0.00                                 
                                </td>
  
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" >Cosecha($)                               
                                </td>
                                <td class="filaDelgada20 " id="Cosecha($)" name="Cosecha($)"> 0.00                                 
                                </td>
    
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
                <!-- Tabla de detalles -->
                <div class="col-sm-10">
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
                        
                    
                        <!-- Tabla de ingresos -->
                        <div  class="col-md-2">
                            <br>
                            <div class="card card-secondary">  
                                <div class="card-header">
                                    <h3 class="card-title ">Dias
                                    </h3>
                                </div>  
                                <div class="card-body">    
                                 
                                    <div class="row clearfix form-horizontal">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 form-control-label  "
                                            style="margin-bottom : 0px;">
                                            <label>Normal:</label>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                                            <div class="form-group">
                                                <div class="form-line">

                                                    <label id="Normall" name="Normall">{{ $datos[1]['normal'] }}</label> 
                                                   
                                                </div>
                                            </div>
                                        </div>      
                                    </div>
                                    <div class="row clearfix form-horizontal">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 form-control-label  "
                                            style="margin-bottom : 0px;">
                                            <label>Descanso:</label>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                                            <div class="form-group">
                                                <div class="form-line">
                                                        <label id="Descansol" name="Descansol">{{ $datos[1]['decanso'] }}</label>
                                                      
                                                </div>
                                            </div>
                                        </div>  
                                    </div>
                                    <div class="row clearfix form-horizontal">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 form-control-label  "
                                            style="margin-bottom : 0px;">
                                            <label>Vacaciones:</label>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                                            <div class="form-group">
                                                <div class="form-line">
                                                        <label id="Vacacionesl" name="Vacacionesl">{{ $datos[1]['vacaciones'] }}</label>
                                                      
                                                </div>
                                            </div>
                                        </div>         
                                    </div>
                                    <div class="row clearfix form-horizontal">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 form-control-label  "
                                            style="margin-bottom : 0px;">
                                            <label>Permiso:</label>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                                            <div class="form-group">
                                                <div class="form-line">
                                                        <label id="Permisol" name="Permisol">{{ $datos[1]['permiso'] }}</label>
                                                      
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="row clearfix form-horizontal">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 form-control-label  "
                                            style="margin-bottom : 0px;">
                                            <label>Cosecha:</label>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                                            <div class="form-group">
                                                <div class="form-line">
                                                        <label id="Cosechal" name="Cosechal">{{ $datos[1]['cosecha'] }}</label>
                                                       
                                                </div>
                                            </div>
                                        </div> 
                                    </div> 
                                    <div class="row clearfix form-horizontal">    
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 form-control-label  "
                                            style="margin-bottom : 0px;">
                                            <label>Dias Extra:</label>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                                            <div class="form-group">
                                                <div class="form-line">
                                                        <label id="Extral" name="Extral">{{ $datos[1]['extra'] }}</label>
                                                        
                                                </div>
                                            </div>
                                        </div>                    
                                    </div>
                                    <div class="row clearfix form-horizontal">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 form-control-label  "
                                            style="margin-bottom : 0px;">
                                            <label>Ausente:</label>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                                            <div class="form-group">
                                                <div class="form-line">
                                                        <label id="Ausentel" name="Ausentel">{{ $datos[1]['ausente'] }}</label>
                                                        
                                                </div>
                                            </div>
                                        </div>   
                                    </div>
                                </div>
                            </div>
                        </div>
                            
                        <!-- Tabla de egresos -->
                            <div  class="col-md-3-5">
                                <br>
                                <div class="card card-secondary"> 
                                    <div class="card-header">
                                        <h3 class="card-title ">Ingresos</h3>
                                        <input type="hidden" id="DiaN" name="DiaN" class="form-control "
                                                        value="0"  required>
                                            
                                    </div> 
                                    <div class="card-body" >
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
                                    </div>    
                                    <div class="row clearfix form-horizontal">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 form-control-label"
                                            style="margin-bottom : 0px;">
                                            <label>Total Ingr.:</label>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <label id="Total_Inl" name="Total_Inl">{{ number_format($datos[1]['ingresos'] ,2)}}</label>
                                                    <input type="hidden" id="Total_In" name="Total_In" class="form-control "
                                                        value="0.00"   required>
                                                </div>
                                            </div>
                                        </div>          
                                    </div>  
                                </div>     
                            </div>
                            <div  class="col-md-3-5">
                            <br>
                                <div class="card card-secondary"> 
                                    <div class="card-header">
                                        <h3 class="card-title ">Egresos</h3>
                                        <input type="hidden" id="DiaN" name="DiaN" class="form-control "
                                                        value="0"  required>     
                                    </div> 
                                    <div class="card-body" >
                                        <div class="card-body table-responsive p-0" style="height: 280px;">
                                            <table id="tablaingresos" class="table table-head-fixed text-nowrap">
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
                                    </div>  
                                    <br>
                                    <div class="row clearfix form-horizontal">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 form-control-label"
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
                            
                            
                        <!-- Tabla de egresos -->
                            <div  class="col-md-3">
                                <br>
                                <div class="card card-secondary"> 
                                    <div class="card-header">
                                        <h3 class="card-title ">Beneficios y Proviciones</h3>
                                       
                                    </div> 
                                    <div class="card-body" >    
                                        <div class="row clearfix form-horizontal">
                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 form-control-label  "
                                                style="margin-bottom : 0px;">
                                                <label>A. Patronal:</label>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <label id="Patronall" name="Patronall" >{{ number_format($datos[1]['patronal'],2) }}</label>
                                                        
                                                        
                                                    </div>
                                                </div>
                                            </div> 
                                        
                                       
                                           
                                            
                                                
                                        </div>
                                        <div class="row clearfix form-horizontal">
                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5  form-control-label  "
                                                style="margin-bottom : 0px;">
                                                <label>D. Tercero:</label>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <label id="Tercerol" name="Tercerol" >{{ number_format($datos[1]['terceroacu'],2) }}</label>
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row clearfix form-horizontal">    
                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5  form-control-label  "
                                                style="margin-bottom : 0px;">
                                                <label>D. Cuarto:</label>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <label id="Cuartol" name="Cuartol" >{{ number_format($datos[1]['cuartoacu'],2) }}</label>
                                                        
                                                    </div>
                                                </div>
                                            </div> 
                                                      
                                        </div>  
                                        <div class="row clearfix form-horizontal">
                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5  form-control-label  "
                                                style="margin-bottom : 0px;">
                                                <label>F. Res.:</label>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <label id="Fondol" name="Fondol" >{{ number_format($datos[1]['fondosacu'] ,2)}}</label>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row clearfix form-horizontal">   
                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5  form-control-label  "
                                                style="margin-bottom : 0px;">
                                                <label>IECE/SECAP.:</label>
                                            </div>
                                            <div class="ccol-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <label id="IECEl" name="IECEl" >{{ number_format($datos[1]['secap'],2) }}</label>
                                                       
                                                    </div>
                                                </div>
                                            </div>         
                                        </div> 
                                        <div class="row clearfix form-horizontal">   
                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5  form-control-label  "
                                                style="margin-bottom : 0px;">
                                                <label>Vacaciones:</label>
                                            </div>
                                            <div class="ccol-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <label id="IECEl" name="IECEl" >{{ number_format($datos[1]['vacacion'],2) }}</label>
                                                       
                                                    </div>
                                                </div>
                                            </div>         
                                        </div> 
                                        
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
                                    
                                        <div class="card-body table-responsive p-0" style="height: 130px;"> 
                                                  
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
                                    
                                        <div class="card-body table-responsive p-0" style="height: 130px;"> 
                                               
                                            <table id="tablaanticipos" class="table table-head-fixed text-nowrap">
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
                                    
                                        <div class="card-body table-responsive p-0" style="height: 130px;"> 
                                       
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
                                            <li class="nav-item "  ><a class="nav-link item active" href="#timeline" data-toggle="tab" >{{ $datos[1]['tipo'] }}</a></li>
            
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
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Total Egresos (-)/td>
                                            <td id="TotalEgresos"  name="TotalEgresos" class="derecha-texto negrita">{{ number_format($datos[1]['egresos'],2) }}</td>
                                           
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Ingresos - Egresos
                                            </td>
                                            <td id="TIngreEgreV" name="TIngreEgreV" class="derecha-texto negrita">{{ number_format($datos[1]['ingresos']-$datos[1]['egresos'],2) }}</td>
                                            
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Fondos de Reserva</td>
                                            <td id="TotalFondosV"  name="TotalFondosV" class="derecha-texto negrita">{{ number_format($datos[1]['fondos'],2) }}</td>
                                                        
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Decimo Tercero</td>
                                            <td id="TotalTerceroV"  name="TotalTerceroV" class="derecha-texto negrita">{{ number_format($datos[1]['tercero'],2) }}</td>
                                            
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Decimo Cuarto</td>
                                            <td id="TotalCuartoV"  name="TotalCuartoV" class="derecha-texto negrita">{{ number_format($datos[1]['cuarto'],2) }}</td>
                                            
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita">Viaticos (+)
                                            </td>
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
                                </div>   
                            </div>
                    </div>
                </div>
        
        </div>
    </form>
</div>

<script type="text/javascript">

function cargarmetodo() {

    var idcabecera = <?php echo($datos[1]['empleado']); ?>;

    cargardatosempleados(idcabecera);
}

function cargardatosdias(id){ 
   
   
}
function cargardatosempleados(id){ 
    
    $.ajax({
        url: '{{ url("empleados/searchN") }}'+ '/' +id,
        dataType: "json",
        type: "GET",
        data: {
            ide: id
        },
        success: function(data){
           
            for (var i=0; i<data.length; i++) {
                document.getElementById("Sueldo_Empelado").innerHTML=data[i].empleado_sueldo;
               
                document.getElementById("Valor_Dia").innerHTML=(data[i].empleado_sueldo/30).toFixed(2);
              
        
                document.getElementById("%IESS_Pa").innerHTML=data[i].parametrizar_iess_patronal;
                

                document.getElementById("%RES").innerHTML=data[i].parametrizar_fondos_reserva;
               

                document.getElementById("IECE/SECAP").innerHTML=data[i].parametrizar_iece_secap;
              

                document.getElementById("Dia_Extra").innerHTML=data[i].parametrizar_porcentaje_he;
              
                document.getElementById("TPExtrasl").innerHTML=data[i].parametrizar_porcentaje_he;

                document.getElementById("Jornada").innerHTML=data[i].empleado_jornada;
               

                document.getElementById("Cosecha($)").innerHTML=data[i].empleado_cosecha;
            
                document.getElementById("TPCosecha").innerHTML= data[i].empleado_cosecha;
              
                
                document.getElementById("%IESS").innerHTML=data[i].parametrizar_iess_personal;
              
            
                
                
                
                
            }                  
        },
    });
}
function sumadias(dias){
    
   if(dias == 'T'){
       document.getElementById("Normall").innerHTML=Number( document.getElementById("Normall").innerHTML)+1;
     
      
   }
   if(dias == 'D'){
       document.getElementById("Descansol").innerHTML=Number( document.getElementById("Descansol").innerHTML)+1;
      
       
   }
   if(dias == 'V'){
       document.getElementById("Vacacionesl").innerHTML=Number( document.getElementById("Vacacionesl").innerHTML)+1;
     
      
   }
   if(dias == 'P'){
       document.getElementById("Permisol").innerHTML=Number( document.getElementById("Permisol").innerHTML)+1;
      
      
   }
   if(dias == 'A'){
       document.getElementById("Ausentel").innerHTML=Number( document.getElementById("Ausentel").innerHTML)+1;
    
       
   }
   if(dias == 'C'){
       document.getElementById("Cosechal").innerHTML=Number( document.getElementById("Cosechal").innerHTML)+1;
      
      

   }
   if(dias == 'X'){
       document.getElementById("Extral").innerHTML=Number( document.getElementById("Extral").innerHTML)+1;
   }

}
</script>
@endsection