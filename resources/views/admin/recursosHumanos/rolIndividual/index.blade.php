@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-primary ">
    <form method="POST" action="{{ url("rolindividual") }} "> 
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
                                <br>

                            </div>
                            <div class="row clearfix form-horizontal">
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label "
                                       >
                                <label >Consumo</label>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" >  
                                    <select class="custom-select select2" id="consumo" name="consumo" >               
                                        @foreach($consumo as $consumos)
                                            <option id="{{$consumos->centro_consumo_nombre}}" name="{{$consumos->centro_consumo_nombre}}" value="{{$consumos->centro_consumo_id}}">{{$consumos->centro_consumo_nombre}}</option>
                                        @endforeach
                                    </select>    
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label "
                                       >
                                <label >Categoria</label>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" >  
                                
                                        <select class="custom-select select2" id="categoria" name="categoria" >               
                                            @foreach($categoria as $categorias)
                                                <option id="{{$categorias->categoria_nombre}}" name="{{$categorias->categoria_nombre}}" value="{{$categorias->categoria_id}}">{{$categorias->categoria_nombre}}</option>
                                            @endforeach
                                        </select>      
                            </div>
                            </div>
                            <br>
                        </div>  
                    </div> 
                    <div id="ulprueba" class="row">  
                        
                    
                        <!-- Tabla de ingresos -->
                                <div  class="col-md-6">
                              
                                    <div class="card card-secondary">  
                                        <div class="card-header">
                                            <h3 class="card-title ">Ingresos
                                            </h3>
                                            <button type="button" id="addin" name="addin"style=" background-color: transparent;border-color: transparent;box-shadow: none;" ><br></button>
                                        </div>
                                        
                                        <div class="card-body table-responsive p-0" style="height: 170px;">  
                                            <table id="exampleingresos"  class="table table-head-fixed text-nowrap">
                                                <thead> 
                                                    <tr >
                                                        <th  class="text-center-encabesado">Fecha desde</th>
                                                        <th class="text-center-encabesado">Fecha hasta</th> 
                                                        <th class="text-center-encabesado">Porcentaje</th>                       
                                                        <th class="text-center-encabesado">Dias</th>
                                                        <th class="text-center-encabesado">Sueldo </th>
                                                        <th class="text-center-encabesado">Vacaciones </th>  
                                                        <th class="text-center-encabesado">Horas Extras </th>  
                                                        <th class="text-center-encabesado">Horas Suplementarias</th> 
                                                        <th class="text-center-encabesado">Viaticos </th> 
                                                        <th class="text-center-encabesado">otros Bonificaciones </th>    
                                                        <th class="text-center-encabesado">otros ingresos </th>    
                                                        <th class="text-center-encabesado">Total Ingresos</th>    
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
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
                                                        value="0" min="0" max="31" onclick="calculosueldo();" onkeyup="calculosueldo();"  required> </td>
                                                        
                                                        <td> <input type="number" id="sueldo" name="sueldo" class="form-controltext"
                                                        value="0.00" min="0"  step="0.01"   required readonly> </td>
                                                        <td>  <input type="number" id="vacaciones" name="vacaciones" class="form-controltext "
                                                        value="0.00" min="0"  step="0.01" onclick="sumaingresos();" onkeyup="sumaingresos();"  required > </td>
                                                        <td><input type="number" id="extras" name="extras" class="form-controltext "
                                                        value="0.00" min="0"  step="0.01"  onclick="sumaingresos();" onkeyup="sumaingresos();" required> </td>
                                                        <td> <input type="number" id="horas_suplementarias" name="horas_suplementarias" class="form-controltext "
                                                        value="0.00" min="0"  step="0.01" onclick="sumaingresos();" onkeyup="sumaingresos();" required></td>
                                                        <td> <input type="number" id="transporte" name="transporte" class="form-controltext "
                                                        value="0.00" min="0"  step="0.01" onclick="sumaingresos();" onkeyup="sumaingresos();" required></td>
                                                        <td><input type="number" id="otros_boni" name="otros_boni" class="form-controltext "
                                                            value="0.00" min="0"  step="0.01" onclick="sumaingresos();" onkeyup="sumaingresos();"  required> </td>
                                                        <td><input type="number" id="otrosin" name="otrosin" class="form-controltext "
                                                            value="0.00" min="0"  step="0.01" onclick="sumaingresos();" onkeyup="sumaingresos();"   required> </td>
                                                        <td><input type="number" id="Sbingresos" name="Sbingresos" class="form-controltext "
                                                            value="0.00"  step="0.01" readonly required> </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            
                        <!-- Tabla de egresos -->
                            <div  class="col-md-6">
                           
                                <div class="card card-secondary"> 
                                    <div class="card-header">
                                        <h3 class="card-title ">Egresos</h3>
                                        <button type="button" id="add" name="add" onclick="agregarItem();" class="btn btn-default btn-sm float-right"><i
                                                        class="fas fa-plus"></i>Agregar</button > 
                                    </div> 
                                    <div class="card-body table-responsive p-0" style="height: 170px;">  
                                        <table id="exampleegresos" class="table table-head-fixed text-nowrap">
                                            <thead>  
                                                <tr >
                                                
                                                    <th class="text-center-encabesado">EXT. Salud </th>
                                                  
                                                    <th class="text-center-encabesado">PPQQ</th>  
                                                    <th class="text-center-encabesado">Prestamos Hipotecarios </th> 
                                                    <th class="text-center-encabesado">Prestamos </th> 
                                                    <th class="text-center-encabesado">Multas</th>    
                                                    <th class="text-center-encabesado">Otros Egresos</th>   
                                                    <th class="text-center-encabesado">Ley salud</th>  
                                                    <th class="text-center-encabesado">Total Egresos</th>    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                    <input type="number" id="Salud" name="Salud" class="form-controltext"
                                                    value="0.00" min="0"  step="0.01" onclick="sumaegresos();" onkeyup="sumaegresos();"  required>
                                                    </td>
                                                    
                                                    <td>
                                                    <input type="number" id="PPQQ" name="PPQQ" class="form-controltext "
                                                    value="0.00" min="0"  step="0.01" onclick="sumaegresos();" onkeyup="sumaegresos();"  required>
                                                    </td>
                                                    <td>
                                                    <input type="number" id="Hipotecarios" name="Hipotecarios" class="form-controltext"
                                                        value="0.00" min="0"  step="0.01" onclick="sumaegresos();" onkeyup="sumaegresos();"  required>
                                                    </td>
                                                    <td>
                                                    <input type="number" id="Prestamos" name="Prestamos" class="form-controltext "
                                                        value="0.00" min="0"  step="0.01" onclick="sumaegresos();" onkeyup="sumaegresos();"  required>
                                                    </td>
                                                    <td>
                                                    <input type="number" id="Multas" name="Multas" class="form-controltext"
                                                        value="0.00" min="0"  step="0.01" onclick="sumaegresos();" onkeyup="sumaegresos();"  required>
                                                    </td>
                                                
                                                    <td>
                                                    <input type="number" id="Otro_Egresos" name="Otro_Egresos" class="form-controltext "
                                                        value="0.00" min="0"  step="0.01" onclick="sumaegresos();" onkeyup="sumaegresos();" required>
                                                    </td>
                                                    <td>
                                                    <input type="number" id="Ley_salud" name="Ley_salud" class="form-controltext "
                                                        value="0.00" min="0"  step="0.01" onclick="sumaegresos();" onkeyup="sumaegresos();" required>
                                                    </td>
                                                    <td>
                                                    <input type="number" id="Sbegresos" name="Sbegresos" class="form-controltext "
                                                        value="0.00"  step="0.01" readonly required>
                                                    </td>
                                                
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>    
                            </div>
                            
                        <!-- Tabla de detalle -->
                            <div  class="col-md-12">  
                                <div class="card">       
                                    <div class="card-body table-responsive p-0" style="height: 200px;" > 
                                        @include ('admin.recursosHumanos.rolIndividual.items')          
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
                                                    <th class="text-center-encabesado">Horas Suplemenntarias </th>
                                                    <th class="text-center-encabesado">Viaticos </th>              
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
                                                    <th> </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
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
                                                                <input type="hidden" class="form-control" id="ncuenta_cheque" name="ncuenta_cheque" >
                                                                
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
                                                            <input type="number" class="form-control" id="idNcheque" name="idNcheque" min="1" >
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
                                                            <input type="hidden" class="form-control" id="ncuenta_transfer" name="ncuenta_transfer" >
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
                                                </div>
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
                                            <td id="TotalIngresosV" name="TotalIngresosV" width="100" class="derecha-texto negrita">0.00
                                            </td>
                                            <input type="hidden"   name="TIngresos"  id="TIngresos" value="0" required readonly>
                                            <input type="hidden"   name="sueldot" id="sueldot" value="0" >
                                            <input type="hidden"   name="sueldod" id="sueldod" value="0" >                          
                                            <input type="hidden"   name="afiliadot"  id="afiliadot" value="0" required readonly>     
                                            <input type="hidden"   name="IessGerencial"  id="IessGerencial" value="0" required readonly>                    
                                            <input type="hidden"   name="asumidot"  id="asumidot" value="0" required readonly>
                                            <input type="hidden"   name="IESCAP"  id="IESCAP" value="0" required readonly>                            
                                            <input type="hidden"   name="impu_rentat"  id="impu_rentat" value="" required readonly>
                                            <input type="hidden"   name="reservat"  id="reservat" value="" required readonly>  
                                            <input type="hidden"   name="acureservat"  id="acureservat" value="" required readonly>  
                                            <input type="hidden"   name="tercerot"  id="tercerot" value="0" required readonly>
                                            <input type="hidden"   name="cuartot"  id="cuartot" value="0" required readonly>
                                            <input type="hidden"   name="tercerot"  id="tercerot" value="0" required readonly>
                                            <input type="hidden"   name="VReservat"  id="VReservat" value="0" required readonly>
                                            <input type="hidden"   name="FechaReservat"  id="FechaReservat" value="0" required readonly>
                                            <input type="hidden"   name="Personalt"  id="Personalt" value="0" required readonly>
                                            <input type="hidden"   name="Patronalt"  id="Patronalt" value="0" required readonly>
                                            <input type="hidden"   name="Gerencialt"  id="Gerencialt" value="0" required readonly>
                                            
                                            <input type="hidden"   name="Basicot"  id="Basicot" value="0" required readonly>
                                            <input type="hidden"   name="DiasTrabajot"  id="DiasTrabajot" value="0" required readonly>  
                                            <input type="hidden"   name="vquincena"  id="vquincena" value="0" required readonly>  
                                            <input type="hidden"   name="idempleado"  id="idempleado" value="0" required readonly>                            
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Total Egresos</td>
                                            <td id="TotalEgresos"  name="TotalEgresos" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="TEgresos"  id="TEgresos" value="0" required readonly>
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Ingre. - Egre.
                                            </td>
                                            <td id="TIngreEgreV" name="TIngreEgreV" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="TIngreEgre"  id="TIngreEgre" value="0" required readonly>
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Fond. Reser.</td>
                                            <td id="TotalFondosV"  name="TotalFondosV" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="TFondo"  id="TFondo" value="0" required readonly> 
                                            <input type="hidden"   name="TFondoacu"  id="TFondoacu" value="0" required readonly>        
                                            <input type="hidden"   name="calreser"  id="calreser" value="0" required readonly>  
                                            <input type="hidden"   name="diasreser"  id="diasreser" value="0" required readonly>          
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Decimo Tercero</td>
                                            <td id="TotalTerceroV"  name="TotalTerceroV" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="TTercero"  id="TTercero" value="0" required readonly>
                                            <input type="hidden"   name="TTerceroacu"  id="TTerceroacu" value="0" required readonly>                      
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Decimo Cuarto</td>
                                            <td id="TotalCuartoV"  name="TotalCuartoV" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="TCuarto"  id="TCuarto" value="0" required readonly>           
                                            <input type="hidden"   name="TCuartoacu"  id="TCuartoacu" value="0" required readonly>           
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita">Vac. Pagadas
                                            </td>
                                            <td id="LVac_pagadas"  name="LVac_pagadas" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="Vac_pagadas"  id="Vac_pagadas" value="0" required readonly>                    
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita">Quincena
                                            </td>
                                            <td id="Tquincena"  name="Tquincena" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="idquincena"  id="idquincena" value="0" required readonly>
                                            <input type="hidden"   name="vvacacion"  id="vvacacion" value="0" required readonly>     
                                            <input type="hidden"   name="idvacacion"  id="idvacacion" value="0" required readonly>                     
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita">Total Anticipos
                                            </td>
                                            <td id="TotalAdelantosV"  name="TotalAdelantosV" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="adelanto"  id="adelanto" value="0" required readonly>                
                                        </tr>
                                        <tr>
                                            <td  class="letra-blanca fondo-gris-oscuro negrita">Total Alimentacion
                                            </td>
                                            <td id="TotalAlimentacionV"  name="TotalAlimentacionV" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="alimentacion"  id="alimentacion" value="0" required readonly>       
                                            <input type="hidden"   name="idalimentacion"  id="idalimentacion" value="0" required readonly>            
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">IESS</td>
                                            <td id="TotalIess"  name="TotalIess" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="TotalIESCAP"  id="TotalIESCAP" value="0" required readonly>
                                            <input type="hidden"   name="Totalpatronal"  id="Totalpatronal" value="0" required readonly>
                                            <input type="hidden"   name="Totalpersonal"  id="Totalpersonal" value="0" required readonly>
                                            <input type="hidden"   name="Tiess"  id="Tiess" value="0" required readonly>    
                                            <input type="hidden"    id="vanti" value="0" required readonly>          
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">IESS Asumido</td>
                                            <td id="TotalAsumido"  name="TotalAsumido" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="Tasumido"  id="Tasumido" value="0" required readonly>           
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Imp. Renta</td>
                                            <td id="TotalRent"  name="TotalRent" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="Trenta"  id="Trenta" value="0" required readonly>           
                                        </tr>
                                        <tr>
                                            <td class="letra-blanca fondo-gris-oscuro negrita">Liquido a Pagar</td>
                                            <td id="LiquidacionTotal" name="LiquidacionTotal" class="derecha-texto negrita">0.00</td>
                                            <input type="hidden"   name="Liquidacion"  id="Liquidacion" value="0" required readonly> 
                                            <input type="hidden"   name="fechafinal"  id="fechafinal" value="0" required readonly>       
                                            <input type="hidden"   name="Totaldias"  id="Totaldias" value="0" required readonly>    
                                            <input type="hidden"   name="Totalsueldo"  id="Totalsueldo" value="0" required readonly>                 
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
function nuevo() { 
    if($('input:radio[name=radioempleado]:checked').val()== undefined){
        alert('Selecione un empelado');
    }
    else{
    document.getElementById("guardarID").disabled = false;
    document.getElementById("cancelarID").disabled = false;
    document.getElementById("nuevoID").disabled = true;
    document.getElementById("exampleingresos").disabled = true;
    document.getElementById("idempleado").value=$('input:radio[name=radioempleado]:checked').val();
    $("#ulprueba").find("*").prop('disabled', false); 
    $("#tableBuscar").find("*").prop('disabled', true); 
    $("#example").find("input,button,textarea,select").attr("disabled", "disabled");
    cargarAnticipos($('input:radio[name=radioempleado]:checked').val());
   
    cargardatosempleados($('input:radio[name=radioempleado]:checked').val());
   
    ExtraerQuincena($('input:radio[name=radioempleado]:checked').val());

    ExtraerVacaciones($('input:radio[name=radioempleado]:checked').val());
    cargarAliemtacion($('input:radio[name=radioempleado]:checked').val());
    }   
}
function Selection(tipo){
    document.getElementById("tipo").value = tipo;
}
function round(num) {
    var m = Number((Math.abs(num) * 100).toPrecision(15));
        m =Math.round(m) / 100 * Math.sign(num);
        return (m).toFixed(2);
}
function agregarItem() {
    
    for (let step = 1; step < document.getElementById("tabla").rows.length; step++) {

        let fecha = new Date($("input[name='Tdesde[]']")[step].value);
        fecha.setMinutes(fecha.getMinutes() + fecha.getTimezoneOffset());
        let fechaini = new Date(document.getElementById("fecha_desde").value);
        fechaini.setMinutes(fechaini.getMinutes() + fechaini.getTimezoneOffset());

        if(fecha.getMonth()!=fechaini.getMonth() || fecha.getFullYear()!=fechaini.getFullYear()){
            alert('Ingrese fechas que esten dentro del Mes y Año verifique por favor');
            return;
        } 
        if(document.getElementById("fecha_desde").value >= $("input[name='Tdesde[]']")[step].value  && document.getElementById("fecha_desde").value <= $("input[name='Thasta[]']")[step].value){
            alert('No puede ingresar una fechas que estan registrada verifique por favor');
            return;
        }
    }
    var linea = $("#plantillaItem").html();
      linea = linea.replace(/{ID}/g, id_item);
       linea = linea.replace(/{desde}/g, document.getElementById("fecha_desde").value);
       linea = linea.replace(/{hasta}/g, document.getElementById("fecha_hasta").value);
       linea = linea.replace(/{porcentaje}/g, document.getElementById("idTipo").value);
       linea = linea.replace(/{dias}/g, document.getElementById("dias").value);
       linea = linea.replace(/{DCSueldo}/g, (Number(document.getElementById("sueldo").value)).toFixed(2));
       linea = linea.replace(/{vacaciones}/g,(Number( document.getElementById("vacaciones").value)).toFixed(2));
       linea = linea.replace(/{extras}/g, (Number(document.getElementById("extras").value)).toFixed(2));
       linea = linea.replace(/{horas_suplementarias}/g,(Number( document.getElementById("horas_suplementarias").value)).toFixed(2));
       linea = linea.replace(/{transporte}/g, (Number(document.getElementById("transporte").value)).toFixed(2));
       linea = linea.replace(/{otrosbon}/g, (Number(document.getElementById("otros_boni").value)).toFixed(2));
       linea = linea.replace(/{otrosin}/g,(Number( document.getElementById("otrosin").value)).toFixed(2));
       linea = linea.replace(/{salud}/g, (Number(document.getElementById("Salud").value)).toFixed(2));

       linea = linea.replace(/{ppqq}/g, (Number(document.getElementById("PPQQ").value)).toFixed(2));
       linea = linea.replace(/{hipotecarios}/g, (Number(document.getElementById("Hipotecarios").value)).toFixed(2));
       linea = linea.replace(/{prestamos}/g, (Number(document.getElementById("Prestamos").value)).toFixed(2));
       linea = linea.replace(/{multas}/g,(Number( document.getElementById("Multas").value)).toFixed(2));
       linea = linea.replace(/{otrosegre}/g, (Number(document.getElementById("Otro_Egresos").value)).toFixed(2));
       linea = linea.replace(/{Ley_salud}/g, (Number(document.getElementById("Ley_salud").value)).toFixed(2)); 
       linea = linea.replace(/{ingresos}/g, document.getElementById("Sbingresos").value); 
       linea = linea.replace(/{totalegresos}/g, document.getElementById("Sbegresos").value); 
       linea = linea.replace(/{subtotal}/g, (Number(document.getElementById("Sbingresos").value)-Number(document.getElementById("Sbegresos").value))); 
       
       $("#tabla tbody").append(linea);
       id_item = id_item + 1;
       
       
       resetearCampos();
       calculototales();
}
function eliminarItem(id) {
    $("#row_" + id).remove();
    document.getElementById("TotalAdelantosV").innerHTML=0.00; 
    $("input[type='checkbox'][id='check']").each(function(){        
            if (this.checked) {   
                   document.getElementById('Descontar'+$(this).val()).value=0.00;
            }    
        });
    
    calculototales();
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
function cargarAliemtacion(id){ 
    
    $.ajax({
        url: '{{ url("buscarAlimentacion/searchN") }}'+ '/' +id,
        dataType: "json",
        type: "GET",
        data: {
            ide: id
        },
        success: function(data){
            for (var i=0; i<data.length; i++) {
                document.getElementById("TotalAlimentacionV").innerHTML= Number(data[i].alimentacion_valor).toFixed(2);
                document.getElementById("alimentacion").value= data[i].alimentacion_valor;
                document.getElementById("idalimentacion").value= data[i].alimentacion_id;
               
            } 
        sumatotales();                  
        },
    });
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
                document.getElementById("sueldot").value= data[i].empleado_sueldo;
                document.getElementById("afiliadot").value= data[i].empleado_afiliado;
                document.getElementById("asumidot").value= data[i].empleado_iess_asumido;
                document.getElementById("impu_rentat").value= data[i].empleado_impuesto_renta;
                document.getElementById("IessGerencial").value= data[i].empleado_iess_gerente;
                document.getElementById("tercerot").value= data[i].empleado_decimo_tercero;
                document.getElementById("cuartot").value= data[i].empleado_decimo_cuarto;
                document.getElementById("Personalt").value= data[i].parametrizar_iess_personal;
                document.getElementById("Patronalt").value= data[i].parametrizar_iess_patronal;
                document.getElementById("Gerencialt").value= data[i].parametrizar_iess_gerencial;
                document.getElementById("IESCAP").value= data[i].parametrizar_iece_secap;
                document.getElementById("VReservat").value= data[i].parametrizar_fondos_reserva;
                document.getElementById("FechaReservat").value= data[i].empleado_fecha_inicioFR;
                document.getElementById("Basicot").value= data[i].parametrizar_sueldo_basico;
                document.getElementById("DiasTrabajot").value= data[i].parametrizar_dias_trabajo;          
                document.getElementById("sueldo").value= (data[i].empleado_sueldo/30).toFixed(2);
                document.getElementById("dias").value= 1;
                document.getElementById("sueldod").value= (data[i].empleado_sueldo/30);
                document.getElementById("acureservat").value= data[i].empleado_fondos_reserva;  
              
                document.getElementById("Sbingresos").value=(data[i].empleado_sueldo/30).toFixed(2);
                document.getElementById("Sbegresos").value=0.00;
            }                  
        },
    });
}

function mes(dfecha){
    let fecha2 = new Date(dfecha);
    fecha2.setMinutes(fecha2.getMinutes() + fecha2.getTimezoneOffset());
    var _mesactual = fecha2.getMonth()+1;
    var anioactual = fecha2.getFullYear();
    if (_mesactual < 10) //ahora le agregas un 0 para el formato date
    {
    var mesactual = "0" + _mesactual;
    } else {
    var mesactual = _mesactual;
    }
    
   return anioactual + '-' + mesactual ; 


}
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
                var combo = document.getElementById("cuenta_id_cheque");
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
                var combo = document.getElementById("cuenta_id_transfer");
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
function Extraerdia30(){
    let fecha2 = new Date(document.getElementById("fecha_hasta").value);
    fecha2.setMinutes(fecha2.getMinutes() + fecha2.getTimezoneOffset());
    var _diaactual = fecha2.getDate();
    var anioactual = fecha2.getFullYear();
    var _mesactual = fecha2.getMonth()+1;

    if (_mesactual < 10) //ahora le agregas un 0 para el formato date
    {
    var mesactual = "0" + _mesactual;
    } else {
    var mesactual = _mesactual;
    }
    if(_diaactual=='31'){
       
        _diaactual=30;

    }
    if (_diaactual < 10) //ahora le agregas un 0 para el formato date
    {
    var diaactual = "0" + _diaactual;
    }
    else {
    var  diaactual = _diaactual;
    } 
    let fecha_minimo = anioactual + '-' + mesactual + '-' + diaactual; 

    document.getElementById("fecha_hasta").value=fecha_minimo;


    
    dateDiffer (document.getElementById("fecha_desde").value, document.getElementById("fecha_hasta").value);

}
function Extraerdias(){  
    let fecha2 = new Date(document.getElementById("fecha_desde").value);
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
    document.getElementById("fechafinal").value= anioactual + '-' + mesactual + '-' + ultimoDia;
    if(ultimoDia=='31'){
     
       ultimoDia=ultimoDia-1;
   }
   
    let fecha_minimo = anioactual + '-' + mesactual + '-' + diaactual; 
    let fecha_maximo = anioactual + '-' + mesactual + '-' + ultimoDia; 
   
  
    
    document.getElementById("fecha_hasta").setAttribute('min',fecha_minimo);
    document.getElementById("fecha_hasta").setAttribute('max',fecha_maximo);
    document.getElementById("fecha_hasta").value=fecha_maximo;
   
    dateDiffer (document.getElementById("fecha_desde").value, document.getElementById("fecha_hasta").value);
}
function es_bisiesto(year){
    return ((year % 4 == 0 && year % 100 != 0) || year % 400 == 0) ? true : false;
}       
function dateDiffer (fecha1, fecha2) {
             // Devuelve los milisegundos de las dos fechas
      let d1 = Date.parse(fecha1)
      let d2 = Date.parse(fecha2)
             // Obtiene el valor absoluto de la diferencia
      let dateDiffer = Math.abs(d1 - d2)
             // La diferencia es milisegundos convertidos a días
      let differDay = Math.floor(dateDiffer / (24 * 3600 * 1000))
      differDay=differDay+1
      
      let fecha = new Date(fecha1);
      fecha.setMinutes(fecha.getMinutes() + fecha.getTimezoneOffset());
        var anioactual = fecha.getFullYear();
        var diaactual = fecha.getDate();
        var mesactual = fecha.getMonth()+1;
        
      if(es_bisiesto(anioactual)==true){
        if(mesactual==2){
            differDay=differDay+1;
        }
      }
      if(es_bisiesto(anioactual)==false){
        if(mesactual==2){
            differDay= differDay+2;
        }
      } 
      document.getElementById("dias").value=differDay;
      calculosueldo();
      return differDay;
}
function dateDiffer2 (fecha1, fecha2) {
             // Devuelve los milisegundos de las dos fechas
      let d1 = Date.parse(fecha1)
      let d2 = Date.parse(fecha2)
             // Obtiene el valor absoluto de la diferencia
      let dateDiffer = Math.abs(d1 - d2)
             // La diferencia es milisegundos convertidos a días
      let differDay = Math.floor(dateDiffer / (24 * 3600 * 1000))
      differDay=differDay+1
      
      let fecha = new Date(fecha1);
      fecha.setMinutes(fecha.getMinutes() + fecha.getTimezoneOffset());
        var anioactual = fecha.getFullYear();
        var diaactual = fecha.getDate();
        var mesactual = fecha.getMonth()+1;
        
      if(es_bisiesto(anioactual)==true){
        if(mesactual==2){
            differDay=differDay+1;
        }
      }
      if(es_bisiesto(anioactual)==false){
        if(mesactual==2){
            differDay= differDay+2;
        }
      } 
      document.getElementById("diasreser").value=differDay-1;
}
function calculosueldo(){
    document.getElementById("sueldo").value=(Number(document.getElementById("dias").value)*Number(document.getElementById("sueldod").value)).toFixed(2);
    porcentaje();
}
function sumaingresos() {


    document.getElementById("Sbingresos").value=(
    Number(document.getElementById("sueldo").value)
    +Number(document.getElementById("vacaciones").value)
    +Number(document.getElementById("extras").value)
    +Number(document.getElementById("horas_suplementarias").value)
    +Number(document.getElementById("transporte").value)
    +Number(document.getElementById("otros_boni").value)
    +Number(document.getElementById("otrosin").value)).toFixed(2);
}
function sumaegresos() {
   //   alert(document.getElementById("TotalIngresosV").innerHTML );

    document.getElementById("Sbegresos").value=(
    Number(document.getElementById("Salud").value)
    +Number(document.getElementById("PPQQ").value)
    +Number(document.getElementById("Hipotecarios").value)
    +Number(document.getElementById("Prestamos").value)
    +Number(document.getElementById("Multas").value)
    +Number(document.getElementById("Otro_Egresos").value)
    +Number(document.getElementById("Ley_salud").value)).toFixed(2);
}
function resetearCampos() {
        document.getElementById("sueldo").value= (Number(document.getElementById("sueldod").value)).toFixed(2);
        document.getElementById("dias").value= 1;
        document.getElementById("vacaciones").value= "0.00"
        document.getElementById("extras").value= "0.00"
        document.getElementById("horas_suplementarias").value= "0.00"
        document.getElementById("transporte").value= "0.00"
        document.getElementById("otros_boni").value= "0.00"
        document.getElementById("otrosin").value= "0.00"
        document.getElementById("Sbegresos").value= "0.00"
        document.getElementById("Sbingresos").value= "0.00"

        document.getElementById("Salud").value= "0.00"
        document.getElementById("PPQQ").value= "0.00"
        document.getElementById("Hipotecarios").value= "0.00"
        document.getElementById("Prestamos").value= "0.00"
        document.getElementById("Multas").value= "0.00"
        document.getElementById("Otro_Egresos").value= "0.00"
        document.getElementById("Ley_salud").value= "0.00"
        
   

}
function porcentaje() {
    document.getElementById("sueldo").value= (((Number(document.getElementById("dias").value)*Number(document.getElementById("sueldod").value))*Number(document.getElementById("idTipo").value))/100).toFixed(2);
    sumaingresos();
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
function calculototales(){
        var sumegresos=0;
       var sumingresos=0;
       var Beneficios=0;
       var descuentos=0;
       var diast=0;
       var totalsueldo=0;
       var totalvacaciones=0;
       
        
        $("input[type='checkbox'][id='check']").each(function(){        
                if (this.checked) {
                    descuentos+=Number(document.getElementById('Descontar'+$(this).val()).value);
                
                }
            });
        
        document.getElementById("TotalAdelantosV").innerHTML=round(descuentos);
        document.getElementById("adelanto").value=round(descuentos);
      
       for (let step = 1; step < document.getElementById("tabla").rows.length; step++) {
          
         sumingresos+=Number($("input[name='TTingresos[]']")[step].value);
         sumegresos+=Number($("input[name='totalegre[]']")[step].value);
         Beneficios+=(Number($("input[name='Textras[]']")[step].value)+ Number($("input[name='Thoras_suplementarias[]']")[step].value)+Number($("input[name='Totrosbon[]']")[step].value));
         diast+=Number($("input[name='Tdias[]']")[step].value);
         totalsueldo+=Number($("input[name='TCSueldo[]']")[step].value);
         totalvacaciones=Number(document.getElementById("Vac_pagadas").value);
        }
        
        if(document.getElementById("afiliadot").value=="1"){
            if (document.getElementById("IessGerencial").value=="1") {
                
                document.getElementById("TotalIess").innerHTML=((((diast*(Number(document.getElementById("sueldod").value)))+Beneficios)*Number(document.getElementById("Gerencialt").value))/100).toFixed(2);
                document.getElementById("Totalpersonal").value=((((diast*(Number(document.getElementById("sueldod").value)))+Beneficios)*Number(document.getElementById("Gerencialt").value))/100).toFixed(2);
            }
            else{
                if(document.getElementById("asumidot").value=="1"){
                    document.getElementById("TotalAsumido").innerHTML=((((diast*(Number(document.getElementById("sueldod").value)))+Beneficios)*Number(document.getElementById("Personalt").value))/100).toFixed(2);
                }
                else{
                    document.getElementById("TotalIess").innerHTML=((((diast*(Number(document.getElementById("sueldod").value)))+Beneficios)*Number(document.getElementById("Personalt").value))/100).toFixed(2);
                }
                
                document.getElementById("Totalpersonal").value=((((diast*(Number(document.getElementById("sueldod").value)))+Beneficios)*Number(document.getElementById("Personalt").value))/100).toFixed(2);
                document.getElementById("Totalpatronal").value=((((diast*(Number(document.getElementById("sueldod").value)))+Beneficios)*Number(document.getElementById("Patronalt").value))/100).toFixed(2);
            }
            document.getElementById("TotalIESCAP").value=((((diast*(Number(document.getElementById("sueldod").value)))+Beneficios)*Number(document.getElementById("IESCAP").value))/100).toFixed(2);
            
            document.getElementById("LVac_pagadas").innerHTML=(totalvacaciones).toFixed(2);
            if(document.getElementById("tercerot").value=="1"){
                document.getElementById("TotalTerceroV").innerHTML=(((diast*(Number(document.getElementById("sueldod").value)))+Beneficios)/12).toFixed(2);
                document.getElementById("TTerceroacu").value=0;
        
            }else{
                document.getElementById("TotalTerceroV").innerHTML=(0).toFixed(2);
                document.getElementById("TTerceroacu").value=(((diast*(Number(document.getElementById("sueldod").value)))+Beneficios)/12).toFixed(2);
            }
            
            if(document.getElementById("cuartot").value=="1"){
                document.getElementById("TotalCuartoV").innerHTML=(diast*(Number(document.getElementById("Basicot").value)/360)).toFixed(2);
                document.getElementById("TCuartoacu").value=0;
            }
            else{
                document.getElementById("TotalCuartoV").innerHTML=(0).toFixed(2);
                document.getElementById("TCuartoacu").value=(diast*(Number(document.getElementById("Basicot").value)/360)).toFixed(2);
            }
            
                       
            if ( document.getElementById("FechaReservat").value <= document.getElementById("fechafinal").value) {       
               
                if(mes(document.getElementById("FechaReservat").value)==mes(document.getElementById("fechafinal").value)){
                    
                    document.getElementById("calreser").value='1'; 
                    dateDiffer2(document.getElementById("FechaReservat").value,document.getElementById("fechafinal").value);
                }
                if(document.getElementById("acureservat").value=="0"){
                   
                    if(document.getElementById("calreser").value=="1"){
                      
                        document.getElementById("TotalFondosV").innerHTML=round((((((diast*(Number(document.getElementById("sueldod").value)))+Beneficios)*Number(document.getElementById("VReservat").value))/100)/30)*Number(document.getElementById("diasreser").value));
                       
                    }
                    else{
                       
                        document.getElementById("TotalFondosV").innerHTML=((((diast*(Number(document.getElementById("sueldod").value)))+Beneficios)*Number(document.getElementById("VReservat").value))/100).toFixed(2);
                    }
                }
                if(document.getElementById("acureservat").value=="1"){
                   
                    if(document.getElementById("calreser").value=="1"){
                        document.getElementById("TFondoacu").innerHTML=round((Number(document.getElementById("sueldod").value)*Number(document.getElementById("VReservat").value))/100);
                    }
                    else{
                        document.getElementById("TFondoacu").value=((((diast*(Number(document.getElementById("sueldod").value)))+Beneficios)*Number(document.getElementById("VReservat").value))/100).toFixed(2); 
                    }
                   
                }
            }   
              
           
            
        }
        document.getElementById("Vac_pagadas").value=Number(document.getElementById("LVac_pagadas").innerHTML);
        document.getElementById("TFondo").value=Number(document.getElementById("TotalFondosV").innerHTML);
        document.getElementById("TTercero").value=Number(document.getElementById("TotalTerceroV").innerHTML);
        document.getElementById("TCuarto").value=Number(document.getElementById("TotalCuartoV").innerHTML);
        document.getElementById("Tiess").value=Number(document.getElementById("TotalIess").innerHTML);
        document.getElementById("Tasumido").value=Number(document.getElementById("TotalAsumido").innerHTML);
        
      

        document.getElementById("Totaldias").value=diast;
        document.getElementById("Totalsueldo").value=totalsueldo;

        document.getElementById("TotalIngresosV").innerHTML=(sumingresos).toFixed(2);
        document.getElementById("TIngresos").value=sumingresos;

        document.getElementById("TotalEgresos").innerHTML=(sumegresos).toFixed(2);
        document.getElementById("TEgresos").value=sumegresos;
        if (document.getElementById("impu_rentat").value=="1") {
            impuestorentacalculo(diast*(Number(document.getElementById("sueldod").value).toFixed(2)));
        }
        document.getElementById("TIngreEgreV").innerHTML=(sumingresos-sumegresos).toFixed(2);
        document.getElementById("Tquincena").innerHTML=(Number(document.getElementById("vquincena").value)).toFixed(2);
        sumatotales();
        
 
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
        calculototales(); 
}
function SumaAdelantos(id) {
    if(Number($("input[name='TDescontar[]']")[id].value)<=Number($("input[name='TSaldo[]']")[id].value)) {           
                
    var nuevo=0; 
    var liquidacion=Number(document.getElementById("TIngreEgreV").innerHTML)
        +Number(document.getElementById("TotalFondosV").innerHTML)
        +Number(document.getElementById("TotalTerceroV").innerHTML)
        +Number(document.getElementById("TotalCuartoV").innerHTML)
        -Number(document.getElementById("LVac_pagadas").innerHTML)
        -Number(document.getElementById("TotalAlimentacionV").innerHTML)
        -Number(document.getElementById("TotalIess").innerHTML)
        -Number(document.getElementById("TotalAsumido").innerHTML)
        -Number(document.getElementById("Tquincena").innerHTML)
        -Number(document.getElementById("TotalRent").innerHTML);
    $("input[type='checkbox'][id='check']").each(function(){        
            if (this.checked) {   
                    nuevo+=Number(document.getElementById('Descontar'+$(this).val()).value);
                
                    if (liquidacion<Number(nuevo)) {
                    
                        $("input[name='TDescontar[]']")[id].value=0.00;
                        $("input[name='TDescont[]']")[id].value=0.00;
                    }
                
                    else{
                        $("input[name='TDescont[]']")[id].value=$("input[name='TDescontar[]']")[id].value;
                        document.getElementById("TotalAdelantosV").innerHTML=nuevo;  
                    }
               
            }
        });
    }
    else{
        $("input[name='TDescontar[]']")[id].value=0.00;
        $("input[name='TDescont[]']")[id].value=0.00;
    }
       calculototales();

}
function ExtraerQuincena(id) {
    $.ajax({
        url: '{{ url("buscarquincena/searchN") }}'+'/'+id,
        dataType: "json",
        type: "GET",
        data: {
            ide: id
        },
        success: function(data){
          
            for (var i=0; i<data.length; i++) {
          
                    document.getElementById("vquincena").value=(Number(data[i].quincena_valor)).toFixed(2); 
                    document.getElementById("idquincena").value=data[i].quincena_id;     
            }                  
           
        },
    });
       

}
function ExtraerVacaciones(id) {
    $.ajax({
        url: '{{ url("buscarvacaciones/searchN") }}'+ '/' +id,
        dataType: "json",
        type: "GET",
        data: {
            ide: id
        },
        success: function(data){
         
            for (var i=0; i<data.length; i++) {
                     document.getElementById("Vac_pagadas").value=(Number(data[i].vacacion_valor)).toFixed(2); 
                     $('#vacaciones').attr('readonly', true);  
                   // document.getElementById("vvacaciones").value=(Number(data[i].vacacion_valor)).toFixed(2); 
                    document.getElementById("idvacacion").value=data[i].vacacion_id;  
                            
            }                  
           
        },
    });
       

}
function sumatotales(){
    
            document.getElementById("LiquidacionTotal").innerHTML=(Number(document.getElementById("TIngreEgreV").innerHTML)
            +Number(document.getElementById("TotalFondosV").innerHTML)
            +Number(document.getElementById("TotalTerceroV").innerHTML)
            +Number(document.getElementById("TotalCuartoV").innerHTML)
            -Number(document.getElementById("TotalAdelantosV").innerHTML)
            -Number(document.getElementById("Tquincena").innerHTML)
            -Number(document.getElementById("TotalAlimentacionV").innerHTML)
            -Number(document.getElementById("TotalIess").innerHTML)
            -Number(document.getElementById("LVac_pagadas").innerHTML)
            -Number(document.getElementById("TotalAsumido").innerHTML)
            -Number(document.getElementById("TotalRent").innerHTML)).toFixed(2);
            document.getElementById("Liquidacion").value=Number(document.getElementById("LiquidacionTotal").innerHTML);
           

}
function impuestorentacalculo(sueldo){
    $.ajax({
        url: '{{ url("buscarimpuestorenta/searchN") }}',
        dataType: "json",
        type: "GET",
        data: { 
        },
        success: function(data){
          
            for (var i=0; i<data.length; i++) {
                if (((sueldo*12) >= data[i].impuesto_fraccion_basica) && ((sueldo*12) < data[i].impuesto_exceso_hasta)) {
                          
                    document.getElementById("TotalRent").innerHTML=(((((Number(data[i].impuesto_exceso_hasta)-(sueldo*12))*Number(data[i].impuesto_sobre_fraccion))/100)+Number(data[i].impuesto_fraccion_excede))/12).toFixed(2);
                    document.getElementById("Trenta").value=(((((Number(data[i].impuesto_exceso_hasta)-(sueldo*12))*Number(data[i].impuesto_sobre_fraccion))/100)+Number(data[i].impuesto_fraccion_excede))/12).toFixed(2);
                           
                }
                
                
            }
            sumatotales(); 
        },
    });

}
</script>
@endsection