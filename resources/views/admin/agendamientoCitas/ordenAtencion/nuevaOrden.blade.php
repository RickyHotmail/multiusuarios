@extends ('admin.layouts.admin')
@section('principal')



<meta name="csrf-token" content="{{ csrf_token() }}">
<form class="form-horizontal" method="POST" action="{{ url("ordenAtencion") }}" enctype="multipart/form-data" onsubmit="return validarInformacion()">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Nueva Orden de Atencion</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("ordenAtencion") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="modal-body">
            <div class="card-body">
                <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Facturación</h5>
                <div class="form-group row">
                    <div class="col-md-8 row">
                        <label for="idFechaFac" class="col-sm-1 col-form-label">Fecha:</label>
                        <div class="col-sm-2">
                            <input type="date" id="idFechaFac" name="idFechaFac" class="form-control" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required readonly>
                        </div>
                        <label for="buscarCliente" class="offset-lg-1 col-sm-1 col-form-label">Cliente:</label>
                        <div class="col-sm-7">
                            <input id="clienteID" name="clienteID" type="hidden">
                            <input id="buscarCliente" name="buscarCliente" type="text" class="form-control" placeholder="Cliente" required>
                        </div>
                    </div>
                    <div class="col-md-4 row">
                        <label for="idCedulaF" class="offset-lg-2 col-sm-2 col-form-label">RUC/CI:</label>
                        <div class="col-sm-5">
                            <input type="text" id="idCedulaF" name="idCedulaF" class="form-control" placeholder="9999999999" disabled required>
                        </div>
                        <div class="col-sm-3 pt-1">
                            <center><a href="{{ url("cliente/create") }}" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-user"></i>&nbsp;&nbsp;Nuevo</a></center>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-8 row">
                        <div class="m-0 col-sm-12 form-group row">
                            <label class="col-sm-1 form-control-label">PAGO :</label>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <select id="factura_tipo_pago" name="factura_tipo_pago" class="form-control show-tick "
                                        data-live-search="true">
                                        <option value="CONTADO">CONTADO</option>
                                        <option value="EN EFECTIVO">EN EFECTIVO</option>
                                    </select>
                                </div>
                            </div>

                            <label class="offset-lg-1 col-sm-1 form-control-label">Bodega :</label>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <select id="bodega_id" name="bodega_id" class="form-control show-tick"
                                        data-live-search="true">
                                        @foreach($bodegas as $bodega)
                                        <option value="{{ $bodega->bodega_id }}">{{ $bodega->bodega_nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <label class="offset-lg-1 col-sm-1 form-control-label">LUGAR :</label>
                            <div class="col-sm-3" style="margin-bottom : 0px;">
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" id="factura_lugar" name="factura_lugar" class="form-control "
                                            value="Machala" placeholder="Lugar" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 form-group row">
                            <label class="col-sm-2 col-form-label"> FORMA  DE  PAGO :</label>
                            <div class="col-sm-5" style="margin-bottom : 1px;">
                                <div class="form-group">
                                    <select class="form-control" id="forma_pago_id" name="forma_pago_id"
                                        data-live-search="true">
                                        @foreach($formasPago as $formaPago)
                                        <option value="{{ $formaPago->forma_pago_id }}" @if($formaPago->forma_pago_nombre ==
                                            'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO') selected
                                            @endif>{{ $formaPago->forma_pago_nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <label class="offset-lg-1 col-sm-1 form-control-label">Caja :</label>
                            <div class="col-sm-3 form-control-label"
                                style="margin-bottom : 0px;">
                                <select id="caja_id" name="caja_id" class="form-control show-tick" data-live-search="true" required>
                                    @if($cajaAbierta)
                                    <option value="{{ $cajaAbierta->caja->caja_id }}">{{ $cajaAbierta->caja->caja_nombre }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 row">
                        <div class="col-sm-12">
                            <input onchange="facturarDiv()" class="col-sm-1" type="checkbox" id="checkFacturar" name="checkFacturar">
                            <label class="col-sm-8" for="checkFacturar">Facturar ahora</label>
                        </div>
                        <div class="col-sm-12 row" id="facturarDiv" style="display:none">
                            <div class="offset-md-1 col-sm-5">
                                <div class="demo-checkbox">
                                    <input type="radio" value="ELECTRONICA" id="check1"
                                        class="with-gap radio-col-deep-orange" name="tipoDoc" checked required />
                                    <label for="check1">Doc. Electronico</label>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="demo-checkbox">
                                    <input type="radio" value="FISICA" id="check2" class="with-gap radio-col-deep-orange"
                                        name="tipoDoc" required />
                                    <label for="check2">Doc. Físico</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Orden de Atención</h5><br>
                <div class="form-group row">
                    <label for="idSucursal" class="col-sm-1 col-form-label">Sucursal :</label>
                    <div class="col-sm-3">
                        <select id="idSucursal" name="idSucursal" class="form-control select2" onchange="cargarOA();"  required>
                                <option value="" label>--Seleccione una opcion--</option>                                                           
                            @foreach($sucursales as $sucursal)
                                <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="Codigo" class="col-sm-2 col-form-label"><center>Orden de Atencion : </center></label>
                    <div class="col-sm-2">                                                          
                        <input type="text" class="form-control negrita" id="Codigo" name="Codigo" placeholder="OA-" readonly required>                  
                    </div>
                    <div class="col-sm-4">                                
                        <input type="text" class="form-control negrita" id="Secuencial" name="Secuencial" value="{{$secuencial}}" placeholder="000000001" readonly required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idPaciente" class="col-sm-1 col-form-label">Paciente : </label>
                    <div class="col-sm-7">
                        <input id="buscarPaciente" name="buscarPaciente" type="text" class="form-control" placeholder="Paciente" required>
                        <input id="idPaciente" name="idPaciente" class="invisible" placeholder="Paciente">
                    </div>
                    <label for="idCedula" class="col-sm-1 col-form-label">Cédula :</label>
                    <div class="col-sm-2">
                        <input id="idCedula" type="text" class="form-control" placeholder="9999999999" required readonly>
                    </div>
                   
                    <div class="col-sm-1"><center><a href="{{ url("paciente/create") }}" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-user"></i>&nbsp;Nuevo</a></center></div>
                    
                </div>
                <div class="form-group row">
                    <label for="idAseguradora" class="col-sm-1 col-form-label">Aseguradora:</label>
                    <div class="col-sm-3">
                        <input id="idAseguradora" name="idAseguradora" type="text" class="form-control" required readonly>
                      
                    </div>
                    <label for="idEmpresa" class="col-sm-1 col-form-label">Empresa :</label>
                    <div class="col-sm-3">
                        <input id="identidad" name="identidad" type="hidden">
                        <input id="idEmpresa" name="idEmpresa" type="text" class="form-control" required readonly>
                    </div>
                    <label for="tipo_atencion" class="col-sm-2 col-form-label"><center>Tipo de Atención :</center></label>
                    <div class="col-sm-2">
                        <select name="tipo_atencion" class="form-control select2" required>
                                <option value="0">Por Primera Vez</option>
                                <option value="1">Subsecuente</option>
                        </select>
                    </div>
                </div> 
                <div class="form-group row">
                    <label for="especialidad_id" class="col-sm-1 col-form-label">Especialidad:</label>
                    <div class="col-sm-3">
                        <select id="especialidad_id" name="especialidad_id" class="form-control select2" data-live-search="true" onchange="cargarMedicos();cargarServicios();" disabled required>
                                <option value="" label>--Seleccione una opcion--</option>
                        </select>
                    </div>
                    <label for="idMespecialidad" class="col-sm-1 col-form-label">Medico :</label>
                    <div class="col-sm-3">
                        <select id="idMespecialidad" name="idMespecialidad" class="form-control select2" data-live-search="true" onchange="cargarHorarioSemanal();" disabled required>
                            <option value="" label>--Seleccione una opcion--</option>
                        </select>
                    </div>
                    <label for="idSeguro" class="col-sm-1 col-form-label">Seguro :</label>
                    <div class="col-sm-3">
                        <select id="idSeguro" name="idSeguro" class="form-control select2" required>
                                <option value="" label>--Seleccione una opcion--</option>                                                           
                            @foreach($seguros as $tipo)
                                <option value="{{$tipo->tipo_id}}">{{$tipo->tipo_codigo.' - '.$tipo->tipo_nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Observacion" class="col-sm-1 col-form-label">Observación:</label>
                    <div class="col-sm-3">
                        <input id="Observacion" name="Observacion" type="text" class="form-control" value="Cita Medica" required>
                    </div>

                    <label for="idServicio" class="col-form-label">Fec y Hora de Cita:</label>
                    <div class="col-sm-2">
                        <input id="idFechaHora" type="text" class="form-control" readonly required>
                        <input type="hidden" id="fechaCitaID" name="fechaCitaID" value="" required/>
                        <input type="hidden" id="horaCitaID" name="horaCitaID" value="" required/>
                    </div>

                    <div style="margin-top: 12px">
                        <input onchange="cambiarModoAmpliacion()" id="idAmpliaciones" type="checkbox" name="idAmpliaciones" class="form-check" readonly required>
                    </div>
                    <label for="idAmpliaciones" class="col-sm-1 col-form-label">Ampliación</label>

                    <label for="idReclamo" class="col-sm-1 col-form-label">Reclamo:</label>
                    <div class="col-sm-2">
                        <input type="hidden" id="idReclamoSec" name="idReclamoSec" value="" required/>
                        <input type="text" id="idReclamoNum" name="idReclamoNum" class="form-control" value="" required readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="es_dependiente" class="col-sm-1 col-form-label">Dependiente:</label>
                    <div class="col-sm-1">
                        <select id="es_dependiente" name="es_dependiente" class="form-control" onchange="cargarDatosDependencia();" required>
                                <option value="1">SI</option>
                                <option value="0" selected>NO</option>
                        </select>
                    </div>
                    <div class="col-sm-10 invisible" id="idDatosDependencia">
                        <div class="row">
                            <label for="idCedulaAsegurado" class="col-sm-2 col-form-label"><center>C.I. Asegurado:</center></label>
                            <div class="col-sm-2">
                                <input id="idCedulaAsegurado" name="idCedulaAsegurado" type="text" class="form-control" placeholder="9999999999">
                            </div>
                            <label for="idNombreAsegurado" class="col-sm-2 col-form-label"><center>Nombre Asegurado:</center></label>
                            <div class="col-sm-3">
                                <input id="idNombreAsegurado" name="idNombreAsegurado" type="text" class="form-control" placeholder="Nombres y Apellidos">
                            </div>
                            <div class="col-sm-3">
                                <div class="row">
                                    <label for="IdTipoDependencia" class="col-sm-5 col-form-label">Dependencia:</label>
                                    <div class="col-sm-7">
                                        <select id="IdTipoDependencia"name="IdTipoDependencia" class="form-control">
                                            @foreach($tiposDependencias as $tipo)
                                                <option value="{{$tipo->tipod_id}}">{{$tipo->tipod_nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                      
                <div class="form-group row">
                    <div class="col-sm-12">
                        <table class="table table-striped table-hover" style="margin-bottom: 6px;">
                            <thead>
                                <tr>
                                    <th class="centrar-texto neo-fondo-tabla">Código</th>
                                    <th class="centrar-texto neo-fondo-tabla">Cant</th>
                                    <th class="centrar-texto neo-fondo-tabla">Procedimiento</th>
                                    <th class="centrar-texto neo-fondo-tabla">Precio</th>
                                    <th class="centrar-texto neo-fondo-tabla">% Cobertura</th>
                                    <th class="centrar-texto neo-fondo-tabla">Cobertura</th>
                                    <th class="centrar-texto neo-fondo-tabla">% Dscto</th>
                                    <th class="centrar-texto neo-fondo-tabla">Dscto.</th>
                                    <th class="centrar-texto neo-fondo-tabla">Copago</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="centrar-texto" id="IdCodigoP" id="IdCodigoP"></td>
                                    <input type="hidden" id="IdCodigo" name="IdCodigo" value="">
                                    <td class="centrar-texto" >1</td>
                                    <td>
                                        <select id="idServicio" name="idServicio" class="form-control select2" data-live-search="true" onchange="cargarDatosProcedimiento();" disabled required>
                                            <option value="" label>--Seleccione una opcion--</option>
                                        </select>
                                    </td>
                                    <input  type="hidden" id="nombreP" name="nombreP" value="">
                                    <td class="centrar-texto" id="IdPrecioP" name="IdPrecioP"></td>
                                    <input  type="hidden" id="IdPrecio" name="IdPrecio" value="">
                                    <td class="centrar-texto" id="IdCoberturaPorcentaje"  name="IdCoberturaPorcentaje"></td>
                                    <input  type="hidden" id="IdCoberturaPorcen" name="IdCoberturaPorcen" value="">
                                    <td class="centrar-texto" id="IdCoberturaP"  name="IdCoberturaP"></td>
                                    <input  type="hidden" id="IdCobertura" name="IdCobertura" value="">
                                    
                                    <td class="centrar-texto">
                                        <input  type="hidden" id="IdDescuentoPorcentaje" name="IdDescuentoPorcentaje" value="0" step="1" min="0" max="100" class="text-right">
                                    </td>

                                    <td class="centrar-texto" id="IdDescuentoP"></td>
                                    <input  type="hidden" id="IdDescuento" name="IdDescuento" value="0.00">

                                    <td class="centrar-texto" id="IdCopagoP"  name="IdCopagoP"></td>
                                    <input  type="hidden" id="IdCopago" name="IdCopago" value="">
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> 
                <div class="form-group row">
                    <div class="col-sm-12">
                    <table class="table table-striped table-hover boder-sar tabla-item-factura" style="margin-bottom: 6px;">
                            <thead>
                                <tr>
                                   <th colspan="2"class="centrar-texto neo-color-fondo">DOCUMENTOS</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="centrar-texto neo-fondo-tabla boder-sar">Cédula del Paciente:</td>
                                    <td><a class="btn btn-outline-primary mr-2" id="a-documento-paciente" style="display:none" target="_blank" >VER CEDULA PACIENTE</a><input id="file-documento-paciente" name="file-documento-paciente" type="file" data-theme="fas" accept="application/pdf,image/*" onClick = "validararchivos();"></td>
                                </tr>
                                <tr id="marcoAfiliado" style="display: none">
                                    <td class="centrar-texto neo-fondo-tabla boder-sar">Cédula del Afiliado:</td>
                                    <td><a class="btn btn-outline-primary mr-2" id="a-documento-afiliado" style="display:none" target="_blank" >VER CEDULA AFILIADO</a><input id="file-documento-afiliado" name="file-documento-afiliado" type="file" data-theme="fas" accept="application/pdf,image/*" onClick = "validararchivos();"></td>
                                </tr>

                                @foreach($documentos as $doc)
                                    <tr>
                                        <td class="centrar-texto neo-fondo-tabla boder-sar">{{$doc->documento_nombre}}</td>
                                        <td><input id="file-es{{$doc->documento_id}}" name="file-es{{$doc->documento_id}}" type="file" data-theme="fas" accept="application/pdf,image/*" onClick = "validararchivos();"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="invisible">
                        <div class="sticky-top mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div id="external-events"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-body p-0">
                                <!-- THE CALENDAR -->
                                <div id="calendar" class="invisible"></div>
                            </div>
                        </div>
                    </div>
                </div>                                            
            </div>
        </div>
    </div>
</form>
@section('scriptAjax')
<script src="{{ asset('admin/js/ajax/autocompleteClienteOrden.js') }}"></script>
<script src="{{ asset('admin/js/ajax/autocompletePaciente.js') }}"></script>
@endsection

<script>
    function facturarDiv(){
        if(document.getElementById('checkFacturar').checked){
            document.getElementById('facturarDiv').style.display="flex"
            document.getElementById('check1').checked=true
        }
        else
            document.getElementById('facturarDiv').style.display="none"
    }
</script>
<script>
    function cambiarModoAmpliacion(){
        console.log(document.getElementById("idAmpliaciones").checked)

        fechaAmpliacion= new Date();

        //fechaCita = new Date(y, m, d, fechaAux.getHours(), fechaAux.getMinutes());
        fechaAmpliacion.setHours(0);
        fechaAmpliacion.setMinutes(0);
        console.log(fechaAmpliacion)
     
        const formatDate = (fechaHora)=>{
            let formatted_date = fechaHora.getFullYear() + "-" + (fechaHora.getMonth() + 1) + "-" + fechaHora.getDate()
            return formatted_date;
        }
        
        if(document.getElementById("idAmpliaciones").checked){
            document.getElementById("fechaCitaID").value= formatDate(fechaAmpliacion);
            document.getElementById("horaCitaID").value= "00:00";
            document.getElementById("idFechaHora").value= formatDate(fechaAmpliacion)+'   00:00 hs.';

            fecha_seleccionada=moment(fechaAmpliacion).format('YYYY-MM-DD HH:mm:ss');
        }
        else{
            document.getElementById("fechaCitaID").value= "";
            document.getElementById("horaCitaID").value= "";
            document.getElementById("idFechaHora").value= "";
            fecha_seleccionada='';
        }

    }
</script>

<script>
    semana_actual = 0;
    var Calendar
    fecha_seleccionada='';

    function validararchivos() {
        $(document).on('change','input[type="file"]',function(){

            var fileName = this.files[0].name;
            var fileSize = this.files[0].size;
        
            if(fileSize > 1000000){
                alert('El tamaño maximo del archivo es de 1MB');
                this.value = '';
                this.files[0].name = '';
            }else{
                // recuperamos la extensión del archivo
                var ext = fileName.split('.').pop();
                
                // Convertimos en minúscula porque 
                // la extensión del archivo puede estar en mayúscula
                ext = ext.toLowerCase();
            
                // console.log(ext);
                switch (ext) {
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'pdf': break;
                    default:
                        alert('Solo puede subir Imagenes o PDF');
                        this.value = ''; // reset del valor
                        this.files[0].name = '';
                }
            }
        });
    }

    function validarInformacion(){
        if($("#clienteID").val()==""){
            alert('Seleccione un Cliente para continuar con el proceso')
            return false;
        }
        else if(!document.getElementById("idAmpliaciones").checked){
            if(fecha_seleccionada==""){
                alert("Selecciona un fecha en el Horario establecido")
                return false
            }
            else if(getCitaDisponible()){
                alert('¡Este Horario ya esta registrado para el Medico seleccionado, actualice la página e intente nuevamente!')
                return false;
            }
        }
        else
            console.log('todo ha salido bien')

        //alert('¡Evité guardar!')
        return true;
    }
    function cargarOA(){  
        $.ajax({
            url: '{{ url("sucursales/searchN") }}'+ '/' +document.getElementById("idSucursal").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("idSucursal").value
            },                      
            success: function(data){    
                for (var i = 0; i < data.length; i++) {                    
                    if(data[i].sucursal_id > 0){                 
                        document.getElementById("Codigo").value = 'OA-' + data[i].sucursal_nombre.replace(/\s+/g, ''); // replace() sirve para quitar los espacios
                    }else{
                        document.getElementById("Codigo").value = 'OA-';
                    }                        
                }
            },
            error: function(data) {
                document.getElementById("Codigo").value = 'OA-';
            },
        });         
    }  

    function cargarEspecialidadesPaciente(){   
        $.ajax({
            async: false,
            url: '{{ url("especilidadesPaciente/searchN") }}'+ '/' +document.getElementById("idPaciente").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("idPaciente").value
            },                      
            success: function(data){   
                document.getElementById("especialidad_id").disabled = false;
                resetCargarEspecialidadesPaciente();
                if(document.getElementById("buscarPaciente").value != ""){
                    for (var i = 0; i < data.length; i++) {                    
                        document.getElementById("especialidad_id").innerHTML += "<option value='" + data[i].especialidad_id + "'>" + data[i].especialidad_nombre + "</option>";
                    }
                }
            },
            error: function(data) {
                resetCargarEspecialidadesPaciente();
            },
        });
    }
    function resetCargarEspecialidadesPaciente(){
        document.getElementById("especialidad_id").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
        document.getElementById("idMespecialidad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
        document.getElementById("idServicio").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";    
        document.getElementById("calendar").classList.add('invisible');   
        document.getElementById("idFechaHora").value= '';
        document.getElementById("fechaCitaID").value= '';
        document.getElementById("horaCitaID").value= '';  
        document.getElementById("nombreP").value='';
        document.getElementById("IdCodigo").value = '';    
        document.getElementById("IdCodigoP").innerHTML = '';  
        document.getElementById("IdPrecioP").innerHTML = '';
        document.getElementById("IdPrecio").value = '';    
        document.getElementById("IdCoberturaPorcentaje").innerHTML = '';
        document.getElementById("IdCoberturaPorcen").value = '';    
        document.getElementById("IdCoberturaP").innerHTML = '';
        document.getElementById("IdCobertura").value = '';    
        document.getElementById("IdCopagoP").innerHTML = '';
        document.getElementById("IdCopago").value = '';    
    }
    function cargarMedicos(){   
        $.ajax({
            async: false,
            url: '{{ url("medicoEspecialidad/searchN") }}'+ '/' +document.getElementById("especialidad_id").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("especialidad_id").value
            },                      
            success: function(data){               
                document.getElementById("idMespecialidad").disabled = false;
                resetCargarMedicos();
                for (var i = 0; i < data.length; i++) {                    
                    if(data[i].proveedor_nombre != null){                                       
                        document.getElementById("idMespecialidad").innerHTML += "<option value='" + data[i].mespecialidad_id + "'>" + data[i].proveedor_nombre + "</option>";
                    }else{ 
                        document.getElementById("idMespecialidad").innerHTML += "<option value='" + data[i].mespecialidad_id + "'>" + data[i].empleado_nombre + "</option>";
                    }                
                }                
            },
            error: function(data) { 
                resetCargarMedicos();
            },
        });
    }
    function resetCargarMedicos(){
        document.getElementById("idMespecialidad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
        document.getElementById("calendar").classList.add('invisible');
        document.getElementById("idFechaHora").value= '';
        document.getElementById("fechaCitaID").value= '';
        document.getElementById("horaCitaID").value= '';
        document.getElementById("IdCodigo").value = '';
        document.getElementById("IdCodigoP").innerHTML = '';  
        document.getElementById("IdPrecioP").innerHTML = '';   
        document.getElementById("IdCoberturaPorcentaje").innerHTML = '';   
        document.getElementById("IdCoberturaP").innerHTML = '';   
        document.getElementById("IdCopagoP").innerHTML = ''; 

        document.getElementById("nombreP").value='';
        document.getElementById("IdCodigo").value = '';  
        document.getElementById("IdCobertura").value = ''; 
        document.getElementById("IdCoberturaPorcen").value = '';
        document.getElementById("IdPrecio").value = '';
        document.getElementById("IdCopago").value = ''; 
    }
    function cargarServicios(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });        
        $.ajax({
            async: false,
            url: '{{ url("servicios/searchN") }}',
            dataType: "json",            
            type: "POST",
            data: {
                paciente: document.getElementById("idPaciente").value,
                especialidad: document.getElementById("especialidad_id").value
            },                       
            success: function(data){                                
                document.getElementById("idServicio").disabled = false;
                document.getElementById("idServicio").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";  
                for (var i = 0; i < data.length; i++) { 
                    document.getElementById("idServicio").innerHTML += "<option value='" + data[i].procedimientoA_id + "'>" + data[i].producto_nombre + "</option>";
                }                
            },
            error: function(data) {
                document.getElementById("idServicio").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";                               
            },
        });
    }    
    function caragrHorario(){
        document.getElementById("calendar").classList.remove('invisible');
        document.getElementById("idFechaHora").value= '';
        document.getElementById("fechaCitaID").value= '';
        document.getElementById("horaCitaID").value= '';
        $.ajax({
            async: false,
            url: '{{ url("horas/searchN") }}'+ '/' +document.getElementById("idMespecialidad").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("idMespecialidad").value
            },                      
            success: function(data){  
                var date = new Date()
                var d    = date.getDate(),
                    m    = date.getMonth(),
                    y    = date.getFullYear(),
                    dia  = date.getDay();
                let horarios = []
                var valor = 0;
                for (var i = 0; i < data.length; i++) { 
                    valor = 0;
                    if(data[i].horario_dia == 'Lunes'){
                        valor = 1 - dia;
                    }
                    if(data[i].horario_dia == 'Martes'){
                        valor = 2 - dia;
                    }
                    if(data[i].horario_dia == 'Miércoles'){
                        valor = 3 - dia;
                    }
                    if(data[i].horario_dia == 'Jueves'){
                        valor = 4 - dia;
                    }
                    if(data[i].horario_dia == 'Viernes'){
                        valor = 5 - dia;
                    }
                    if(data[i].horario_dia == 'Sábado'){
                        valor = 6 - dia;
                    }
                    if(data[i].horario_dia == 'Domingo'){
                        valor = -1 + dia;
                    }
                    
                    var turnos = 60/data[i].especialidad_duracion;
                    horaI = data[i].horario_hora_inicio.split(':');
                    horaF = data[i].horario_hora_fin.split(':');

                    var hora1 = new Date();
                    hora1.setHours(horaI[0]);
                    hora1.setMinutes(horaI[1]);
                    var hora2 = new Date();
                    hora2.setHours(horaF[0]);
                    hora2.setMinutes(horaF[1]);

                    //La diferencia se da en milisegundos así que debes dividir entre 1000
                    var horasTurno = (((hora2-hora1)/1000)/60)/60;
                    
                    fechaAux = new Date();
                    fechaAux.setHours(horaI[0]);
                    fechaAux.setMinutes(horaI[1]);
                    fechaPost = new Date();
                    fechaPost.setHours(horaI[0]);
                    fechaPost.setMinutes(horaI[1]);    
                    for(var tur = 0; tur < turnos*horasTurno; tur++){
                        fechaPost.setMinutes(fechaPost.getMinutes() + data[i].especialidad_duracion);
                        fechaCita = new Date(y, m, d + valor, fechaAux.getHours(),fechaAux.getMinutes());
                        horarios.push({
                            title          : 'DISPONIBLE',//data[i].horario_hora_inicio,
                            start          : new Date(y, m, d + valor, fechaAux.getHours(),fechaAux.getMinutes()),
                            end            : new Date(y, m, d + valor, fechaPost.getHours(),fechaPost.getMinutes()),
                            allDay         : false,
                            url            : "javascript:seleccionarHora('"+fechaCita+"');",
                            backgroundColor: '#00a65a', //Success (green)
                            borderColor    : '#00a65a' //Success (green)
                        });
                        fechaAux.setMinutes(fechaAux.getMinutes() + data[i].especialidad_duracion);
                    }
                }
                var Calendar = FullCalendar.Calendar;
                var calendarEl = document.getElementById('calendar');
                var calendar = new Calendar(calendarEl, {
                    headerToolbar: {
                        right  : 'prev,next today',
                        center: 'title',
                        left: '',
                    },
                    themeSystem: 'bootstrap',
                    events: horarios,
                    editable  : false,
                });
                calendar.render();
            },
            error: function(data) {
               
            },
        });
    }

    function cargarHorarioSemanal(){
        document.getElementById("calendar").classList.remove('invisible');
        document.getElementById("idFechaHora").value= '';
        document.getElementById("fechaCitaID").value= '';
        document.getElementById("horaCitaID").value= '';

        $.ajax({
            async: false,
            url: '{{ url("horas/searchN") }}'+ '/' +document.getElementById("idMespecialidad").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("idMespecialidad").value
            },
            success: function(data){  
                console.log("horarios")
                console.log(data)
                
                var hoy = new Date()
                console.log("fecha")
                console.log(hoy)


                var date = new Date()
                var dias = 7*semana_actual; // sumando la semana actual
                date.setDate(hoy.getDate() + dias);


                var d    = date.getDate(),
                    m    = date.getMonth(),
                    y    = date.getFullYear(),
                    dia  = date.getDay();

                let horarios = []
                var valor = 0;
                for (var i = 0; i < data.length; i++) {
                    valor = 0;
                    if(data[i].horario_dia == 'Lunes'){
                        valor = 1 - dia;
                    }
                    if(data[i].horario_dia == 'Martes'){
                        valor = 2 - dia;
                    }
                    if(data[i].horario_dia == 'Miércoles'){
                        valor = 3 - dia;
                    }
                    if(data[i].horario_dia == 'Jueves'){
                        valor = 4 - dia;
                    }
                    if(data[i].horario_dia == 'Viernes'){
                        valor = 5 - dia;
                    }
                    if(data[i].horario_dia == 'Sábado'){
                        valor = 6 - dia;
                    }
                    if(data[i].horario_dia == 'Domingo'){
                        valor = -1 + dia;
                    }

                    
                    
                    var turnos = 60/data[i].especialidad_duracion;
                    horaI = data[i].horario_hora_inicio.split(':');
                    horaF = data[i].horario_hora_fin.split(':');

                    var hora1 = new Date();
                    hora1.setHours(horaI[0]);
                    hora1.setMinutes(horaI[1]);

                    var hora2 = new Date();
                    hora2.setHours(horaF[0]);
                    hora2.setMinutes(horaF[1]);

                    //La diferencia se da en milisegundos así que debes dividir entre 1000
                    var horasTurno = (((hora2-hora1)/1000)/60)/60;
                    
                    fechaAux = new Date();
                    fechaAux.setHours(horaI[0]);
                    fechaAux.setMinutes(horaI[1]);

                    fechaPost = new Date();
                    fechaPost.setHours(horaI[0]);
                    fechaPost.setMinutes(horaI[1]);


                    //obtener rango incio fin con fecha
                    f1 = new Date();
                    f1.setHours(horaI[0]);
                    f1.setMinutes(horaI[1]);
                    fecha_rango1 = new Date(y, m, d + valor, f1.getHours(), f1.getMinutes());

                    var f2 = new Date();
                    f2.setHours(horaF[0]);
                    f2.setMinutes(horaF[1]);
                    fecha_rango2 = new Date(y, m, d + valor, f2.getHours(),f2.getMinutes()-1)


                    console.log('rango')
                    console.log("fecha1 "+fecha_rango1+"    fecha2 "+fecha_rango2)

                    //console.log("medico "+data[i].medico_id)
                    ordenes =getOrdenesAtencion(data[i].medico_id,
                                                  data[i].especialidad_id,  
                                                  moment(fecha_rango1).format('YYYY-MM-DD HH:mm:ss'),
                                                  moment(fecha_rango2).format('YYYY-MM-DD HH:mm:ss'))

                    //console.log(data[i].medico_id+" "+data[i].especialidad_id+"      "+JSON.stringify(ordenes))

                    console.log("turnos")
                    console.log(turnos+"x"+horasTurno +"    duracion "+data[i].especialidad_duracion)

                    for(var tur = 0; tur < turnos*horasTurno; tur++){
                        fechaPost.setMinutes(fechaPost.getMinutes() + parseInt(data[i].especialidad_duracion));
                        fechaCita = new Date(y, m, d + valor, fechaAux.getHours(), fechaAux.getMinutes());

                        console.log("fecha cita "+fechaCita)


                        fecha_inicio = new Date(y, m, d + valor, fechaAux.getHours(),fechaAux.getMinutes())
                        fecha_fin = new Date(y, m, d + valor, fechaPost.getHours(),fechaPost.getMinutes()-1)

                        titulo = 'DISPONIBLE'
                        color = '#00a65a'
                        funcion =  "javascript:seleccionarHora('"+fechaCita+"');"

                        //console.log("compracion "+hoy.getTime()+"   "+fecha_inicio.getTime())
                        if(hoy.getTime()<=fecha_inicio.getTime())
                            for(var ord=0; ord<ordenes.length; ord++){
                                if (ordenes[ord].orden_hora.substring(0, 5)==moment(fecha_inicio).format('HH:mm')){
                                    titulo = 'OCUPADO'
                                    color = '#E11B1B'
                                    funcion =  "javascript:errorSeleccionar1()"
                                    
                                    break;
                                }
                            }
                        else{
                            titulo = ''
                            color = '#58704e'
                            funcion =  "javascript:errorSeleccionar2()"
                            
                            if(ordenes.length>0){
                                
                                for(var ord=0; ord<ordenes.length; ord++){
                                    if (ordenes[ord].orden_hora.substring(0, 5)==moment(fecha_inicio).format('HH:mm')){
                                        titulo = 'OCUPADO'
                                        color = '#564144'
                                    }
                                }
                            }
                        }
                        
                        horarios.push({
                            title          : titulo,//data[i].horario_hora_inicio,
                            start          : fecha_inicio,
                            end            : fecha_fin,
                            allDay         : false,
                            url            : funcion,
                            backgroundColor: color, //Success (green)
                            borderColor    : color //Success (green)
                        });

                        console.log('fecha auxiliar '+fechaAux+"   +dura "+data[i].especialidad_duracion +"   minutes "+fechaAux.getMinutes())
                        fechaAux.setMinutes(fechaAux.getMinutes() + parseInt(data[i].especialidad_duracion));

                        console.log('fecha auxiliar sumado '+fechaAux)
                        console.log('')
                    }
                }

               
                
                Calendar = FullCalendar.Calendar;
                var calendarEl = document.getElementById('calendar');
                var calendar = new Calendar(calendarEl, {
                    initialDate: moment(date).format('YYYY-MM-DD'), // will be parsed as local
                    headerToolbar: {
                        right  : 'prev,next today',
                        center: 'title',
                        left: '',
                    },
                    themeSystem: 'bootstrap',
                    events: horarios,
                    editable  : false,
                });
                calendar.render();
            },
            error: function(data) {
               
            },
        });
    }

    function errorSeleccionar1(){
        alert('Esta Cita ya ha sido reservada');
        document.getElementById("fechaCitaID").value= '';
        document.getElementById("horaCitaID").value= '';
        document.getElementById("idFechaHora").value= '';
    }

    function errorSeleccionar2(){
        alert('Esta Fecha ya ha pasado');
        document.getElementById("fechaCitaID").value= '';
        document.getElementById("horaCitaID").value= '';
        document.getElementById("idFechaHora").value= '';
    }

    function getOrdenesAtencion(medico_id, especialidad_id, fecha1, fecha2){
        f=[];

        $.ajax({
            async: false,
            url: '{{ url("horarios/getOrdenesMedico") }}',
            dataType: "json",
            type: "GET",
            data: {
                medico_id,
                especialidad_id,
                fecha1,
                fecha2
            },                      
            success: function(data){    
                //console.log("dentro del ajax ordenes")
                //console.log(data)
                
                f= data
            },
            error: function(data) { 
                console.log(data);       
            },
        });

        return f
    }

    function getCitaDisponible(){
        response=false;
        especialidad_id=document.getElementById("especialidad_id").value;
        medico_id=document.getElementById("idMespecialidad").value;

        $.ajax({
            async: false,
            url: '{{ url("horarios/getCitaDisponible") }}',
            dataType: "json",
            type: "GET",
            data: {
                medico_id,
                especialidad_id,
                fecha: fecha_seleccionada
            },                      
            success: function(data){    
                if(data.ocupada=='1')
                    response=true;
            },
            error: function(data) { 
                console.log(data);       
            },
        });
        
        return response;
    }

    function seleccionarHora(fechaHora){
        /*
        var fechaHora = new Date(fechaHora);
        fechaFormat = '';
        horaFormat = '';
        if(new String(fechaHora.getDate()).length ==1){
            fechaFormat = fechaFormat + '0' + fechaHora.getDate();
        }else{
            fechaFormat = fechaFormat + '' + fechaHora.getDate();
        }
        if(new String(fechaHora.getMonth()+1).length ==1){
            fechaFormat = fechaFormat + '/0' + fechaHora.getMonth();
        }else{
            fechaFormat = fechaFormat + '/' + fechaHora.getMonth();
        }
        fechaFormat = fechaFormat + '/' + fechaHora.getFullYear();
        if(new String(fechaHora.getHours()).length ==1){
            horaFormat = horaFormat + '0' + fechaHora.getHours();
        }else{
            horaFormat = horaFormat + '' + fechaHora.getHours();
        }
        if(new String(fechaHora.getMinutes()).length ==1){
            horaFormat = horaFormat + ':0' + fechaHora.getMinutes();
        }else{
            horaFormat = horaFormat + ':' + fechaHora.getMinutes();
        }
        document.getElementById("idFechaHora").value= fechaFormat+'   '+horaFormat+' hs.';
        document.getElementById("fechaCitaID").value= fechaFormat;
        document.getElementById("horaCitaID").value= horaFormat;
        */
        
        var fechaHora = new Date(fechaHora);
        fechaFormat = '';
        horaFormat = '';
     
        const formatDate = (fechaHora)=>{
            let formatted_date = fechaHora.getFullYear() + "-" + (fechaHora.getMonth() + 1) + "-" + fechaHora.getDate()
            return formatted_date;
        }
        var fecha_cita=formatDate(fechaHora);
        var Hora = fechaHora.toLocaleTimeString([], {timeStyle: 'short'});
        
        document.getElementById("fechaCitaID").value= formatDate(fechaHora);
        document.getElementById("horaCitaID").value= fechaHora.toLocaleTimeString();
       
        document.getElementById("idFechaHora").value= fecha_cita+'   '+Hora+' hs.';

        fecha_seleccionada=moment(fechaHora).format('YYYY-MM-DD HH:mm:ss');
    }

    function cargarDatosDependencia(){
        if(document.getElementById("es_dependiente").value == '1'){
            document.getElementById("idDatosDependencia").classList.remove('invisible');
            $('#idCedulaAsegurado').prop("required", true);
            $('#idNombreAsegurado').prop("required", true);
            $('#es_dependiente').prop("required", true);
        }else{
            document.getElementById("idDatosDependencia").classList.add('invisible');
            $('#idCedulaAsegurado').removeAttr("required");
            $('#idNombreAsegurado').removeAttr("required");
            $('#es_dependiente').removeAttr("required");
        }
    }
    function cargarDatosProcedimiento(){
        document.getElementById("nombreP").value='';
        document.getElementById("IdCodigo").value = '';
        document.getElementById("IdCodigoP").innerHTML = '';  
        document.getElementById("IdPrecioP").innerHTML = '';   
        document.getElementById("IdCoberturaPorcentaje").innerHTML = '';   
        document.getElementById("IdCoberturaP").innerHTML = '';   
        document.getElementById("IdCopagoP").innerHTML = '';  

        document.getElementById("IdCodigo").value = '';  
        document.getElementById("IdCobertura").value = ''; 
        document.getElementById("IdCoberturaPorcen").value = '';
        document.getElementById("IdPrecio").value = '';
        document.getElementById("IdCopago").value = '';  
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });        
        $.ajax({
            async: false,
            url: '{{ url("asegProsedimiento/searchN") }}',
            dataType: "json",
            type: "POST",
            data: {
                procedimientoA_id: document.getElementById("idServicio").value,
                entidad_id: document.getElementById("identidad").value
            },                      
            success: function(data){ 
                console.log("Cargando")  
                console.log(data)  
                var combo = document.getElementById("idServicio");

                document.getElementById("nombreP").value=combo.options[combo.selectedIndex].text;     
                document.getElementById("IdCodigo").value = data[0].producto_id; 
                document.getElementById("IdCodigoP").innerHTML = data[0].producto_codigo;

                document.getElementById("IdPrecioP").innerHTML = Number(data[0].procedimientoA_valor).toFixed(2);
                document.getElementById("IdPrecio").value =Number(data[0].procedimientoA_valor).toFixed(2);

                document.getElementById("IdCoberturaPorcentaje").innerHTML = Number(data[1].ep_valor).toFixed(2)+'  %'; 
                document.getElementById("IdCoberturaPorcen").value = Number(data[1].ep_valor).toFixed(2);

                valorCobertura = (Number(data[0].procedimientoA_valor)*Number(data[1].ep_valor))/100;
                document.getElementById("IdCoberturaP").innerHTML = Number(valorCobertura).toFixed(2);  
                document.getElementById("IdCobertura").value = Number(valorCobertura).toFixed(2);

                document.getElementById("IdDescuentoPorcentaje").type="number";
                document.getElementById("IdDescuentoPorcentaje").value="0";

                document.getElementById("IdDescuentoP").innerHTML="0,00";
                document.getElementById("IdDescuento").value="0,00";


                if(Number(valorCobertura)<0){
                    document.getElementById("IdCopagoP").innerHTML = Number(data[0].procedimientoA_valor).toFixed(2);
                    document.getElementById("IdCopago").value = Number(data[0].procedimientoA_valor).toFixed(2);
                }
                if(Number(valorCobertura)>0){
                    document.getElementById("IdCopagoP").innerHTML = Number(Number(data[0].procedimientoA_valor)-(Number(valorCobertura))).toFixed(2);
                    document.getElementById("IdCopago").value = Number(Number(data[0].procedimientoA_valor)-(Number(valorCobertura))).toFixed(2);
                }
            },
            error: function(data) { 
                console.log(data);       
            },
        });
    }

    document.getElementById("IdDescuentoPorcentaje").addEventListener("keyup", function(){
        valor= parseFloat(document.getElementById("IdPrecio").value);
        cobertura=parseFloat(document.getElementById("IdCobertura").value);
        porcentaje=parseInt(document.getElementById("IdDescuentoPorcentaje").value);

        descuento= valor*porcentaje/100;

        document.getElementById("IdDescuentoP").innerHTML=(descuento).toFixed(2);
        document.getElementById("IdDescuento").value=(descuento).toFixed(2);

        //total=document.getElementById("IdCopagoP").innerHTML;

        document.getElementById("IdCopagoP").innerHTML=(valor-descuento-cobertura).toFixed(2)
        document.getElementById("IdCopago").value=(valor-descuento-cobertura).toFixed(2)
    })

    function cargarReclamo(aseguradora){
        $.ajax({
            async: false,
            url: '{{ url("ordenAtencionReclamo/searchN") }}'+ '/' +aseguradora,
            dataType: "json",
            type: "GET",
            data: {
                buscar: aseguradora
            },                      
            success: function(data){  
                document.getElementById("idReclamoSec").value = data[0];
                document.getElementById("idReclamoNum").value = data[1];
            },
            error: function(data) { 
                console.log(data);       
            },
        });
        
    }
</script>
@endsection
@section('scriptCalendar')

$(function () {
    /* initialize the external events
    -----------------------------------------------------------------*/
    function ini_events(ele) {
        ele.each(function () {

            // create an Event Object (https://fullcalendar.io/docs/event-object)
            // it doesn't need to have a start or end
            var eventObject = {
            title: $.trim($(this).text()) // use the element's text as the event title
            }

            // store the Event Object in the DOM element so we can get to it later
            $(this).data('eventObject', eventObject)

            // make the event draggable using jQuery UI
            $(this).draggable({
                zIndex        : 1070,
                revert        : true, // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            })

        })
    }

    ini_events($('#external-events div.external-event'))

    /* initialize the calendar
    -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date()
    var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear()

    var Calendar = FullCalendar.Calendar;
    var Draggable = FullCalendar.Draggable;

    var containerEl = document.getElementById('external-events');
    var calendarEl = document.getElementById('calendar');

    // initialize the external events
    // -----------------------------------------------------------------

    new Draggable(containerEl, {
        itemSelector: '.external-event',
        eventData: function(eventEl) {
            return {
            title: eventEl.innerText,
            backgroundColor: window.getComputedStyle( eventEl ,null).getPropertyValue('background-color'),
            borderColor: window.getComputedStyle( eventEl ,null).getPropertyValue('background-color'),
            textColor: window.getComputedStyle( eventEl ,null).getPropertyValue('color'),
            };
        }
    });

    var calendar = new Calendar(calendarEl, {});
    calendar.render();
})

$(document).ready(function(){
    $(".fc-prev-button").click(function(){
        semana_actual--;

        console.log('semana actual '+semana_actual)
        cargarHorarioSemanal()
    })

    $(".fc-next-button").click(function(){
        semana_actual++;

        console.log('semana actual '+semana_actual)
        cargarHorarioSemanal()
    })
})



@endsection