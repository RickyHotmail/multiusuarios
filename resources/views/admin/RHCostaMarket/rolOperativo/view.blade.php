@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-primary ">
    <form method="POST" action="{{ url("operativorol")}}" onsubmit="return validar()"> 
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
                                @foreach($empleados as $empleado)
                                <tr>
                                    <td class="filaDelgada30"><div class="custom-control custom-radio"><input type="radio" class="custom-control-input" id="{{ $empleado->empleado_id}}"  name="radioempleado" value="{{ $empleado->empleado_id}}" ><label for="{{ $empleado->empleado_id}}" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">{{$empleado->empleado_nombre}}</label></div>                                
                                    </td>
                                </tr>
                                @endforeach
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
                                <input type="hidden"   name="VEmpelado"  id="VEmpelado" value="0" >
                                <input type="hidden"   name="sueldo_basico"  id="sueldo_basico" value="0" >
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita"> Valor por Dia                                
                                </td>
                                <td class="filaDelgada20 " id="Valor_Dia" name="Valor_Dia"> 0.00                              
                                </td>
                                <input type="hidden"   name="VDia" id="VDia" value="0" >
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" > % I.E.S.S.                                 
                                </td>
                                <td class="filaDelgada20 " id="%IESS" name="%IESS"> 0.00                               
                                </td>
                                <input type="hidden"   name="VIESS"  id="VIESS" value="0" > 
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" > % I.E.S.S. Patronal                                
                                </td>
                                <td class="filaDelgada20 " id="%IESS_Pa" name="%IESS_Pa"> 0.00                               
                                </td>
                                <input type="hidden"   name="VIESS_Pa"  id="VIESS_Pa" value="0">
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" > % F.RES.                                 
                                </td>
                                <td class="filaDelgada20 " id="%RES" name="%RES"> 0.00                               
                                </td>
                                <input type="hidden"   name="VRES"  id="VRES" value="0">
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" > % IECE/SECAP                               
                                </td>
                                <td class="filaDelgada20 " id="IECE/SECAP" name="IECE/SECAP"> 0.00                                 
                                </td>
                                <input type="hidden"   name="VIESCAP"  id="VIESCAP" value="0" >  
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" > %Dia Extra                             
                                </td>
                                <td class="filaDelgada20 " id="Dia_Extra" name="Dia_Extra"> 0.00                                 
                                </td>
                                <input type="hidden"   name="VDia_Extra"  id="VDia_Extra" value="0" >
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" > Jornada                               
                                </td>
                                <td class="filaDelgada20 " id="Jornada" name="Jornada"> 0.00                                 
                                </td>
                                <input type="hidden"   name="VJornada"  id="VJornada" value="0" >
                            </tr>
                            <tr>
                                <td class="filaDelgada20 negrita" >Cosecha($)                               
                                </td>
                                <td class="filaDelgada20 " id="Cosecha($)" name="Cosecha($)"> 0.00                                 
                                </td>
                                <input type="hidden"   name="VCosecha"  id="VCosecha" value="0" >
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
                <!-- Tabla de detalles -->
                <div class="col-sm-10">
                <br>
                    <div  class="row">  
                        
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                       
                            <div class="float-right">
                                <button type="button" id="nuevoID" onclick="nuevo()" class="btn btn-primary btn-sm"><i
                                        class="fas fa-receipt"></i><span> Nuevo</span></button>
                                <button id="guardarID" type="submit" class="btn btn-success btn-sm" disabled><i
                                        class="fa fa-save"></i><span> Guardar</span></button>
                                <button type="button" id="cancelarID" name="cancelarID" onclick="javascript:location.reload()"
                                    class="btn btn-danger btn-sm not-active-neo" disabled><i
                                        class="fas fa-times-circle"></i><span> Cancelar</span></button>
                                        <input type="hidden" id="rango" name="rango"  value="{{$rangoDocumento->rango_id}}">
                                        <input type="hidden" id="punto_id" name="punto_id"  value="{{$rangoDocumento->punto_id}}">

                            </div>
                           
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
                                                    <label id="Normall" name="Normall">0</label> 
                                                    <input type="hidden" id="Normal" name="Normal"  class="form-control "
                                                    value="0"  required>
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
                                                        <label id="Descansol" name="Descansol">0</label>
                                                       <input type="hidden" id="Descanso" name="Descanso"  class="form-control "
                                                       value="0"  required>
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
                                                        <label id="Vacacionesl" name="Vacacionesl">0</label>
                                                        <input type="hidden" id="Vacaciones" name="Vacaciones"  class="form-control "
                                                       value="0"  required>     
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
                                                        <label id="Permisol" name="Permisol">0</label>
                                                        <input type="hidden" id="Permiso" name="Permiso"  class="form-control "
                                                      value="0"  required>
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
                                                        <label id="Cosechal" name="Cosechal">0</label>
                                                        <input type="hidden" id="Cosecha" name="Cosecha"  class="form-control "
                                                      value="0"  required>
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
                                                        <label id="Extral" name="Extral">0</label>
                                                        <input type="hidden" id="Extra" name="Extra"  class="form-control "
                                                      value="0"  required>   
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
                                                        <label id="Ausentel" name="Ausentel">0</label>
                                                        <input type="hidden" id="Ausente" name="Ausente"  class="form-control "
                                                      value="0"  required>
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
                                        @include ('admin.RHCostaMarket.rolOperativo.items')              
                                        <div class="card-body table-responsive p-0" style="height: 280px;">
                                            <table id="tablaingresos" class="table table-head-fixed text-nowrap">
                                                <tbody>                           
                                                    
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
                                                        <label id="Total_Inl" name="Total_Inl">0.00</label>
                                                        <input type="hidden" id="Total_In" name="Total_In" class="form-control "
                                                            value="0.00"   required>
                                                    </div>
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
                                        <h3 class="card-title ">Egresos
                                        </h3>
                                    </div>  
                                    <div class="card-body">
                                         
           
                                        @include ('admin.RHCostaMarket.rolOperativo.items')              
                                        <div class="card-body table-responsive p-0" style="height: 280px;">
                                            <table id="tablaegresos" class="table table-head-fixed text-nowrap">
                                                <tbody>                           
                                                    
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
                                                        <label id="Total_Egl" name="Total_Egl" >0.00</label>
                                                        <input type="hidden" id="Total_Eg" name="Total_Eg" class="form-control "
                                                        value="0.00"  required>
                                                    </div>
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
                                        @include ('admin.RHCostaMarket.rolOperativo.items')              
                                        <div class="card-body table-responsive p-0" style="height: 280px;">
                                            <table id="tabla" class="table table-head-fixed text-nowrap">
                                                <tbody>                           
                                                    @foreach($rubros as $rubro)
                                                    @if($rubro->rubro_tipo=='3')
                                                    <tr  class="editable">
                                                       
                                                        <td >{{$rubro->rubro_descripcion}}</td>
                                                        <td id="{{$rubro->rubro_nombre}}"> 0.00
                                                        </td>
                                                        <input type="hidden" name="rolid[]" value="0"/>
                                                        <input type="hidden" name="idrubro[]" value="{{$rubro->rubro_id}}"/>
                                                        <input type="hidden" name="tiporubro[]" value="{{$rubro->rubro_tipo}}"/>
                                                        <input type="hidden" name="rubro[]"   value="{{$rubro->rubro_nombre}}"/>
                                                        <input type="hidden" name="valor[]" id="V{{$rubro->rubro_nombre}}" value="0"/>
                                                        <input type="hidden" name="prubro[]" id="p{{$rubro->rubro_nombre}}"  value="PAGADO"/>     
                                                    </tr>
                                                    @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
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
                                                        <th  class="text-center-encabesado"></th>
                                                        <th  class="text-center-encabesado">Fecha </th>
                                                        <th class="text-center-encabesado">Valor</th>                        
                                                        <th class="text-center-encabesado">Saldo</th>
                                                        <th class="text-center-encabesado">Descontar </th>
                                                        <th class="text-center-encabesado">Diario </th>    
                                                           
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                   
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card card-secondary">
                                        <div class="card-header">
                                            <h3 class="card-title">Comisariato</h3>
                                        </div>
                                    
                                        <div class="card-body table-responsive p-0" style="height: 150px;"> 
                                        @include ('admin.recursosHumanos.rolOperativo.items')    
                                            <table id="tablacomida" class="table table-head-fixed text-nowrap">
                                                <thead>  
                                                    <tr >
                                                        <th  class="text-center-encabesado"></th>
                                                        <th  class="text-center-encabesado"> Factura</th>
                                                        <th class="text-center-encabesado">Fecha</th>                        
                                                        <th >Valor</th>
                                                           
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                   
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
                                          
                                            <li class="nav-item " onclick="Selection('Cheque')" ><a class="nav-link item active" href="#timeline" data-toggle="tab" >Cheque</a></li>
                                            <li class="nav-item " onclick="Selection('Transferencia')" ><a class="nav-link item" href="#settings" data-toggle="tab" >Transferencia</a></li>
                                            </ul>
                                            <input type="hidden" id="tipo" name="tipo" class="form-controltext "
                                                            value="Cheque"  > 
                                        </div>
                                        <div class="card-body">
                                            <div class="tab-content">
                                            
                                                          
                                                <div class="tab-pane active" id="timeline"> 
                                                    <div class="form-group row">
                                                        <label for="banco_id_cheque" class="col-sm-3 col-form-label">Banco</label>
                                                        <div class="col-sm-9">
                                                            <select class="custom-select" id="banco_id_cheque" name="banco_id_cheque" onchange="cargarCuentacheque();" >
                                                                <option value="" label>--Seleccione una opcion--</option>
                                                            
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                            <label for="cuenta_id_cheque" class="col-sm-3 col-form-label">Cuenta</label>
                                                            <div class="col-sm-9">
                                                                <select class="custom-select" id="cuenta_id_cheque" name="cuenta_id_cheque"  onchange="cargarContablecheque();"  >
                                                                    
                                                                </select>
                                                                <input type="hidden" class="form-control" id="ncuenta_cheque" name="ncuenta_cheque" value="0">
                                                            </div>
                                                    </div> 
                                                    <div class="form-group row">
                                                            <label for="idCuentaContable_cheque" class="col-sm-3 col-form-label">Cuenta Contable</label>
                                                            <div class="col-sm-9">
                                                                <select class="custom-select" id="idCuentaContable_cheque"  name="idCuentaContable_cheque" disabled >
                                                                    <option value="--Seleccione una opcion--" label>--Seleccione una opcion--</option>                                   
                                                                </select>
                                                            </div>
                                                    </div> 
                                                    <div class="form-group row">
                                                                <label for="idFechaCheque" class="col-sm-3 col-form-label">Fecha</label>
                                                                <div class="col-sm-9">
                                                                    <input type="date" class="form-control" id="idFechaCheque" name="idFechaCheque" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                                                                </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="idNcheque" class="col-sm-3 col-form-label">Inicio # de Cheque</label>
                                                        <div class="col-sm-9">
                                                            <input type="number" class="form-control" id="idNcheque" name="idNcheque" >
                                                        </div>
                                                    </div>  
                                                </div>
                                                <div class="tab-pane" id="settings">
                                                    <div class="form-group row">
                                                        <label for="banco_id_transfer" class="col-sm-3 col-form-label">Banco</label>
                                                        <div class="col-sm-9">
                                                            <select class="custom-select" id="banco_id_transfer" name="banco_id_transfer" onchange="cargarCuentatransfer();"  >
                                                                <option value="" label>--Seleccione una opcion--</option>
                                                            
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="cuenta_id_transfer" class="col-sm-3 col-form-label">Cuenta</label>
                                                        <div class="col-sm-9">
                                                            
                                                            <select class="custom-select" id="cuenta_id_transfer" name="cuenta_id_transfer" onchange="cargarContabletransfer();" >
                                                                
                                                            </select>
                                                            <input type="hidden" class="form-control" id="ncuenta_transfer" name="ncuenta_transfer" value="0">
                                                        </div>
                                                    </div> 
                                                    <div class="form-group row">
                                                            <label for="idCuentaContable_transfer" class="col-sm-3 col-form-label">Cuenta Contable</label>
                                                            <div class="col-sm-9">
                                                                <select class="custom-select" id="idCuentaContable_transfer"  name="idCuentaContable_transfer" disabled >
                                                                    <option value="--Seleccione una opcion--" label>--Seleccione una opcion--</option>                                   
                                                                </select>
                                                            </div>
                                                    </div> 
                                                    <div class="form-group row">
                                                                <label for="idFechatrasnfer" class="col-sm-3 col-form-label">Fecha</label>
                                                                <div class="col-sm-9">
                                                                    <input type="date" class="form-control" id="idFechatrasnfer" name="idFechatrasnfer" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                                                                </div>
                                                    </div>
                                                </div>
                                            </div>    
                                        </div>
                                
                            </div>
                           
                            


                        
                            <div class="col-md-3">
                                <div class="card card-primary">  
                                    <table class="table table-totalVenta">
                                        <tr>
                                            <th  class="text-center-encabesado letra-blanca fondo-gris-oscuro negrita" colspan="2">Totales </th>
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita" width="50%" >Total Ingresos (+)
                                            </td>
                                            <td id="TotalIngresosV" name="TotalIngresosV" width="50%" class="derecha-texto negrita">0.00
                                            </td>
                                            <input type="hidden"   name="TIngresos"  id="TIngresos" value="0" required readonly>
                                            <input type="hidden"   name="idControldia"  id="idControldia" value="0" required readonly>
                                            <input type="hidden"   name="fecha"  id="fecha" value="0" required readonly> 
                                            <input type="hidden"   name="fechafinal"  id="fechafinal" value="0" required readonly> 
                                            
                                            <input type="hidden"   name="VTercero"  id="VTercero" value="0" required readonly> 
                                            <input type="hidden"   name="VCuarto"  id="VCuarto" value="0" required readonly>       
                                            <input type="hidden"   name="VFondo"  id="VFondo" value="0" required readonly>    
                                            <input type="hidden"   name="VTFondo"  id="VTFondo" value="0" required readonly>    
                                            <input type="hidden"   name="asumidot"  id="asumidot" value="0" required readonly>    
                                            <input type="hidden"   name="VAfiliado"  id="VAfiliado" value="0" required readonly>    
                                            <input type="hidden"   name="VInpuesto"  id="VInpuesto" value="0" required readonly> 
                                            <input type="hidden"   name="sueldo"  id="sueldo" value="0" required readonly>  
                                            <input type="hidden"   name="idempleado"  id="idempleado" value="0" required readonly>                                                 
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita" width="50%">Total Egresos (-)</td>
                                            <td id="TotalEgresos"  name="TotalEgresos" class="derecha-texto negrita" width="50%">0.00</td>
                                            <input type="hidden"   name="TEgresos"  id="TEgresos" value="0" required readonly>
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita" width="50%">Ingresos - Egresos
                                            </td>
                                            <td id="TIngreEgreV" name="TIngreEgreV" class="derecha-texto negrita" width="50%">0.00</td>
                                            <input type="hidden"   name="TIngreEgre"  id="TIngreEgre" value="0" required readonly>
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita" width="50%">Fondos de Reserva (+)</td>
                                            <td id="TotalFondosV"  name="TotalFondosV" class="derecha-texto negrita" width="50%">0.00</td>
                                            <input type="hidden"   name="TFondo"  id="TFondo" value="0" required readonly> 
                                            <input type="hidden"   name="reservat"  id="reservat" value="" required readonly> 
                                            <input type="hidden"   name="reservatacu"  id="reservatacu" value="" required readonly> 
                                            <input type="hidden"   name="Fondoacumulado"  id="Fondoacumulado" value="0" required readonly> 
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita" width="50%">Decimo Tercero (+)</td>
                                            <td id="TotalTerceroV"  name="TotalTerceroV" class="derecha-texto negrita" width="50%">0.00</td>
                                            <input type="hidden"   name="TTercero"  id="TTercero" value="0" required readonly>           
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita" width="50%">Decimo Cuarto (+)</td>
                                            <td id="TotalCuartoV"  name="TotalCuartoV" class="derecha-texto negrita" width="50%">0.00</td>
                                            <input type="hidden"   name="TCuarto"  id="TCuarto" value="0" required readonly>           
                                        </tr>
                                        
                                        
                                    
                                       
                                        
                                    </table>
                                    <table id="tablatotal" class="table table-totalVenta">
                                        @foreach($rubros as $rubro)
                                        @if($rubro->rubro_tipo=='4')
                                        <tr >
                                        <td class="letra-blanca fondo-gris-oscuro negrita" width="50%">{{$rubro->rubro_descripcion}} (+)</td>
                                           
                                            <td id="{{$rubro->rubro_nombre}}" width="50%"> <input type="number" id="V{{$rubro->rubro_nombre}}" name="valor[]" class="form-control"
                                                        value="0.00" onclick="sumatotales();" onkeyup="sumatotales();" step="any" required>
                                            </td>
                                            <input type="hidden" name="rolid[]" value="0"/>
                                            <input type="hidden" name="idrubro[]" value="{{$rubro->rubro_id}}"/>
                                            <input type="hidden" name="tiporubro[]" value="{{$rubro->rubro_tipo}}"/>
                                            <input type="hidden" name="rubro[]"   value="{{$rubro->rubro_nombre}}"/>
                                            <input type="hidden" name="prubro[]" id="p{{$rubro->rubro_nombre}}"  value="PAGADO"/>     
                                        
                                        </tr>
                                        @endif
                                        @endforeach
                                        
                                        
                                        </td>
                                        </tr>
                                        
                                    </table>  
                                    <table class="table table-totalVenta">
                                    <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita" width="50%">Liquido a Pagar</td>
                                            <td id="LiquidacionTotal" name="LiquidacionTotal" class="derecha-texto negrita" width="50%">0.00</td>
                                            <input type="hidden"   name="Liquidacion"  id="Liquidacion" value="0" required readonly> 
                                            
                                            <input type="hidden"   name="Totaldias"  id="Totaldias" value="0" required readonly>    
                                            <input type="hidden"   name="Totalsueldo"  id="Totalsueldo" value="0" required readonly>    
                                            <input type="hidden"   name="mes"  id="mes" value="0" required readonly>    
                                            <input type="hidden"   name="anio"  id="anio" value="0" required readonly>                 
                                        </tr>
                                    </table>  
                                    <br>
                                    <br>
                                    <table class="table table-totalVenta">
                                        <tr>
                                            <th  class="text-center-encabesado letra-blanca fondo-gris-oscuro negrita" colspan="2">Datos Banco Empleado </th>
                                        </tr>
                                        <tr>
                                            
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Banco</td>
                                            <td id="bancoEmpleado" name="bancoEmpleado" class="derecha-texto negrita"> </td> 
                                            <input type="hidden"   name="VbancoEmpleado"  id="VbancoEmpleado" value="0" > 
                                            <input type="hidden"   name="fechaactual"  id="fechaactual" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required readonly>                                           
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

<script type="text/javascript">
var id_item = 1;
function cargarmetodo() {
    $("#ulprueba").find("*").prop('disabled', true);
    cargarCuentaCaja();
    cargarCuentaParametrizada();
    cargarbanco();

}
function validar() {
    if(Number(document.getElementById("LiquidacionTotal").innerHTML)<0){
        alert('El total a pagar no debe ser menor a cero');
        return false
    }  
    document.getElementById("guardarID").value = "Enviando...";
	document.getElementById("guardarID").disabled = true; 
    return true;
}

function nuevo() { 
    if($('input:radio[name=radioempleado]:checked').val()== undefined){
        alert('Selecione un empelado');
    }
    else{
    document.getElementById("guardarID").disabled = false;
    document.getElementById("cancelarID").disabled = false;
    document.getElementById("nuevoID").disabled = true;
    $("#tableBuscar").find("*").prop('disabled', true); 
    $("#ulprueba").find("*").prop('disabled', false); 
    cargardatosdias($('input:radio[name=radioempleado]:checked').val());
  
    cargardatosempleados($('input:radio[name=radioempleado]:checked').val());
    cargardatosempleadosbanco($('input:radio[name=radioempleado]:checked').val());
    ExtraerQuincena($('input:radio[name=radioempleado]:checked').val());
    cargarAnticipos($('input:radio[name=radioempleado]:checked').val());
    cargarAliemtacion($('input:radio[name=radioempleado]:checked').val());
  //  ExtraerVacaciones($('input:radio[name=radioempleado]:checked').val());
   // 
   // 
    document.getElementById("idempleado").value=$('input:radio[name=radioempleado]:checked').val();
    }   
}

function ExtraerQuincena(id) {
    $.ajax({
        url: '{{ url("buscarquincena/searchN") }}'+ '/' +id,
        dataType: "json",
        async:false,
        type: "GET",
        data: {
            ide: id
        },
        success: function(data){
            var counta=1;
            for (var i=0; i<data.length; i++) {
                var linea = $("#plantillaItemQuincena").html();
                    linea = linea.replace(/{ID}/g, counta);
                    linea = linea.replace(/{IDE}/g, data[i].quincena_id);
                    linea = linea.replace(/{Fecha}/g, data[i].quincena_fecha );
                    linea = linea.replace(/{Valor}/g, data[i].quincena_valor );
                    linea = linea.replace(/{Saldo}/g,  data[i].quincena_saldo);
                    linea = linea.replace(/{Descontar}/g, "0.00" );
                    linea = linea.replace(/{Diario}/g,  data[i].diario_id);
                    linea = linea.replace(/{NDiario}/g,  data[i].diario_codigo);
                    
                    $("#tablaquincena tbody").append(linea);
                    document.getElementById("QDescontar"+counta).disabled=true;
                    counta= counta+1;
                    
            }           
                            
           
        },
    });
       

}
function cargarAnticipos(id){ 
    
    $.ajax({
        url: '{{ url("buscarAnticipos/searchN") }}'+ '/' +id,
        dataType: "json",
        type: "GET",
        async:false,
        data: {
            ide: id
        },
        success: function(data){
           var counta=1;
            for (var i=0; i<data.length; i++) {
                var linea = $("#plantillaItemAnticpos").html();
                    linea = linea.replace(/{ID}/g, counta);
                    linea = linea.replace(/{IDE}/g, data[i].anticipo_id);
                    linea = linea.replace(/{Fecha}/g, data[i].anticipo_fecha );
                    linea = linea.replace(/{Valor}/g, data[i].anticipo_valor );
                    linea = linea.replace(/{Saldo}/g,  data[i].anticipo_saldo);
                    linea = linea.replace(/{Descontar}/g, "0.00" );
                    linea = linea.replace(/{Diario}/g,  data[i].diario_id);
                    linea = linea.replace(/{NDiario}/g,  data[i].diario_codigo);
                    $("#tablaanticipos tbody").append(linea);
                    document.getElementById("Descontar"+counta).disabled=true;
                    counta= counta+1;
                    
            }                  
        },
    });
}
function cargardatosempleadosbanco(id){ 
    $.ajax({
        url: '{{ url("empleados/banco") }}'+ '/' +id,
        dataType: "json",
        async:false,
        type: "GET",
        data: {
            ide: id
        },
        success: function(data){
            for (var i=0; i<data.length; i++) {
              
                document.getElementById("bancoEmpleado").innerHTML=data[i].empleado_cuenta_numero;
                document.getElementById("cuentaempleado").innerHTML= data[i].banco_lista_nombre;
                document.getElementById("VbancoEmpleado").value=data[i].empleado_cuenta_numero;
                document.getElementById("Vcuentaempleado").value= data[i].banco_lista_nombre;
            }
        },

    });
            
}
function cargardatosempleados(id){ 
    
    $.ajax({
        url: '{{ url("empleados/searchN") }}'+ '/' +id,
        dataType: "json",
        async:false,
        type: "GET",
        data: {
            ide: id
        },
        success: function(data){
           
            for (var i=0; i<data.length; i++) {
                document.getElementById("Sueldo_Empelado").innerHTML=data[i].empleado_sueldo;
                document.getElementById("VEmpelado").value= data[i].empleado_sueldo;
                document.getElementById("asumidot").value= data[i].empleado_iess_asumido;
                document.getElementById("Valor_Dia").innerHTML=(data[i].empleado_sueldo/30).toFixed(2);
                document.getElementById("Valor_Dia").innerHTML=(data[i].empleado_sueldo/30).toFixed(2);

                document.getElementById("sueldo_basico").value=data[i].parametrizar_sueldo_basico;
               
        
                document.getElementById("%IESS_Pa").innerHTML=data[i].parametrizar_iess_patronal;
                document.getElementById("VIESS_Pa").value= data[i].parametrizar_iess_patronal;

                document.getElementById("%RES").innerHTML=data[i].parametrizar_fondos_reserva;
                document.getElementById("VRES").value= data[i].parametrizar_fondos_reserva;

                document.getElementById("IECE/SECAP").innerHTML=data[i].parametrizar_iece_secap;
                document.getElementById("VIESCAP").value= data[i].parametrizar_iece_secap;
               
                document.getElementById("Dia_Extra").innerHTML=data[i].parametrizar_porcentaje_he;
                document.getElementById("VDia_Extra").value= data[i].parametrizar_porcentaje_he;
             
                document.getElementById("Jornada").innerHTML=data[i].empleado_jornada;
                document.getElementById("VJornada").value= data[i].empleado_jornada;
                
                document.getElementById("Cosecha($)").innerHTML=data[i].empleado_cosecha;
                document.getElementById("VCosecha").value= data[i].empleado_cosecha;

              
        
                document.getElementById("%IESS").innerHTML=data[i].parametrizar_iess_personal;
                document.getElementById("VIESS").value= data[i].parametrizar_iess_personal;   
              
                document.getElementById("VTercero").value=data[i].empleado_decimo_tercero;
                document.getElementById("VCuarto").value=data[i].empleado_decimo_cuarto;
                document.getElementById("VFondo").value=data[i].empleado_fondos_reserva;
                document.getElementById("VTFondo").value=data[i].parametrizar_fondos_reserva;
                document.getElementById("VAfiliado").value=data[i].empleado_afiliado;
                document.getElementById("VInpuesto").value=data[i].empleado_impuesto_renta;
               
                if( data[i].empleado_fondos_reserva=="1"){    
                    if ( data[i].empleado_fecha_inicioFR <= fechaactual()) {       
                        document.getElementById("reservatacu").value ="1"; 
                        
                    }       
                }
                if( data[i].empleado_fondos_reserva=="0"){    
                    if ( data[i].empleado_fecha_inicioFR <= fechaactual()) {       
                        document.getElementById("reservatacu").value ="0"; 
                       
                    }     
                }
               
                
               
              
               cargarIngreso(id);
               
               cargaregreso(id);
              
            
            }                   
        },
    });
}
function cargarIngreso(id) {
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
       
    $.ajax({
        url: '{{ url("cargarIngreso") }}',
        dataType: "json",
        async: false,
        type: "POST",
        data: {
            buscar: id,
            aniorubro : document.getElementById("anio").value,
            mesrubro : document.getElementById("mes").value,
        },
        success: function(data){
            var total=0;
            for (var i=0; i<data.length; i++) {
               
                var linea = $("#plantillaItemingresos").html();
                linea = linea.replace(/{ID}/g, (i+1));
                    linea = linea.replace(/{idrol}/g, data[i]["idrol_mov"]);
                    linea = linea.replace(/{idrubro}/g, data[i]["idrubro"]);
                    linea = linea.replace(/{rubro}/g, data[i]["nombre"]);
                    linea = linea.replace(/{nombre}/g, data[i]["descripcion"]);
                    linea = linea.replace(/{tipo}/g,  data[i]["rubro_tipo"]);
                    linea = linea.replace(/{valor}/g,  Number(data[i]["valor"]).toFixed(2));
                    total+=Number(data[i]["valor"]);
                    $("#tablaingresos tbody").append(linea);
                
            }  
            let saludo         =  document.getElementById("VJornada").value;
            var saludoPalabras = saludo.split('-');
            if(Number(document.getElementById("Ausentel").innerHTML)>0){
                document.getElementById("DiaN").value=((30-Number(document.getElementById("Ausentel").innerHTML))*Number(document.getElementById("Normal").value)).toFixed(2);   
                document.getElementById("sueldos").innerHTML=((30-Number(document.getElementById("Ausentel").innerHTML))*Number(document.getElementById("Normal").value)).toFixed(2);
                document.getElementById("Vsueldos").value=((30-Number(document.getElementById("Ausentel").innerHTML))*Number(document.getElementById("Normal").value)).toFixed(2);
            }
            else{ 
                document.getElementById("DiaN").value=((Number(document.getElementById("VEmpelado").value)/Number(saludoPalabras[0]))*Number(document.getElementById("Normal").value)).toFixed(2);  
                document.getElementById("sueldos").innerHTML=((Number(document.getElementById("VEmpelado").value)/Number(saludoPalabras[0]))*Number(document.getElementById("Normal").value)).toFixed(2);
                document.getElementById("Vsueldos").value=(Number(document.getElementById("VEmpelado").value)/Number(saludoPalabras[0]))*Number(document.getElementById("Normal").value);
            }   
       
            sumaingresos();
          
            if (document.getElementById("VAfiliado").value=="1") {
                document.getElementById("aportePatronal").innerHTML=((Number(document.getElementById("Total_In").value)*Number(document.getElementById("%IESS_Pa").innerHTML))/100).toFixed(2);
                document.getElementById("VaportePatronal").value=document.getElementById("aportePatronal").innerHTML;
               
                document.getElementById("iece").innerHTML=((Number(document.getElementById("Total_In").value)*Number(document.getElementById("IECE/SECAP").innerHTML))/100).toFixed(2);
                document.getElementById("Viece").value= document.getElementById("iece").innerHTML;
                document.getElementById("vacacion").innerHTML=(Number(document.getElementById("Total_In").value)/24).toFixed(2);
                document.getElementById("Vvacacion").value= document.getElementById("vacacion").innerHTML;
                if (document.getElementById("VCuarto").value=="1") {
                    document.getElementById("TotalCuartoV").innerHTML=((Number(document.getElementById("sueldo_basico").value))/12).toFixed(2);
                    document.getElementById("TCuarto").value=document.getElementById("TotalCuartoV").innerHTML;
                    document.getElementById("pdecimoCuarto").value='ACUMULADO';
                }
                else{
                    document.getElementById("decimoCuarto").innerHTML=((Number(document.getElementById("sueldo_basico").value))/12).toFixed(2);
                    document.getElementById("VdecimoCuarto").value=document.getElementById("decimoCuarto").innerHTML;
                }
                if (document.getElementById("VTercero").value=="1") {
                    document.getElementById("TotalTerceroV").innerHTML=((Number(document.getElementById("Total_In").value))/12).toFixed(2);
                    document.getElementById("TTercero").value=document.getElementById("TotalTerceroV").innerHTML;
                    document.getElementById("pdecimoTercero").value='ACUMULADO';
                }
                else{
                    document.getElementById("decimoTercero").innerHTML=((Number(document.getElementById("Total_In").value))/12).toFixed(2);
                    document.getElementById("VdecimoTercero").value=document.getElementById("decimoTercero").innerHTML;
                }
                if (document.getElementById("reservatacu").value=="1") {
                    document.getElementById("fondoReserva").innerHTML=((Number(document.getElementById("Total_In").value)*Number(document.getElementById("%RES").innerHTML))/100).toFixed(2);
                    document.getElementById("VfondoReserva").value=((Number(document.getElementById("Total_In").value)*Number(document.getElementById("%RES").innerHTML))/100).toFixed(2);
                }
                if (document.getElementById("reservatacu").value=="0") {
                    document.getElementById("TotalFondosV").innerHTML=((Number(document.getElementById("Total_In").value)*Number(document.getElementById("%RES").innerHTML))/100).toFixed(2);
                    document.getElementById("TFondo").value=((Number(document.getElementById("Total_In").value)*Number(document.getElementById("%RES").innerHTML))/100).toFixed(2);
                    document.getElementById("pfondoReserva").value='ACUMULADO';
                }
                 
            }
            
           
        },
    });

}
function cargaregreso(id) {
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
    $.ajax({
        url: '{{ url("cargarEgreso") }}',
        dataType: "json",
        async: false,
        type: "POST",
        data: {
            buscar: id,
            aniorubro : document.getElementById("anio").value,
            mesrubro : document.getElementById("mes").value,
        },
        success: function(data){
            var total=0;
            for (var i=0; i<data.length; i++) {
               
                var linea = $("#plantillaItemingresos").html();
                    linea = linea.replace(/{ID}/g, (i+1));
                    linea = linea.replace(/{idrol}/g, data[i]["idrol_mov"]);
                    linea = linea.replace(/{idrubro}/g, data[i]["idrubro"]);
                    linea = linea.replace(/{rubro}/g, data[i]["nombre"]);
                    linea = linea.replace(/{nombre}/g, data[i]["descripcion"]);
                    linea = linea.replace(/{tipo}/g,  data[i]["rubro_tipo"]);
                    linea = linea.replace(/{valor}/g,  Number(data[i]["valor"]).toFixed(2));
                    total+=Number(data[i]["valor"]);
                    $("#tablaegresos tbody").append(linea);
                
            }  
            if (document.getElementById("VAfiliado").value=="1") {
                if(document.getElementById("asumidot").value=="1"){
                    document.getElementById("iessAsumido").innerHTML=((Number(document.getElementById("Total_In").value)*Number(document.getElementById("%IESS").innerHTML))/100).toFixed(2);
                    document.getElementById("ViessAsumido").value= document.getElementById("iessAsumido").innerHTML;
                }
                else{
                    document.getElementById("iess").innerHTML=((Number(document.getElementById("Total_In").value)*Number(document.getElementById("%IESS").innerHTML))/100).toFixed(2);
                    document.getElementById("Viess").value= document.getElementById("iess").innerHTML;
                }
                
            }
         
            sumaegresos();
                              
        },
    });
}

function sumaingresos() { 
    var ingresos=0;
    $('#tablaingresos tr').each(function () {
    ingresos=Number($(this).find("td").eq(1).html())+ingresos;
    });
    
    document.getElementById("Total_Inl").innerHTML= (ingresos).toFixed(2);      
    document.getElementById("Total_In").value= ingresos;

    document.getElementById("TotalIngresosV").innerHTML= (ingresos).toFixed(2);      
    document.getElementById("TIngresos").value= ingresos;
    sumatotales()
}
function sumaegresos() { 
    
    var descuentos=0;
    
    $("input[type='checkbox'][id='check']").each(function(){        
            if (this.checked) {
                descuentos+=Number(document.getElementById('Descontar'+$(this).val()).value);
              
            }
        });
        
        var quincena=0;
    
    $("input[type='checkbox'][id='Qcheck']").each(function(){        
            if (this.checked) {
               
                quincena+=Number(document.getElementById('QDescontar'+$(this).val()).value);
            }
        });

        
        document.getElementById("anticipos").innerHTML=(descuentos).toFixed(2);
        document.getElementById("Vanticipos").value=descuentos;
        document.getElementById("quincena").innerHTML=(quincena).toFixed(2);
        document.getElementById("Vquincena").value=quincena;
        

    var egresos=0;
   
    $('#tablaegresos tr').each(function () {
        egresos=Number($(this).find("td").eq(1).html())+egresos;
    });
    document.getElementById("Total_Egl").innerHTML= (egresos).toFixed(2);      
    document.getElementById("Total_Eg").value= egresos;

    document.getElementById("TotalEgresos").innerHTML= (egresos).toFixed(2);      
    document.getElementById("TEgresos").value= egresos;
    sumatotales();

}
function fechaactual(){
    var fechaactual = new Date();
    var mes = fechaactual.getMonth()+1;
    var dia=fechaactual.getDate();
    if (mes < 10) //ahora le agregas un 0 para el formato date
    {
    var mesactual = "0" + mes;
    } else {
    var mesactual = mes;
    }

    if (dia < 10) //ahora le agregas un 0 para el formato date
    {
    var diaactual = "0" + dia;
    } 
    else {
    var diaactual = dia;
    }
    var fecha= fechaactual.getFullYear()+'-'+mesactual+'-'+diaactual;
    return fecha;
}

function Selection(tipo){
    document.getElementById("tipo").value = tipo;
}

function cargardatosdias(id){ 
    $.ajax({
        url: '{{ url("dias/searchN") }}'+ '/' +id,
        dataType: "json",
        async:false,
        type: "GET",
        data: {
            ide: id
        },
        success: function(data){ 
                document.getElementById("idControldia").value=data[0].control_id;
                document.getElementById("fecha").value=data[0].control_fecha;

                document.getElementById("Normall").innerHTML=data[0].control_normal;
                document.getElementById("Normal").value=data[0].control_normal;

                document.getElementById("Descansol").innerHTML=data[0].control_decanso;
                document.getElementById("Descanso").value=data[0].control_decanso;

                document.getElementById("Vacacionesl").innerHTML=data[0].control_vacaciones;
                document.getElementById("Vacaciones").value=data[0].control_vacaciones;

                document.getElementById("Permisol").innerHTML=data[0].control_permiso;
                document.getElementById("Permiso").value=data[0].control_permiso;

                document.getElementById("Cosechal").innerHTML=data[0].control_cosecha;
                document.getElementById("Cosecha").value=data[0].control_cosecha;

                document.getElementById("Extral").innerHTML=data[0].control_extra;
                document.getElementById("Extra").value=data[0].control_extra;

                document.getElementById("Ausentel").innerHTML=data[0].control_ausente;
                document.getElementById("Ausente").value=data[0].control_ausente;
                document.getElementById("mes").value=data[0].control_mes;
                document.getElementById("anio").value=data[0].control_ano;

                Extraerdias( document.getElementById("fecha").value);

                document.getElementById("Totaldias").value=data[0].control_normal+data[0].control_decanso;

                /*
                var nuevo=0;
                if(total<30){
                   nuevo= Number( document.getElementById("Normall").innerHTML)
                    +Number( document.getElementById("Descansol").innerHTML)
                    +Number( document.getElementById("Vacacionesl").innerHTML)
                    +Number( document.getElementById("Permisol").innerHTML)
                    +Number( document.getElementById("Ausentel").innerHTML)
                    +Number( document.getElementById("Cosechal").innerHTML)
                    +Number( document.getElementById("Extral").innerHTML);
                   
                    document.getElementById("Normall").innerHTML=Number(document.getElementById("Normall").innerHTML)+(30-nuevo);
                    document.getElementById("Normal").value=document.getElementById("Normall").innerHTML;
                }
               

            */
        },
    }); 
}
function Extraerdias(fecha){  
    let fecha2 = new Date(fecha);
    fecha2.setMinutes(fecha2.getMinutes() + fecha2.getTimezoneOffset());
    var anioactual = fecha2.getFullYear();
    var _diaactual = fecha2.getDate();
    var _mesactual = fecha2.getMonth()+1;
  
    if (_mesactual < 10) //ahora le agregas un 0 para el formato date
    {
    var mesactual = "0" + _mesactual;
    } else {
    var mesactual = _mesactual;
    }

    if (_diaactual < 10) //ahora le agregas un 0 para el formato date
    {
    var diaactual = "0" + _diaactual;
    } 
    else {
    var diaactual = _diaactual;
    }
    var ultimoDia = new Date(anioactual,_mesactual, 0).getDate();  
    document.getElementById("fecha").value= anioactual + '-' + mesactual + '-01' ;
    document.getElementById("fechafinal").value= anioactual + '-' + mesactual + '-' + ultimoDia;
}
var total=0;

function cargarCuentacheque(){
    $.ajax({
        url: '{{ url("cuentaBancaria/searchN") }}'+ '/' +document.getElementById("banco_id_cheque").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("banco_id_cheque").value
        },
        success: function(data){

            document.getElementById("cuenta_id_cheque").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            document.getElementById("idCuentaContable_cheque").innerHTML="";
            for (var i=0; i<data.length; i++) {
                document.getElementById("cuenta_id_cheque").innerHTML += "<option value='"+data[i].cuenta_bancaria_id+"'>"+data[i].cuenta_bancaria_numero+"</option>";
            }           
        },
    });
}
function cargarCuentatransfer(){
    $.ajax({
        url: '{{ url("cuentaBancaria/searchN") }}'+ '/' +document.getElementById("banco_id_transfer").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("banco_id_transfer").value
        },
        success: function(data){

            document.getElementById("cuenta_id_transfer").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            document.getElementById("idCuentaContable_transfer").innerHTML="";
            for (var i=0; i<data.length; i++) {
                document.getElementById("cuenta_id_transfer").innerHTML += "<option value='"+data[i].cuenta_bancaria_id+"'>"+data[i].cuenta_bancaria_numero+"</option>";
            }           
        },
    });
}

function cargarbanco(){
    $.ajax({
        url: '{{ url("bancos/searchN") }}',
        dataType: "json",
        type: "GET",
        data: {
           
        },
        success: function(data){
            
            document.getElementById("cuenta_id_cheque").innerHTML = "";
            document.getElementById("idCuentaContable_cheque").innerHTML="";
            document.getElementById("cuenta_id_transfer").innerHTML = "";
            document.getElementById("idCuentaContable_transfer").innerHTML="";
            document.getElementById("cuenta_id_transfer").innerHTML = "";
            document.getElementById("banco_id_transfer").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";   
            document.getElementById("banco_id_cheque").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";   
            for (var i=0; i<data.length; i++) {
                document.getElementById("banco_id_transfer").innerHTML += "<option value='"+data[i].banco_id+"'>"+data[i].banco_lista_nombre+"</option>";
                document.getElementById("banco_id_cheque").innerHTML += "<option value='"+data[i].banco_id+"'>"+data[i].banco_lista_nombre+"</option>";
            }  
               
        },
    });
}
function cargarContablecheque(){
    $.ajax({
        url: '{{ url("cuentaContable/searchN") }}'+ '/' +document.getElementById("cuenta_id_cheque").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("cuenta_id_cheque").value
        },
        success: function(data){
            
            for (var i=0; i<data.length; i++) {
                document.getElementById("idCuentaContable_cheque").innerHTML += "<option value='"+data[i].cuenta_id+"'>"+data[i].cuenta_numero+"-"+data[i].cuenta_nombre+"</option>";   
    
                document.getElementById("ncuenta_cheque").value= data[i].cuenta_id;
            }           
        },
    });
}
function cargarContabletransfer(){
    $.ajax({
        url: '{{ url("cuentaContable/searchN") }}'+ '/' +document.getElementById("cuenta_id_transfer").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("cuenta_id_transfer").value
        },
        success: function(data){
            
            for (var i=0; i<data.length; i++) {
                document.getElementById("idCuentaContable_transfer").innerHTML += "<option value='"+data[i].cuenta_id+"'>"+data[i].cuenta_numero+"-"+data[i].cuenta_nombre+"</option>";
              
                document.getElementById("ncuenta_transfer").value= data[i].cuenta_id;
            }           
        },
    });
}
function cargarCuentaCaja(){    
    $.ajax({
        url: '{{ url("cuentasCaja/searchN") }}',
        dataType: "json",
        type: "GET",
        data: {
        
        },
        success: function(data){                  
            for (var i=0; i<data.length; i++) {
                document.getElementById("idCuentaContableCaja").innerHTML += "<option value='"+data[i].cuenta_id+"'>"+data[i].cuenta_numero+"-"+data[i].cuenta_nombre+"</option>";
            }

        },
    });
    cargarCuentaParametrizada();
}
function cargarAliemtacion(id){ 
    
    $.ajax({
        url: '{{ url("buscarAlimentacion/search") }}'+ '/' +id,
        dataType: "json",
        type: "GET",
        async:false,
        data: {
            ide: id
        },
        success: function(data){
            var counta=1;
            for (var i=0; i<data.length; i++) {
                var linea = $("#plantillaItemAlimentacion").html();
                    linea = linea.replace(/{ID}/g, counta);
                    linea = linea.replace(/{IDE}/g, data[i].alimentacion_id);
                    linea = linea.replace(/{Fecha}/g, data[i].alimentacion_fecha );
                    linea = linea.replace(/{factura}/g, data[i].transaccion_numero );
                    linea = linea.replace(/{Valor}/g, data[i].alimentacion_valor );
                    $("#tablacomida tbody").append(linea);
                   // document.getElementById("Alimentacion"+counta).disabled=true;
                    counta= counta+1;
                    
            }  
             
        },
    });
}
function cargarCuentaParametrizada(){    
    $.ajax({
        url: '{{ url("cuentaParametrizadaCaja/searchN/CAJA") }}',
        dataType: "json",
        type: "GET",
        data: {
            buscar: "CAJA"
        },
        success: function(data){

            for (var i=0; i<data.length; i++) {
                $("#idCuentaContableCaja > option[value="+ data[i].cuenta_id +"]").attr("selected",true);
                $("#idCuentaContableCaja").select2().val(data[i].cuenta_id).trigger("change");    
            }                  
        },
    });
}

function ExtraerVacaciones(id) {
    $.ajax({
        url: '{{ url("buscarvacaciones/searchN") }}'+ '/' +id,
        dataType: "json",
        type: "GET",
        async:false,
        data: {
            ide: id
        },
        success: function(data){
         
            for (var i=0; i<data.length; i++) {
                     document.getElementById("TVacacion").innerHTML=(0).toFixed(2); 
                    document.getElementById("vvacacion").value=(0).toFixed(2); 
                   
                    
                    document.getElementById("idvacacion").value=(0).toFixed(2); 
            }                  
            sumaingresos();
        },
    });
       

}
function porcentajepermiso() {

    document.getElementById("VPermisol").innerHTML=(((Number(document.getElementById("Permisol").innerHTML)*Number(document.getElementById("VDia").value))*Number(document.getElementById("idPerpor").value))/100).toFixed(2);
    document.getElementById("VPermiso").value= document.getElementById("VPermisol").innerHTML;
    sumaingresos();
}


function getRow() {
        $("input[type='checkbox'][id='check']").each(function(){        
            if (this.checked) {           
                document.getElementById('Descontar'+$(this).val()).disabled=false;  
                document.getElementById('Descontar'+$(this).val()).value= document.getElementById('TSaldo'+$(this).val()).value;  
            }
            else{
                document.getElementById('Descontar'+$(this).val()).disabled=true;
                document.getElementById('Descontar'+$(this).val()).value=0.00;
            }
        }); 
        sumaegresos(); 
}
function QgetRow() {
        $("input[type='checkbox'][id='Qcheck']").each(function(){        
            if (this.checked) {           
                document.getElementById('QDescontar'+$(this).val()).disabled=false;
                document.getElementById('QDescontar'+$(this).val()).value= document.getElementById('QTSaldo'+$(this).val()).value;
            }
            else{
                document.getElementById('QDescontar'+$(this).val()).disabled=true;
                document.getElementById('QDescontar'+$(this).val()).value=0.00;
            }
        }); 
        sumaegresos(); 
}
function getalimentacion(id) {
    var nuevo=0; 

   
    if ($("input[name='checkali[]']")[id].checked==true) { 
        if (nuevo>=0) {    
            nuevo= Number(document.getElementById("comisariato").innerHTML)+Number($("input[name='Valor[]']")[id].value);
            document.getElementById("comisariato").innerHTML=Number(nuevo).toFixed(2);
            document.getElementById("Vcomisariato").value=Number(nuevo).toFixed(2);
        }
        else{
            $("input[name='checkali[]']")[id].checked=false;
        }    
    
    }
    else{
            nuevo= Number(document.getElementById("comisariato").innerHTML)-Number($("input[name='Valor[]']")[id].value);
            document.getElementById("comisariato").innerHTML=Number(nuevo).toFixed(2);
            document.getElementById("Vcomisariato").value=Number(nuevo).toFixed(2);
    }

    sumaegresos();
}
function SumaAdelantos(id) {
    var nuevo=0; 
    if(Number($("input[name='TDescontar[]']")[id].value)<=Number($("input[name='TSaldo[]']")[id].value)) {           
                
        var liquidacion= Number(document.getElementById("LiquidacionTotal").innerHTML);
       
    $("input[type='checkbox'][id='check']").each(function(){        
            if (this.checked) {   
                    nuevo+=Number(document.getElementById('Descontar'+$(this).val()).value);

                   
                
                        $("input[name='TDescont[]']")[id].value=$("input[name='TDescontar[]']")[id].value;
                        document.getElementById("anticipos").innerHTML=nuevo;  
                        document.getElementById("Vanticipos").value=nuevo;  
                    
               
            }
        });
    }
    else{
        $("input[name='TDescontar[]']")[id].value=0.00;
        $("input[name='TDescont[]']")[id].value=0.00;
    }
    sumaegresos();
}
function SumaQuincena(id) {
    var nuevo=0; 
    if(Number($("input[name='QTDescontar[]']")[id].value)<=Number($("input[name='QTSaldo[]']")[id].value)) {           
        var liquidacion= Number(document.getElementById("LiquidacionTotal").innerHTML);         
    $("input[type='checkbox'][id='Qcheck']").each(function(){        
            if (this.checked) {   
                    nuevo+=Number(document.getElementById('QDescontar'+$(this).val()).value);
                    
                    $("input[name='QTDescont[]']")[id].value=$("input[name='QTDescontar[]']")[id].value;
                    document.getElementById("quincena").innerHTML=nuevo;  
                    document.getElementById("Vquincena").value=nuevo;  
                    
            }
        });
    }
    else{
        $("input[name='QTDescontar[]']")[id].value=0.00;
        $("input[name='QTDescont[]']")[id].value=0.00;
    }
    sumaegresos();

}
function sumatotales(){
    var otros=0;
    $('#tablatotal tr').each(function () {
        otros=otros+Number(($(this).find('td').eq(1).find('input').val()));
  
    });

    
    document.getElementById("TIngreEgreV").innerHTML=(Number( document.getElementById("Total_Inl").innerHTML)-Number(document.getElementById("Total_Egl").innerHTML)).toFixed(2);
  
    document.getElementById("LiquidacionTotal").innerHTML=(Number(document.getElementById("TIngreEgreV").innerHTML)
    +Number(document.getElementById("TotalFondosV").innerHTML)
    +Number(document.getElementById("TotalTerceroV").innerHTML)
    +Number(document.getElementById("TotalCuartoV").innerHTML)+otros).toFixed(2);
    
    document.getElementById("Liquidacion").value=Number(document.getElementById("LiquidacionTotal").innerHTML);
   
}
</script>
@endsection