@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="/historialClinico">
@csrf
<div class="card card-secondary">
    <div class="card-header">
        <div class="float-right">
            <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
            <button onclick="window.location = '/historialClinico';" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">         
            <div class="col-sm-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="datosPaciente-tab" data-toggle="tab" href="#datosPaciente" role="tab" aria-controls="datosPaciente" aria-selected="true">Datos del Paciente</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="parametros-tab" data-toggle="tab" href="#parametros" role="tab" aria-controls="parametros" aria-selected="true">Parámetros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="bancoArchivos-tab" data-toggle="tab" href="#bancoArchivos" role="tab" aria-controls="bancoArchivos" aria-selected="false">Banco de Archivos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="historial-tab" data-toggle="tab" href="#historial" role="tab" aria-controls="historial" aria-selected="false">Historial</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="atencion-tab" data-toggle="tab" href="#atencion" role="tab" aria-controls="atencion" aria-selected="false">Atención</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="hallazgos-tab" data-toggle="tab" href="#hallazgos" role="tab" aria-controls="hallazgos" aria-selected="false">Hallazgos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="documentos-tab" data-toggle="tab" href="#documentos" role="tab" aria-controls="documentos" aria-selected="false">Documentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="resultados-tab" data-toggle="tab" href="#resultados" role="tab" aria-controls="resultados" aria-selected="false">Resultados</a>
                    </li>
                </ul>
                <br>
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title negrita">
                            {{$paciente->paciente_apellidos}}  {{$paciente->paciente_nombres}}
                        </h3>
                        <div class="float-right">
                            <h3 class="card-title negrita" >
                                <input type="text" class="negrita" name="edadPaciente"  id="edadPaciente" style="background: transparent; border: none; color:white;">
                            </h3>
                            <script>
                                var edadPaciente = edad('{{$paciente->paciente_fecha_nacimiento}}');
                                document.getElementById("edadPaciente").value = edadPaciente;
                            </script>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="well listview-pagupa">
                        <div class="tab-content" id="myTabContent">
                                <!-- /.tab-contentt -->
                            <!--Datos del Paciente-->
                            <div class="tab-pane fade show active" id="datosPaciente" role="tabpanel" aria-labelledby="datosPaciente-tab"> 

                                <div class="row">
                                <a>&nbsp;&nbsp;&nbsp;</a>
                                    <div class="card col-5">
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <label for="identificacion" class="col-sm-3 col-form-label">*Identificación:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="{{$paciente->tipo_identificacion_nombre}}" readonly>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="cedula" class="col-sm-3 col-form-label">N°:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="{{$paciente->paciente_cedula}}" readonly>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="nombres" class="col-sm-3 col-form-label">*Nombres:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="{{ $paciente->paciente_nombres}}" readonly>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="apellidos" class="col-sm-3 col-form-label">*Apellidos:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="{{ $paciente->paciente_apellidos}}" readonly>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="nacimiento" class="col-sm-3 col-form-label">*Nacimiento:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="{{ $paciente->paciente_fecha_nacimiento}}" readonly>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="sexo" class="col-sm-3 col-form-label">*Sexo:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="{{ $paciente->paciente_sexo}}" readonly>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="estadoCivil" class="col-sm-3 col-form-label">*Estado civil:</label>
                                                <div class="col-sm-6">
                                                    <select id="sucursal" name="sucursal" class="form-control show-tick " data-live-search="true">
                                                        <option value="">--No se conoce--</option>
                                                        <option value="SOLTERO/A">SOLTERO/A</option>
                                                        <option value="UNION DE HECHO">UNION DE HECHO</option>
                                                        <option value="CASADO/A">CASADO/A</option>
                                                        <option value="DIVORCIADO/A">DIVORCIADO/A</option>
                                                        <option value="VUIDO/A">VUIDO/A</option>
                                                    </select>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="educacion" class="col-sm-3 col-form-label">*Educación:</label>
                                                <div class="col-sm-6">
                                                    <select id="sucursal" name="sucursal" class="form-control show-tick " data-live-search="true">
                                                        <option value="">--No especifico--</option>
                                                        <option value="BASICA">BASICA</option>
                                                        <option value="SECUNDARIA">SECUNDARIA</option>
                                                        <option value="UNIVERSIDAD">UNIVERSIDAD</option>
                                                        <option value="POSGRADO">POSGRADO</option>
                                                    </select>
                                                </div>
                                            </div>  
                                            <div class="form-group row">
                                                <label for="identificacion" class="col-sm-3 col-form-label">*Provincia:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="{{$paciente->provincia_nombre}}" readonly>
                                                </div>
                                            </div>  
                                            <div class="form-group row">
                                                <label for="identificacion" class="col-sm-3 col-form-label">*Ciudad:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="{{$paciente->ciudad_nombre}}" readonly>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="categoria" class="col-sm-3 col-form-label">Categoria:</label>
                                                <div class="col-sm-6">
                                                    <select id="sucursal" name="sucursal" class="form-control show-tick " data-live-search="true">
                                                        <option value="">--NINGUNO--</option>
                                                    </select>
                                                </div>
                                            </div>  
                                            <div class="form-group row">
                                                <label for="profesion" class="col-sm-3 col-form-label">Profesión:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="" readonly>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="profesion" class="col-sm-3 col-form-label">*Ingreso:</label>
                                                <div class="col-sm-6">
                                                    <input type="date" class="form-control" value="" readonly>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="profesion" class="col-sm-3 col-form-label">(Crear) Cliente:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="" readonly>
                                                </div>
                                            </div>                                     
                                            <div class="form-group row">
                                                <label for="profesion" class="col-sm-3 col-form-label">Dirección:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="{{$paciente->paciente_direccion}}" readonly>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="telefonos" class="col-sm-3 col-form-label">Teléfonos:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="" readonly>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="celular" class="col-sm-3 col-form-label">*Celular:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="{{$paciente->paciente_celular}}" readonly>
                                                </div>
                                            </div>                                     
                                            <div class="form-group row">
                                                <label for="email" class="col-sm-3 col-form-label">*E-mail:</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" value="{{$paciente->paciente_email}}" readonly>
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <label for="seguro" class="col-sm-3 col-form-label">Seguro:</label>
                                                <div class="col-sm-6">
                                                    <select id="sucursal" name="sucursal" class="form-control show-tick " data-live-search="true">
                                                        <option value="">--NINGUNO--</option>
                                                    </select>
                                                </div>
                                            </div>  
                                            <div class="form-group row">
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col-form-label" style="margin-bottom : 0px;">
                                                    <div class="demo-checkbox">
                                                        <input type="radio" value="Ambulatorio" id="tipo" class="with-gap radio-col-deep-orange" name="tipo" checked required />
                                                        <label class="form-check-label" for="check1">Ambulatorio</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col-form-label" style="margin-bottom : 0px;">
                                                    <div class="demo-checkbox">
                                                        <input type="radio" value="Hospitalario" id="tipo" class="with-gap radio-col-deep-orange" name="tipo" required />
                                                        <label class="form-check-label" for="check1">Hospitalario</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                    <div class="card col-6">
                                        <div class="card-body">
                                            <table class="table table-hover">
                                                <tbody>
                                                    <tr data-widget="expandable-table" aria-expanded="false">
                                                        <td>
                                                            <i class="fas fa-angle-left right"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="nav-icon "></i>&nbsp;&nbsp;A) ALERTAS
                                                        </td>
                                                    </tr>
                                                    <tr class="expandable-body">
                                                        <td>
                                                            <div class="p-0">
                                                                <table class="table table-hover">
                                                                    <tbody>
                                                                        <br>
                                                                        <div class="form-group row">   
                                                                            <label for="alerta1" class="col-sm-3 col-form-label">Alerta 1:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="alerta1" name="alerta1"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="alerta2" class="col-sm-3 col-form-label">Alerta 2:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="alerta2" name="alerta2"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="alerta3" class="col-sm-3 col-form-label">Alerta 3:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="alerta3" name="alerta3"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr data-widget="expandable-table" aria-expanded="false">
                                                        <td>
                                                            <i class="fas fa-angle-left right"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="nav-icon "></i>&nbsp;&nbsp;B) ANTECEDENTES HEREDO FAMILIARES
                                                        </td>
                                                    </tr>
                                                    <tr class="expandable-body">
                                                        <td>
                                                            <div class="p-2">
                                                                <table class="table table-hover">
                                                                    <tbody>           
                                                                        <br>    
                                                                        <div class="form-group row">   
                                                                            <label for="diabetes" class="col-sm-3 col-form-label">Diabetes:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="diabetes" name="diabetes"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="hipertension" class="col-sm-3 col-form-label">Hipertensión:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="hipertension" name="hipertension"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="cardiopatia" class="col-sm-3 col-form-label">Cardiopatía:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="cardiopatia" name="cardiopatia"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="hepatopatia" class="col-sm-3 col-form-label">Hepatopatía:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="hepatopatia" name="hepatopatia"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="nefropatia" class="col-sm-3 col-form-label">Nefropatía:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="nefropatia" name="nefropatia"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="enfMental" class="col-sm-3 col-form-label">Enf. Mentales:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="enfMental" name="enfMental"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="asma" class="col-sm-3 col-form-label">Asma:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="asma" name="asma"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="osteoarticulares" class="col-sm-3 col-form-label">Osteoarticulares:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="osteoarticulares" name="osteoarticulares"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="enfAlergicas" class="col-sm-3 col-form-label">Enf. Alérgicas:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="enfAlergicas" name="enfAlergicas"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="enfEndocrinas" class="col-sm-3 col-form-label">Enf. Endócrinas:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="enfEndocrinas" name="enfEndocrinas"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="neoplasias" class="col-sm-3 col-form-label">Neoplasias:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="neoplasias" name="neoplasias"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">   
                                                                            <label for="otrosHeredo" class="col-sm-3 col-form-label">Otros:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="otrosHeredo" name="otrosHeredo"  placeholder="">                                                                        
                                                                            </div>
                                                                        </div>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr data-widget="expandable-table" aria-expanded="false">
                                                        <td>
                                                            <i class="fas fa-angle-left right"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="nav-icon "></i>&nbsp;&nbsp;C) ANTECEDENTES PERSONALES PATOLOGICOS
                                                        </td>
                                                    </tr>
                                                    <tr class="expandable-body">
                                                        <td>
                                                            <div class="p-0">
                                                                <table class="table table-hover">
                                                                    <tbody>           
                                                                    <br>    
                                                                        <div class="form-group row">
                                                                            <label for="quirurgicos" class="col-sm-3 col-form-label">Quírurgicos</label>
                                                                            <div class="col-sm-9">                                
                                                                                <textarea type="text" class="form-control" id="quirurgicos" name="aquirurgicos"  placeholder="Quírurgicos"></textarea>
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="transfusion" class="col-sm-3 col-form-label">Transfusión</label>
                                                                            <div class="col-sm-9">                                
                                                                                <textarea type="text" class="form-control" id="transfusion" name="transfusion"  placeholder="Transfusión"></textarea>
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="alergias" class="col-sm-3 col-form-label">Alergias</label>
                                                                            <div class="col-sm-9">                                
                                                                                <textarea type="text" class="form-control" id="alergias" name="alergias"  placeholder="Alergias"></textarea>
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="traumaticos" class="col-sm-3 col-form-label">Traumáticos</label>
                                                                            <div class="col-sm-9">                                
                                                                                <textarea type="text" class="form-control" id="traumaticos" name="traumaticos"  placeholder="Traumáticos"></textarea>
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="hospitalizacionesPrevias" class="col-sm-3 col-form-label">Hospitalizaciones Previas</label>
                                                                            <div class="col-sm-9">                                
                                                                                <textarea type="text" class="form-control" id="hospitalizacionesPrevias" name="hospitalizacionesPrevias"  placeholder="Hospitalizaciones Previas"></textarea>
                                                                            </div> 
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label for="adicciones" class="col-sm-3 col-form-label">Adicciones</label>
                                                                            <div class="col-sm-9">                                
                                                                                <textarea type="text" class="form-control" id="adicciones" name="adicciones"  placeholder="Adicciones"></textarea>
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="digestivas" class="col-sm-3 col-form-label">Digestivas</label>
                                                                            <div class="col-sm-9">                                
                                                                                <textarea type="text" class="form-control" id="digestivas" name="digestivas"  placeholder="Digestivas"></textarea>
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="otrosPatologicos" class="col-sm-3 col-form-label">Otros</label>
                                                                            <div class="col-sm-9">                                
                                                                                <textarea type="text" class="form-control" id="otrosPatologicos" name="otrosPatologicos"  placeholder="Otros"></textarea>
                                                                            </div> 
                                                                        </div> 
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr data-widget="expandable-table" aria-expanded="false">
                                                        <td>
                                                            <i class="fas fa-angle-left right"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="nav-icon "></i>&nbsp;&nbsp;D) ANTECEDENTES PERSONALES NO PATOLOGICOS
                                                        </td>
                                                    </tr>
                                                    <tr class="expandable-body">
                                                        <td>
                                                            <div class="p-0">
                                                                <table class="table table-hover">
                                                                    <tbody>           
                                                                    <br>  
                                                                        <div class="form-group row">
                                                                            <label for="tipoSangre" class="col-sm-3 col-form-label">Tipo de Sangre</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="tipoSangre" name="tipoSangre">
                                                                            </div> 
                                                                        </div>  
                                                                        <div class="form-group row">
                                                                            <label for="tabaquismo" class="col-sm-3 col-form-label">Tanaquismo (cig/día)</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="tabaquismo" name="tabaquismo">
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="alimentacion" class="col-sm-3 col-form-label">Alimentación (veces/día)</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="alimentacion" name="alimentacion">
                                                                            </div> 
                                                                        </div>  
                                                                        <div class="form-group row">
                                                                            <label for="actividadFisica" class="col-sm-3 col-form-label">Actividad Fisíca</label>
                                                                            <div class="col-sm-9">                                
                                                                            <textarea type="text" class="form-control" id="actividadFisica" name="actividadFisica"  placeholder="Actividad Fisíca"></textarea>
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="inmunizaciones" class="col-sm-3 col-form-label">Inmunizaciones</label>
                                                                            <div class="col-sm-9">                                
                                                                            <textarea type="text" class="form-control" id="inmunizaciones" name="inmunizaciones"  placeholder="Inmunizaciones"></textarea>
                                                                            </div> 
                                                                        </div>  
                                                                        <div class="form-group row">
                                                                            <label for="vivienda" class="col-sm-3 col-form-label">Vivienda</label>
                                                                            <div class="col-sm-9">                                
                                                                                <textarea type="text" class="form-control" id="vivienda" name="vivienda"  placeholder="Vivienda"></textarea>
                                                                            </div> 
                                                                        </div>  
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr data-widget="expandable-table" aria-expanded="false">
                                                        <td>
                                                            <i class="fas fa-angle-left right"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="nav-icon "></i>&nbsp;&nbsp;E) GINECO - OBTETRICOS
                                                        </td>
                                                    </tr>
                                                    <tr class="expandable-body">
                                                        <td>
                                                            <div class="p-0">
                                                                <table class="table table-hover">
                                                                    <tbody>           
                                                                    <br>    
                                                                        <div class="form-group row">
                                                                            <label for="menarquia" class="col-sm-3 col-form-label">Menarquía</label>
                                                                            <div class="col-sm-9">                                
                                                                                <textarea type="text" class="form-control" id="menarquia" name="menarquia"  placeholder="Menarquía"></textarea>
                                                                            </div>
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="ritmoMenstrual" class="col-sm-3 col-form-label">Ritmo Menstrual</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="ritmoMenstrual" name="ritmoMenstrual">
                                                                            </div> 
                                                                        </div>  
                                                                        <div class="form-group row">
                                                                            <label for="ctrl" class="col-sm-3 col-form-label">Ctrl <br> (P)arto/(C)esárea</label>
                                                                            <div class="col-sm-9">                                
                                                                                <select id="ctrl" name="ctrl" class="form-control show-tick " data-live-search="true">
                                                                                    <option value="">--Seleccione una opcion--</option>
                                                                                    <option value="Parto">Parto</option>
                                                                                    <option value="Cesárea">Cesárea</option>
                                                                                </select>
                                                                            </div> 
                                                                        </div>  
                                                                        <div class="form-group row">
                                                                            <label for="fpp" class="col-sm-3 col-form-label">FPP</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="fpp" name="fpp">
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="gestaciones" class="col-sm-3 col-form-label">Gestaciones</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="gestaciones" name="gestaciones">
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="partos" class="col-sm-3 col-form-label">Partos</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="partos" name="partos">
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="vivos" class="col-sm-3 col-form-label">Vivos</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="vivos" name="vivos">
                                                                            </div> 
                                                                        </div>  
                                                                        <div class="form-group row">
                                                                            <label for="cesareas" class="col-sm-3 col-form-label">Cesáreas</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="cesareas" name="cesareas">
                                                                            </div> 
                                                                        </div>  
                                                                        <div class="form-group row">
                                                                            <label for="abortos" class="col-sm-3 col-form-label">Abortos</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="abortos" name="abortos">
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="menopausia" class="col-sm-3 col-form-label">Menopausía</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="menopausia" name="menopausia">
                                                                            </div> 
                                                                        </div> 
                                                                        <div class="form-group row">
                                                                            <label for="actividadS" class="col-sm-3 col-form-label">Actividad Sexual (Si/No)</label>
                                                                            <div class="col-sm-9">                                
                                                                                <select id="actividadS" name="actividadS" class="form-control show-tick " data-live-search="true">
                                                                                    <option value="">--Seleccione una opcion--</option>
                                                                                    <option value="Si">Si</option>
                                                                                    <option value="No">No</option>
                                                                                </select>
                                                                            </div> 
                                                                        </div>  
                                                                        <div class="form-group row">
                                                                            <label for="metAnticonceptivo" class="col-sm-3 col-form-label">Mét. Anticonceptivo</label>
                                                                            <div class="col-sm-9">                                
                                                                                <input type="text" class="form-control" id="metAnticonceptivo" name="metAnticonceptivo">
                                                                            </div> 
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label for="pap" class="col-sm-3 col-form-label">PAP test:</label>
                                                                            <div class="col-sm-9">                                
                                                                                <textarea type="text" class="form-control" id="pap" name="pap"  placeholder="PAP test"></textarea>
                                                                            </div>
                                                                        </div> 
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-contentt -->
                            <!--Parámetros-->
                            <div class="tab-pane fade show" id="parametros" role="tabpanel" aria-labelledby="parametros-tab">
                                <div class="card">
                                    <div class="card-header border-0">
                                        <div class="form-group row">
                                            <label for="dato1" class="col-sm-1 col-form-label">Dato 1:</label>
                                            <div class="col-sm-2">
                                                <select id="dato1" name="dato1" class="form-control show-tick " data-live-search="true">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                </select>
                                            </div>
                                            <label for="dato2" class="col-sm-1 col-form-label">Dato 2:</label>
                                            <div class="col-sm-2">
                                                <select id="dato2" name="dato2" class="form-control show-tick " data-live-search="true">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                </select>
                                            </div>
                                            <label for="dato1" class="col-sm-1 col-form-label">Desde:</label>
                                            <div class="col-sm-2">
                                                <input type="date" class="form-control">
                                            </div>
                                            <label for="dato2" class="col-sm-1 col-form-label">Hasta:</label>
                                            <div class="col-sm-2">
                                                <input type="date" class="form-control">
                                            </div>
                                            <div class="col-sm-2">
                                                <a class="btn btn-info btn-sm float-right" style="padding: 6px 30px;" onclick="grafico();">Buscar</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 
                                <div class="card">
                                    <div class="card-header border-0">
                                        <div class="d-flex justify-content-between">
                                        <h3 class="card-title">Control de Parámetros</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="speedChart" width="1200" height="600"></canvas>
                                        <div class="d-flex flex-row center-content-end">
                                            <span class="mr-2">
                                                <i class="fas fa-square text-primary"></i> This Week
                                            </span>
                                            <span>
                                                <i class="fas fa-square text-gray"></i> Last Week
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.tab-contentt -->
                            <!--Banco de Archivos-->
                            <div class="tab-pane fade show" id="bancoArchivos" role="tabpanel" aria-labelledby="bancoArchivos-tab">
                                <div class="card">
                                    <div class="card-header border-0">
                                                <div class="form-group row">
                                            <label for="dato1" class="col-sm-1 col-form-label">Medico:</label>
                                            <div class="col-sm-3">
                                                <select id="dato1" name="dato1" class="form-control show-tick " data-live-search="true">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                </select>
                                            </div>
                                            <label for="dato1" class="col-sm-1 col-form-label">Titulo:</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control">
                                            </div>
                                            <label for="dato1" class="col-sm-1 col-form-label">Fecha:</label>
                                            <div class="col-sm-2">
                                                <input type="date" class="form-control">    
                                            </div>
                                        </div> 
                                        <div class="form-group row">
                                            <label for="dato1" class="col-sm-1 col-form-label">Descripcion:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control">
                                            </div>
                                        </div> 
                                        <div class="form-group row">
                                            <label for="archivo" class="col-sm-1 col-form-label">Archivo:</label>
                                            <div class="col-sm-4">
                                                <input type="file" class="form-control" id="archivo">
                                            </div>
                                            <label for="dato1" class="col-sm-1 col-form-label">Cita:</label>
                                            <div class="col-sm-3">
                                                <select id="dato1" name="dato1" class="form-control show-tick " data-live-search="true">
                                                        <option value="" label>--Seleccione una opcion--</option>
                                                </select>
                                            </div>
                                            <label for="dato1" class="col-sm-1 col-form-label">Principal:</label>
                                            <div class="col-sm-2">
                                                <select id="dato1" name="dato1" class="form-control show-tick " data-live-search="true">
                                                        <option value="" label>--Seleccione una opcion--</option>
                                                </select>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>
    	                    <!-- /.tab-contentt -->
                            <!--Historial-->
                            <div class="tab-pane fade show" id="historial" role="tabpanel" aria-labelledby="historial-tab">
                                <br>
                                <div class="row">
                                <a>&nbsp;&nbsp;&nbsp;</a>
                                    <div class="card col-5">
                                        <div class="card-header border-0">
                                            <div class="form-group row">
                                                <label for="dato1" class="col-sm-2 col-form-label">Desde:</label>
                                                <div class="col-sm-4">
                                                    <input type="date" class="form-control">
                                                </div>
                                                <label for="dato2" class="col-sm-2 col-form-label">Hasta:</label>
                                                <div class="col-sm-4">
                                                    <input type="date" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="dato1" class="col-sm-2 col-form-label">Tipo:</label>
                                                <div class="col-sm-10">
                                                    <select id="dato1" name="dato1" class="form-control show-tick " data-live-search="true">
                                                            <option value="" label>--Seleccione una opcion--</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <a class="btn btn-info btn-sm float-right" style="padding: 6px 30px;">Buscar</a>
                                            </div>
                                        </div>
                                    </div>
                                    <a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                    <div class="card col-6">
                                        <div class="card-header border-0">
                                        <p>
                                            Haga click sobre una atencion medica para ver el detalle.
                                        </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-contentt -->
                            <!--Atención-->
                            <div class="tab-pane fade show" id="atencion" role="tabpanel" aria-labelledby="atencion-tab">
                                <br>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                                        <div>
                                            <table class="table table-hover">
                                                <tbody>
                                                    <br>     
                                                    <?php $temp = 1;?>   
                                                </tbody>
                                            </table>
                                        </div>
                                        <div>
                                            <div class="form-group row">
                                                <label for="otros_examenes" class="col-sm-4 col-form-label">OTROS EXÁMENES:</label>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">                                
                                                    <textarea type="text" class="form-control" id="otros_examenes" name="otros_examenes"  placeholder="OTROS EXÁMENES"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-contentt -->
                            <!--Hallazgos-->
                            <div class="tab-pane fade show" id="hallazgos" role="tabpanel" aria-labelledby="hallazgos-tab">
                                <br>
                                <div class="row">
                                    <div class="col-5">
                                        
                                    </div>
                                    <div class="col-7">
                                        <div class="form-group row">
                                            <label for="dato1" class="col-sm-2 col-form-label">Desde:</label>
                                            <div class="col-sm-4">
                                                <input type="date" class="form-control">
                                            </div>
                                            <label for="dato2" class="col-sm-2 col-form-label">Hasta:</label>
                                            <div class="col-sm-4">
                                                <input type="date" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="dato1" class="col-sm-2 col-form-label">Tipo:</label>
                                            <div class="col-sm-10">
                                                <select id="dato1" name="dato1" class="form-control show-tick " data-live-search="true">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="dato1" class="col-sm-2 col-form-label">#:</label>
                                            <div class="col-sm-4">
                                                <select id="dato1" name="dato1" class="form-control show-tick " data-live-search="true">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                </select>
                                            </div>
                                            <label for="dato2" class="col-sm-2 col-form-label">Fecha:</label>
                                            <div class="col-sm-4">
                                                <input type="date" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="dato1" class="col-sm-2 col-form-label">Medico:</label>
                                            <div class="col-sm-10">
                                                <select id="dato1" name="dato1" class="form-control show-tick " data-live-search="true">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="dato1" class="col-sm-2 col-form-label">Observacion:</label>
                                            <div class="col-sm-10">
                                                <textarea type="date" class="form-control"> </textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-contentt -->
                            <!--Documentos-->
                            <div class="tab-pane fade show" id="documentos" role="tabpanel" aria-labelledby="documentos-tab">
                                <br>
                                <div class="row">
                                    <div class="col-5">
                                        <div class="form-group row">
                                            <label for="dato1" class="col-sm-2 col-form-label">Desde:</label>
                                            <div class="col-sm-4">
                                                <input type="date" class="form-control">
                                            </div>
                                            <label for="dato2" class="col-sm-2 col-form-label">Hasta:</label>
                                            <div class="col-sm-4">
                                                <input type="date" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="dato1" class="col-sm-2 col-form-label">Tipo:</label>
                                            <div class="col-sm-10">
                                                <select id="dato1" name="dato1" class="form-control show-tick " data-live-search="true">
                                                        <option value="" label>--Seleccione una opcion--</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-7">
                                        <p>
                                            Haga click sobre una atencion medica para ver el detalle.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-contentt -->
                            <!--Resultados-->
                            <div class="tab-pane fade show" id="resultados" role="tabpanel" aria-labelledby="resultados-tab">
                                <br>
                            </div>
                            <!-- /.tab-contentt -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-footer -->
    </div>
    <!-- /.card-body -->
</div>
</form>
<!-- /.card -->
@endsection
<script>

    function edad(fechaNacimiento){
        //El siguiente fragmento de codigo lo uso para igualar la fecha de nacimiento con la fecha de hoy del paciente
        let d = new Date(),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();
        if (month.length < 2) 
            month = '0' + month;
        if (day.length < 2) 
            day = '0' + day;
        d=[year, month, day].join('-')
        /*------------*/
        var hoy = new Date(d);//fecha del sistema con el mismo formato que "fechaNacimiento"
        var cumpleanos = new Date(fechaNacimiento);
        //alert(cumpleanos+" "+hoy);
        //Calculamos años
        var edad = hoy.getFullYear() - cumpleanos.getFullYear();
        var m = hoy.getMonth() - cumpleanos.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
            edad--;
        }
        // calculamos los meses
        var meses=0;
        if(hoy.getMonth()>cumpleanos.getMonth()){
            meses=hoy.getMonth()-cumpleanos.getMonth();
        }else if(hoy.getMonth()<cumpleanos.getMonth()){
            meses=12-(cumpleanos.getMonth()-hoy.getMonth());
        }else if(hoy.getMonth()==cumpleanos.getMonth() && hoy.getDate()>cumpleanos.getDate() ){
            if(hoy.getMonth()-cumpleanos.getMonth()==0){
                meses=0;
            }else{
                meses=11;
            }
        }
        // Obtener días: día actual - día de cumpleaños
        let dias  = hoy.getDate() - cumpleanos.getDate();
        if(dias < 0) {
            // Si días es negativo, día actual es mayor al de cumpleaños,
            // hay que restar 1 mes, si resulta menor que cero, poner en 11
            meses = (meses - 1 < 0) ? 11 : meses - 1;
            // Y obtener días faltantes
            dias = 30 + dias;
        }
        console.log(`${edad} año(s) ${meses} mes(es) ${dias} día(s)`);
        var msg = `${edad} año(s) ${meses} mes(es) ${dias} día(s)`;
        return msg;
    }   
   
</script>
<script>
    function grafico(){
    var speedCanvas = document.getElementById("speedChart");

    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 16;

    var speedData = {
        labels: ["45s", "10s", "100s"],
        datasets: [{
            label: "Car Speed (mph)",
            data: [45, 10],
        }]
    };

    var chartOptions = {
        legend: {
            display: true,
            position: 'top',
            labels: {
            boxWidth: 200,
            fontColor: 'purple'
            }
        }
    };

    var lineChart = new Chart(speedCanvas, {
        type: 'line',
        data: speedData,
        options: chartOptions
        });
    }
</script>