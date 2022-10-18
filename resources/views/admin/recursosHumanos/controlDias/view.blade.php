@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-primary ">
       
        <div class="row">
            <!-- Tabla de empelados -->
            <div class="col-sm-2">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Empleados</h3>
                    </div>
                    <div class="card-body" >
                        <table id="exampleBuscar" class="table table-hover table-responsive">
                            <thead class="invisible">
                                <tr class="text-center">
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                              
                                <tr>
                                    <td>
                                        {{$control->empleado->empleado_nombre}}
                                    </td>
                                </tr>
                              
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
            <!-- Tabla de detalles -->
            <div class="col-sm-10">
                <div  class="row">  
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="row clearfix form-horizontal">
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                                <label>NUMERO</label>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group">
                                    <div class="form-line">
                                        
                                        <label class="form-control">{{$control->control_numero}} </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <div class="float-right">
                                  
                                      
                                        
                                        <button type="button" onclick='window.location = "{{ url("listacontroldia") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                        
                                </div>
                   
                            </div>
                        
                        </div>  
                    </div> 
                </div> 
                <div id="ulprueba" class="row">  
                <!-- Tabla de ingresos -->
                    <div  class="col-md-4">
                        <br>
                        <div class="card card-secondary">  
                            <div class="card-header">
                                <h3 class="card-title "> Mes y Año
                                </h3>
                                
                            </div>
                            
                            <div class="card-body " >  
                                <div class="row clearfix form-horizontal">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 form-control-label  "
                                       >
                                        <label >Mes y año</label>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" >  
                                        <div class="form-group">
                                            <div class="form-line">
                                            <label class="form-control">{{$control->control_mes}} {{$control->control_ano}} </label>
                                            
                                        </div>
                                        </div> 
                                    </div> 
                                   
                                </div> 
                                                       
                            </div>      
                        </div>
                    </div>
                    <div  class="col-md-8">
                        <br>
                      
                    </div>
                    <div  class="col-md-12">  
                        <div class="card">       
                            <div class="card-body table-responsive p-0" style="height: 150px;" >       
                                <table id="tabla" class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th class="text-center">AÑO</th>
                                            <th class="text-center">MES</th>
                                         
                                                @for ($j = 1; $j <= 31; ++$j) 
                                                    <?php  $titulo='titulo'.$j;?>            
                                                <th class="text-center">{{$detalle[1][$titulo]}}</th>
                                                @endfor                       
                                           
                                           

                                        </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="text-center">
                                    <td class="text-center">{{ $anio }} </td>
                                    <td class="text-center">{{ $mes }}</td>
                                        @for ($j = 1; $j <= 31; ++$j) 
                                            <?php  $titulo='valor'.$j;?>            
                                        <td class="text-center"><input type="text" class="form-controltext2" @if($detalle[1][$titulo]=='T') style="background-color: #28a745; color: #fff;" @ENDIF @if($detalle[1][$titulo]=='D') style="background-color: #ffc107; color: #fff;" @ENDIF @if($detalle[1][$titulo]=='V') style="background-color: #17a2b8; color: #fff;" @ENDIF @if($detalle[1][$titulo]=='P') style="background-color: #007bff; color: #fff;" @ENDIF @if($detalle[1][$titulo]=='A') style="background-color: #6c757d; color: #fff;" @ENDIF @if($detalle[1][$titulo]=='C') style="background-color: #fd7e14; color: #fff;" @ENDIF @if($detalle[1][$titulo]=='X') style="background-color: #dc3545; color: #fff;" @ENDIF value="@if($detalle[1][$titulo]!=0) {{$detalle[1][$titulo]}} @endif" readonly/></td>
                                        @endfor   
                                    </tr>   
                                    </tbody>
                                </table>
                            </div> 
                        </div>   
                    </div> 
                    <div class="col-md-6">
                    </div>   
                    <div class="col-md-3">
                        <table  class="table table-head-fixed text-nowrap">
                            <thead >
                                <tr class="text-center">
                                    <th>Tipo</th>
                                    <th> Simbolo</th>
                                    <th>Color</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr>
                                    <td class="filaDelgada20"> Dia Normal                                 
                                    </td>
                                    <td class="filaDelgada20 text-center"> T                                
                                    </td>
                                    <td class="filaDelgada20 bg-success color-palette">                          
                                    </td>
                                </tr>
                                <tr>
                                    <td class="filaDelgada20"> Descanso                                
                                    </td>
                                    <td class="filaDelgada20 text-center"> D                               
                                    </td>
                                    <td class="filaDelgada20 bg-warning  color-palette">                        
                                    </td>
                                </tr>
                                <tr>
                                    <td class="filaDelgada20"> Vacaciones                                 
                                    </td>
                                    <td class="filaDelgada20 text-center"> V                               
                                    </td>
                                    <td class="filaDelgada20 bg-info color-palette">          
                                    </td>
                                </tr>
                                <tr>
                                    <td class="filaDelgada20"> Permisos                                 
                                    </td>
                                    <td class="filaDelgada20 text-center"> P                               
                                    </td>
                                    <td class="filaDelgada20 bg-primary color-palette">             
                                    </td>
                                </tr>
                                <tr>
                                    <td class="filaDelgada20"> Ausente                                
                                    </td>
                                    <td class="filaDelgada20 text-center"> A                               
                                    </td>
                                    <td class="filaDelgada20 bg-gray color-palette">                            
                                    </td>
                                </tr>
                                <tr>
                                    <td class="filaDelgada20"> Cosecha                                 
                                    </td>
                                    <td class="filaDelgada20 text-center"> C                                
                                    </td>
                                    <td class="filaDelgada20 bg-orange color-palette">                             
                                    </td>
                                </tr>
                                <tr>
                                    <td class="filaDelgada20"> Dia Extra                                
                                    </td>
                                    <td class="filaDelgada20 text-center"> X                                 
                                    </td>
                                    <td class="filaDelgada20 bg-danger color-palette">                     
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div> 
                    <div class="col-md-3">
                        <div class="card card-primary">  
                            <table class="table table-totalVenta">
                                <tr>
                                    <th  class="text-center-encabesado letra-blanca fondo-gris-oscuro negrita" colspan="2">Totales </th>
                                </tr>
                                <tr>
                                    <td  class="letra-blanca fondo-gris-oscuro negrita" width="90">Total Dia Normal
                                    </td>
                                    <td id="Totalt" name="Totalt" width="100" class="derecha-texto negrita">{{$control->control_normal}}
                                    </td>
                                    <input type="hidden"   name="ndias"  id="ndias" value="0" required readonly> 
                                    <input type="hidden"   name="DTotalt"  id="DTotalt" value="0" >                                 
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-gris-oscuro negrita">Total Descanso</td>
                                    <td id="Totald"  name="Totald" class="derecha-texto negrita">{{$control->control_decanso}}</td>
                                    <input type="hidden"   name="mes"  id="mes" value="0" required readonly>
                                    <input type="hidden"   name="DTotald"  id="DTotald" value="0" >     
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-gris-oscuro negrita">Total Vacaciones
                                    </td>
                                    <td id="Totalv" name="Totalv" class="derecha-texto negrita">{{$control->control_vacaciones}}</td>
                                    <input type="hidden"   name="anio"  id="anio" value="0" required readonly>
                                    <input type="hidden"   name="DTotalv"  id="DTotalv" value="0" >     
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-gris-oscuro negrita">Total Permisos</td>
                                    <td id="Totalp"  name="Totalp" class="derecha-texto negrita">{{$control->control_permiso}}</td>
                                    <input type="hidden"   name="fecha"  id="fecha" value="0" required readonly> 
                                    <input type="hidden"   name="DTotalp"  id="DTotalp" value="0" >                 
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-gris-oscuro negrita">Total Ausente</td>
                                    <td id="Totala"  name="Totala" class="derecha-texto negrita">{{$control->control_ausente}}</td>
                                    <input type="hidden"   name="empleado_id"  id="empleado_id" value="0" required readonly>
                                    <input type="hidden"   name="DTotala"  id="DTotala" value="0" >        
                                  
                                <tr>
                                    <td class="letra-blanca fondo-gris-oscuro negrita">Total Cosecha</td>
                                    <td id="Totalc"  name="Totalc" class="derecha-texto negrita">{{$control->control_cosecha}}</td>
                                    <input type="hidden"   name="DTotalc"  id="DTotalc" value="0" >     
                                   
                                </tr>
                                <tr>
                                    <td  class="letra-blanca fondo-gris-oscuro negrita">Total Dia Extra
                                    </td>
                                    <td id="Totalx"  name="Totalx" class="derecha-texto negrita">{{$control->control_extra}}</td>
                                    <input type="hidden"   name="DTotalx"  id="DTotalx" value="0" >     
                                  
                                                
                                </tr>
                               
                            </table>
                        </div>   
                    </div>
                </div>
            </div>
        </div>
        
        

</div>


@endsection