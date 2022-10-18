@extends ('admin.layouts.admin')
@section('principal')

<style>
    a:hover{
        cursor: pointer
    }

    .visible{
        display: block;
    }

    .oculto{
        display: none;
    }

    .active{
        background-color: #c2d7eb !important;
    }

    .active-timeline{
        background-color: #e5a946 !important;
    }
</style>


<div class="card card-secondary">
    <div class="card-header">
        <div class="float-right">
            <button onclick="window.history.back();" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
    
        <div class="row">
            <div class="col-lg-12"> 
                <div class="form-group row">
                    <label for="sucursal" class="col-sm-1 col-form-label">Paciente:</label>
                    <div class="col-sm-4">
                        <label  class="form-control" >{{$paciente->paciente_apellidos.' '.$paciente->paciente_nombres}}</label>
                    </div>
                    <label for="seguro" class="col-sm-1 col-form-label">Aseguradora:</label>
                    <div class="col-sm-3">
                        <label class="form-control">{{$paciente->aseguradora->cliente_nombre}}</label>
                    </div>
                </div> 
                
            </div>
        </div>
        <div class="row">
            <div class="col-1">
                <ul class="nav flex-column2 nav-tabs h-100" id="myTab" role="tablist" aria-orientation="vertical">
                    <?php $primero=0 ?>
                    @foreach($tiposDetalles as $tipD)
                        <?php $primero++ ?>
                        @if($tipD->tipod_descripcion=='SIGNOS VITALES Y ANTROPOMETRIA')
                            <li class="nav-item">
                                <a class="nav-link btn btn-app2 redondo" id="signos-tab" data-toggle="tab" href="#signos" role="tab" aria-controls="signos" aria-selected="false">
                                    <span class="badge bg-purple"></span>
                                    <i class="fas fa-stethoscope"></i>
                                    Signos Vitales
                                </a>
                            </li>
                        @elseif($tipD->tipod_descripcion=='DIAGNOSTICO')
                            <li class="nav-item">
                                <a class="nav-link btn btn-app2 redondo" id="diagnostico-tab" data-toggle="tab" href="#diagnostico" role="tab" aria-controls="diagnostico" aria-selected="false">
                                    <span class="badge bg-purple"></span>
                                    <i class="fas fa-stethoscope"></i>
                                    Diagnóstico
                                </a>
                            </li>
                        @else
                            <?php $cadena = strtolower(str_replace(" ", "", $tipD->tipod_descripcion)) ?>
                            <li class="nav-item">
                                <a class="nav-link btn btn-app2 redondo @if($primero==1) active @endif" id="{{ $cadena }}-tab" data-toggle="tab" href="#{{ $cadena }}" role="tab" aria-controls="{{ $cadena }}" aria-selected="false">
                                    <span class="badge bg-purple"></span>
                                    <i class="fas fa-info-circle"></i> 
                                    {{ $tipD->tipod_descripcion }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                    
                    <li class="nav-item">
                        <a class="nav-link btn btn-app2 redondo" id="prescripcion-tab" data-toggle="tab" href="#prescripcion" role="tab" aria-controls="prescripcion" aria-selected="false"><span class="badge bg-purple"></span><i class="fas fa-prescription-bottle-alt"></i> Prescripción</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class=" nav-link btn btn-app2 redondo" id="examenes-tab" data-toggle="tab" href="#examenes" role="tab" aria-controls="examenes" aria-selected="false"><span class="badge bg-purple"></span><i class="fas fa-microscope"></i> Exámenes</a>
                    </li>
                    <li class="nav-item">
                        <a class=" nav-link btn btn-app2 redondo" id="imagenes-tab" data-toggle="tab" href="#imagenes" role="tab" aria-controls="imagenes" aria-selected="false"><span class="badge bg-purple"></span><i class="fas fa-id-card-alt"></i> Imágenes</a>
                    </li>
                </ul>
            </div>  
            <div class="col-8" style="height: 75vh; overflow-y: scroll"> 
                <div class="form-group tab-group">
                    <div class="tab-pane col-12 oculto" id="adicional" role="tabpanel" aria-labelledby="adicional-tab">
                    <div id="marco-expediente" class="col-12 col-sm-12">
                            
                            </div>
                    </div>   

                    <div class="tab-pane col-12 oculto" id="signos" role="tabpanel" aria-labelledby="signos-tab">  
                        <br> 
                        <div id="marco-signos" class="col-12 col-sm-12">
                            
                        </div>
                    </div>

                    <div class="tab-pane col-12 oculto" id="diagnostico" role="tabpanel" aria-labelledby="diagnostico-tab">  
                        <br> 
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <label  class="col-sm-12 col-form-label">Diagnóstico (CIE 10)</label>
                                <div class="select2-purple">
                                    <select class="select2" id="select22" name="DenfermedadId[]" multiple="multiple" data-placeholder="Selecione el Diagnóstico" data-dropdown-css-class="select2-purple" style="width: 100%;">
                                        <option value=3423 selected>swdas</option>
                                        <option value=333 selected>ssssss</option>
                                        @if(isset($enfermedades))
                                            @foreach($enfermedades as $enfermedad)
                                                <option value="{{$enfermedad->enfermedad_id}}">{{$enfermedad->enfermedad_codigo}} - {{$enfermedad->enfermedad_nombre}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div> 
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <label for="observacion" class="col-sm-5 col-form-label">Observación:</label>
                                    <textarea class="form-control" id="diagnostico_observacion"   name="diagnostico_observacion" > </textarea>
                                </div>
                            </div>     
                        </div>
                    </div>

                    <div class="tab-pane col-12 oculto" id="prescripcion" role="tabpanel" aria-labelledby="prescripcion-tab">
                        <br>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                            @include ('admin.citasMedicas.atencionCitas.itemMedicamento')
                                <div class="table-responsive">
                                    <table id="cargarItemPrescripcion" class="table table-striped table-hover" style="margin-bottom: 6px;">
                                        <thead>
                                            <tr class="letra-blanca fondo-azul-claro text-center">
                                                <th>Medicinas y dietas</th>
                                                <th>Cant</th>
                                                <th>Indicaciones</th>  
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <div>
                                    <div class="form-group row">
                                        <label for="recomendacion_prescripcion" class="col-sm-6 col-form-label">Recomendaciones:</label>
                                        <label for="observacion_prescripcion" class="col-sm-6 col-form-label">Observaciones:</label>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">                                
                                            <textarea type="text" class="form-control" id="recomendacion_prescripcion" name="recomendacion_prescripcion"  placeholder="Recomendaciones"></textarea>
                                        </div>
                                        <div class="col-sm-6">                                
                                            <textarea type="text" class="form-control" id="observacion_prescripcion" name="observacion_prescripcion"  placeholder="Observaciones"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="min-height: 500px" class="tab-pane col-12 oculto" id="examenes" role="tabpanel" aria-labelledby="examenes-tab">
                        <iframe style='display:none' id='frame' width='100%' height='100%' frameborder='0'></iframe>
                    </div>
                    
                    <div class="tab-pane col-12 oculto" id="imagenes" role="tabpanel" aria-labelledby="imagenes-tab">
                        <br>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                                <div class="table-responsive">
                                @include ('admin.citasMedicas.atencionCitas.itemImagen')
                                    <table id="cargarItemImagen" class="table table-striped table-hover" style="margin-bottom: 6px;">
                                        <thead>
                                            <tr class="letra-blanca fondo-azul-claro text-center">
                                                <th>Imagenes</th>
                                                <th>Indicaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>

                                    <div class="modal fade" id="modal-imagenes">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-secondary">
                                                    <h4 class="modal-title">Imagenes</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="container">
                                                    <div class="container">
                                                        <br>
                                                        <table id="example3" class="table table-bordered table-hover sin-salto" style="margin-bottom: 2px;">
                                                            <thead>
                                                                <tr class="letra-blanca fondo-azul-claro text-left">
                                                                    <th>Nombre</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $cont2 = 1;?>
                                                                @if(isset($imagenes))
                                                                @foreach($imagenes as $imagen)
                                                                    <tr class="text-left"  id="row_<?php echo $cont2; ?>">                                                                                
                                                                        <td>
                                                                            <a class="btn btn-success btn-sm" onclick="agregarItemImagen(<?php echo $cont2; ?>)"><i class="fa fa-plus"></i></a>&nbsp;&nbsp;{{ $imagen->imagen_nombre }}                                                                               
                                                                            <input class="invisible"  id="imagenNombreAux_<?php echo $cont2; ?>" name="imagenNombreAux[]" value="{{ $imagen->imagen_nombre }}" />
                                                                            <input class="invisible" id="imagenIdAux_<?php echo $cont2; ?>" name="imagenIdAux[]" value="{{ $imagen->imagen_id }}" />
                                                                        </td>
                                                                        <?php $cont2 = $cont2 + 1;?>                                   
                                                                    </tr>
                                                                @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                        <br>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label for="observacion" class="col-sm-5 col-form-label">Otros:</label>
                                    <textarea class="form-control" id="otros_imagen"   name="otros_imagen" > </textarea>
                                </div>  
                            </div> 
                        </div>

                        <iframe id="frameImagenes" height="100%" width="100%"></iframe>
                    </div>
                </div>
            </div>
        
         
            <div class="col-3" style="max-height: 75vh; ; overflow-y: scroll">
                <!-- Timeline -->
                <ul class="timeline">
                    <?php 
                        $c=0;
                    ?>

                    @foreach($historial as $historiales)
                        <?php $c++; ?>

                        <a  class="card" onclick="cargarConsultaMedica({{ $historiales->orden_id }})">
                            <li id="timeline{{ $historiales->orden_id }}" class="timeline-item bg-white rounded ml-3 p-4 shadow">
                                <div class="timeline-arrow"></div>
                                <h5 class="h7 mb-0 text-center">
                                    @if(isset($historiales->medico->empleado)) 
                                        {{$historiales->medico->empleado->empleado_nombre}} 
                                    @elseif(isset($historiales->medico->proveedor)) 
                                        {{$historiales->medico->proveedor->proveedor_nombre}} 
                                    @endif 
                                    
                                    <br>
                                    
                                    {{$historiales->especialidad->especialidad_nombre}}
                                </h5>
                                <span class="small text-center"><i class="fa fa-clock-o mr-1"></i>{{$historiales->orden_fecha}} {{$historiales->orden_hora}}</span>
                            </li>
                        </a>
                        <?php
                            if($c==1){
                                $historial_primero=$historiales->orden_id;
                            }
                        ?>
                    @endforeach

                    <script>
                        item1= <?= $historial_primero; ?>

                        if(item1){
                            setTimeout(function(){
                                cargarConsultaMedica(item1)
                                aditionalTab = document.getElementById("adicional-tab")
                                aditionalTab.click()
                            }, 300)
                        }
                    </script>
                </ul><!-- End -->
            </div>
        </div>
    </div>
</div>
<!-- /.card -->

<script>
    var borrada=false
    aditional = document.getElementById("adicional")
    signos = document.getElementById("signos")
    diagnostico = document.getElementById("diagnostico")
    prescripcion = document.getElementById("prescripcion")
    examenes = document.getElementById("examenes")
    imagenes = document.getElementById("imagenes")

    aditionalTab = document.getElementById("adicional-tab")
    signosTab = document.getElementById("signos-tab")
    diagnosticoTab = document.getElementById("diagnostico-tab")
    prescripcionTab = document.getElementById("prescripcion-tab")
    examenesTab = document.getElementById("examenes-tab")
    imagenesTab = document.getElementById("imagenes-tab")

    


    aditionalTab.addEventListener("click", function(){
        ocultarTodos();
        aditional.classList.remove("oculto")
    });

    signosTab.addEventListener("click", function(){
        ocultarTodos();
        signos.classList.remove("oculto")
    });

    diagnosticoTab.addEventListener("click", function(){
        ocultarTodos();
        diagnostico.classList.remove("oculto")
    });

    prescripcionTab.addEventListener("click", function(){
        ocultarTodos();
        prescripcion.classList.remove("oculto")
    });

    examenesTab.addEventListener("click", function(){
        ocultarTodos();
        examenes.classList.remove("oculto")
    });

    imagenesTab.addEventListener("click", function(){
        ocultarTodos();
        imagenes.classList.remove("oculto")
    });

    function ocultarTodos(){
        aditional.classList.add("oculto")
        signos.classList.add("oculto")
        diagnostico.classList.add("oculto")
        prescripcion.classList.add("oculto")
        examenes.classList.add("oculto")
        imagenes.classList.add("oculto")
    }
</script>
@endsection


    <script>
        function cargarConsultaMedica($id) {
            //control.classList.add('active')
            cargarDatos($id);

            $(".timeline-item").removeClass('active-timeline');
            $("#timeline"+$id).addClass('active-timeline')
        }

        function cargarDatos($id){
            if(!borrada){
                $('#plantillaItemImagen tr td:first').remove();
                $('#plantillaItemMedicamento tr td:first').remove();

                borrada=true
            }

            $.ajax({
                async: false,
                url: '{{ url("historialClinico/") }}/'+$id+'/informacion',
                dataType: "json",
                type: "GET",
                data: {
                    "orden_id": $id,
                },                      
                success: function(data){ 
                    console.log("Cargando "+$id)
                    console.log(data)

                   
                   
                    /////////////////////     diagnostico  //////////////////////////////////
                    select = document.getElementById("select22")
                    observacion_diagnostico = document.getElementById("diagnostico_observacion")
                    observacion_diagnostico.value=""
                    
                    /////limpiar select
                    for (let i = select.options.length; i >= 0; i--) {
                        select.remove(i);
                    }

                    if(data.diagnostico){
                        observacion_diagnostico.value=data.diagnostico.diagnostico_observacion
                        
                        //agregar enfermedades
                        if(data.diagnostico.detallediagnostico){
                            data.diagnostico.detallediagnostico.forEach(det=>{
                                const option = document.createElement('option');
                                
                                option.value =  det.enfermedad.enfermedad_id;
                                option.text = det.enfermedad.enfermedad_nombre;
                                option.selected=true

                                select.appendChild(option);
                            })
                        }
                    }







                    /////////////////////     prescripcion  //////////////////////////////////
                    
                    observacion_prescripcion = document.getElementById("observacion_prescripcion")
                    observacion_prescripcion.value=""

                    recomendacion_prescripcion = document.getElementById("recomendacion_prescripcion")
                    recomendacion_prescripcion.value=""


                    ////borrar tabla de prescripcion
                    $('#cargarItemPrescripcion tbody tr').remove();
                    //console.log('borrando')

                    if(data.prescripcion){
                        observacion_prescripcion.value=data.prescripcion.prescripcion_observacion
                        recomendacion_prescripcion.value=data.prescripcion.prescripcion_recomendacion
                        
                        //agregar medicamentos
                        if(data.prescripcion.pres_medicamento){
                            data.prescripcion.pres_medicamento.forEach(det=>{
                                var linea = $("#plantillaItemMedicamento").html();
                                //linea = linea.replace(/{ID}/g, id_itemM);
                                linea = linea.replace(/{PmedicinaNombre}/g, det.medicamento.producto.producto_nombre);
                                //linea = linea.replace(/{PproductoId}/g, document.getElementById("idProductoID").value);
                                linea = linea.replace(/{PmedicinaId}/g, det.medicamento_id);
                                linea = linea.replace(/{Pcantidad}/g, det.prescripcionm_cantidad);
                                linea = linea.replace(/{Pindicaciones}/g, det.prescripcionm_indicacion);

                                $("#cargarItemPrescripcion tbody").append(linea);
                                //id_itemM = id_itemM + 1; 
                                //$('#modal-prescripcion').modal('hide');
                            
                                //resetearCampos();
                            })
                        }
                    }

                    ////////////////////////  orden de Imagenes  ///////////////////////////////////////
                    observacion_imagen = document.getElementById("otros_imagen")
                    observacion_imagen.value=""

                    ////borrar tabla
                    $('#cargarItemImagen tbody tr').remove();
                    $iframe=document.getElementById("frameImagenes")
                    $iframe.src=""

                    if(data.imagen){
                        observacion_imagen.value=data.imagen.orden_observacion

                        if(data.imagen.detalle_imagen){
                            data.imagen.detalle_imagen.forEach(det=>{
                                var linea = $("#plantillaItemImagen").html();

                                //linea = linea.replace(/{ID}/g, det.imagen.imagen_id);
                                linea = linea.replace(/{ID}/g, det.detalle_id);
                                linea = linea.replace(/{ImagenNombre}/g, det.imagen.imagen_nombre);
                                linea = linea.replace(/{ImagenId}/g, det.imagen_id);
                                linea = linea.replace(/{Iobservacion}/g, det.detalle_indicacion);

                                $("#cargarItemImagen tbody").append(linea);

                                tr=document.getElementById("row_"+det.detalle_id)
                                td=$(tr).find("td:eq(2)")

                                var boton=document.createElement("button")
                                boton.classList.add('btn', 'btn-primary')
                                boton.innerHTML="<i class='fa fa-eye'></i>"
                                boton.setAttribute("tittle", "Ver Resultados")

                                ruc=data.ruc
                                fecha=data.fecha
                                numero=data.ordenAtencion.orden_numero

                                boton.addEventListener("click", function(){
                                    $iframe.src='{{ url("DocumentosOrdenAtencion") }}'+"/"+ruc+"/"+fecha+"/"+numero+"/Documentos/Imagenes/imagen_resultado"+det.detalle_id+"_1.pdf"
                                })
                                //cargarIframe('http://127.0.0.1:8000/DocumentosOrdenAtencion/0702932179001/28-03-2022/OA-MATRIZCOMERCIAL-000000083/Documentos/Imagenes/imagen_resultado105_1.pdf')
                                td.append(boton)
                            })
                        }
                    }
                    
                    ////////////////////////  Analisis de Laboratorio  /////////////////////////////
                    if(data.examen){
                        if(data.examen.orden_estado==2){
                            var frame = $('#frame');
                            var url = "{{ url('analisisLaboratorio') }}/"+data.examen.analisis.analisis_laboratorio_id+"/resultados";
                            frame.attr('src',url).show();

                            console.log("sadsadsadasdsad 0")
                        }
                        else{
                            console.log("sadsadsadasdsad 1")
                        }
                    }
                    else{
                        console.log("sadsadsadasdsad 2")
                    }



                    /////////////////signos vitales
                    marco_signos=document.getElementById('marco-signos')
                    
                    while (marco_signos.firstChild) {
                        marco_signos.removeChild(marco_signos.firstChild);
                    }

                    if(data.signos_vitales){
                        data.signos_vitales.forEach(det=>{
                            div = document.createElement('div')
                            div.classList.add('row', 'offset-md-4', 'col-md-4', 'mb-5')

                            label = document.createElement('label')
                            label.classList.add('mb-0')
                            label.innerHTML=det.signo_nombre

                            input = document.createElement('input')
                            input.classList.add('form-control')
                            input.value=det.signo_valor
                            
                            if(det.signo_medida)                            
                                input.value+=det.signo_medida

                            div.appendChild(label)
                            div.appendChild(input)
                            marco_signos.appendChild(div)
                        })
                    }

                    /////////////////expediente detalle
                    marco_expediente=document.getElementById('marco-expediente')
                    
                    while (marco_expediente.firstChild) {
                        marco_expediente.removeChild(marco_expediente.firstChild);
                    }

                    if(data.detalle_expediente){
                        data.detalle_expediente.forEach(det=>{
                            div = document.createElement('div')
                            div.classList.add('row', 'offset-md-2', 'col-md-8', 'mb-5')

                            label = document.createElement('label')
                            label.classList.add('mb-0')
                            label.innerHTML=det.detallee_nombre

                            input = document.createElement('textarea')
                            input.setAttribute('cols',80);
                            input.setAttribute('rows', 3);
                            input.setAttribute('rows', 3);
                            //input.style.resize='none';
                            input.classList.add('form-control')
                            input.value=det.detallee_valor
                            
                            if(det.signo_medida)                            
                                input.value+=det.detallee_medida

                            div.appendChild(label)
                            div.appendChild(input)
                            marco_expediente.appendChild(div)
                        })
                    }
                },
                error: function(data) { 
                    console.log("error "+$id)
                    console.log(data);       
                },
            });
        }
    
    </script>

