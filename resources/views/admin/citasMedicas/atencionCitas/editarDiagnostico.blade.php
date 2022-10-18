@extends ('admin.layouts.admin')
@section('principal')

<style>
    .active{
        background-color: #c2d7eb !important;
    }
</style>
<form class="form-horizontal" method="POST" action="{{ url("actualizarDiagnosticoOrdenAtencion") }}/{{ $ordenAtencion->orden_id }}" enctype="multipart/form-data">
    @csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title negrita mt-1">Editar Diagnóstico</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='history.back()' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
            <div class="col-sm-12">
                <input class="invisible" id="expediente_id" name="expediente_id" value="{{$ordenAtencion->expediente->expediente_id}}">
                <div class="form-group row">
                    <label for="sucursal" class="col-sm-1 col-form-label">Paciente:</label>
                    <div class="col-sm-3">
                        <label  class="form-control" >{{$ordenAtencion->paciente->paciente_apellidos.' '.$ordenAtencion->paciente->paciente_nombres}}</label>
                    </div>
                   

                    <label for="fecha_hora" class="col-sm-1 col-form-label">Fecha/Hora:</label>
                    <div class="col-sm-2">
                        <input type="date" class="form-control" id="fecha" name="fecha" value="{{$ordenAtencion->orden_fecha}}" readonly>
                    </div>
                    <div class="col-sm-1">
                        <input type="time" class="form-control" id="hora" name="hora" value="{{$ordenAtencion->orden_hora}}" readonly>
                    </div>
                </div>
                <div class="row">
                    <label for="especialidad" class="col-sm-1 col-form-label">Especialidad:</label>
                    <div class="col-sm-3">
                        @foreach($especialidades as $especialidad)
                            @if($especialidad->especialidad_id == $ordenAtencion->especialidad_id)
                                <label  class="form-control" >{{$especialidad->especialidad_nombre}}</label>
                            @endif
                        @endforeach
                    </div>

                    <label for="medico" class="col-sm-1 col-form-label">Medico:</label>
                    <div class="col-sm-3">
                    <input type="text" class="form-control" id="medico" value="@if(isset($medico->empleado)) {{$medico->empleado->empleado_nombre}} @else {{$medico->proveedor->proveedor_nombre}} @endif" readonly>
                    </div>
                </div> 
            </div>
                
            <div class="col-sm-11">
                <div class="form-group">
                    <div class="well listview-pagupa">                      
                            <!--Diagnóstico-->
                            <div class="tab-pane fade show" id="diagnostico" role="tabpanel" aria-labelledby="diagnostico-tab" style="background: white !important">  
                                <br> 
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label  class="col-sm-12 col-form-label">Diagnóstico (CIE 10)</label>
                                        <div class="select2-purple">
                                            <select class="select2" id="select22" name="DenfermedadId[]" multiple="multiple" data-placeholder="Selecione el Diagnóstico" data-dropdown-css-class="select2-purple" style="width: 100%;">
                                                @foreach($enfermedades as $enfermedad)
                                                    <option 
                                                        value="{{$enfermedad->enfermedad_id}}"
                                                        @foreach($diagnostico as $diag)
                                                            @if($diag->enfermedad->enfermedad_codigo==$enfermedad->enfermedad_codigo)
                                                                selected
                                                            @endif
                                                        @endforeach
                                                    >
                                                    {{$enfermedad->enfermedad_codigo}} - {{$enfermedad->enfermedad_nombre}}
                                                    </option>
                                                @endforeach
                                        
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label for="observacion" class="col-sm-5 col-form-label">Observación:</label>
                                            <textarea class="form-control" id="diagnostico_observacion"   name="diagnostico_observacion">{{ $observacion }}</textarea>
                                        </div>
                                    </div>     
                                </div>
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
<style type="text/css">   
   .mayus { text-transform: uppercase;}    
</style> 

@section('scriptAjax')
<script src="{{ asset('admin/js/ajax/autocompleteProductoMedicamento.js') }}"></script>
<script src="{{ asset('admin/js/ajax/autocompleteImagen.js') }}"></script>

@endsection

<script>
    cantidadImagenes=0;

    function borrarMarcoImagen(id){
        $("#"+id).remove();
    }

    function comprobarStockPrescripcion(){
        resultado= true
       
        //recorro cada medicamento para conocer el stock
        $("#cargarItemPrescripcion tbody tr").each(function(){
            celdaId= jQuery(this).find("td:eq(1)");
            celdaCant= jQuery(this).find("td:eq(2)");
            
            id= celdaId.children().eq(1).val()
            cant= celdaCant.children().eq(0).children().eq(0).val()

            //////extraer el producto para ver el stok
            $.ajax({
                url: "/medicinas/searchId/"+id,
                dataType: "json",
                type: "GET",
                async: false,
                data: {
                    buscar: id
                },
                success: function(data){
                    if(data.producto_stock<cant){
                        alert('El producto '+data.producto_nombre+' ya no tiene el Stock('+data.producto_stock+'    '+cant+') suficiente para poder Continuar');
                        resultado=false
                    }
                }
            });

            if(resultado==false)
                return false   //salir del Each
        })
        
        return resultado
    }

    window.addEventListener('paste', e => {
        cantidadImagenes++;
        
        var fileInput = document.createElement('input')
        $(fileInput).attr('id', 'imagefile_'+cantidadImagenes)
        $(fileInput).attr('name', 'imagefile[]')
        $(fileInput).attr('type', 'file')
        $(fileInput).css('visibility', 'hidden')

        fileInput.files = e.clipboardData.files;
        const file = e.clipboardData.files[0];
        
        
        size=bytesToSize(file.size)
        type=file.type

        var linea = $("#template").html();

        
        if(type=="image/png" || type=="image/jpg"){
            linea = linea.replace(/imageFile_0:,/g, 'imagefile_'+cantidadImagenes);
            linea = linea.replace(/marcodelete:,/g, "marco"+cantidadImagenes);

            fileInput.files = e.clipboardData.files;
            const objectURL = window.URL.createObjectURL(fileInput.files[0])
            linea = linea.replace(/data:,/g, objectURL);
            linea = linea.replace(/tamano:,/g, size);
            linea = linea.replace(/error:,/g, '');
            linea = linea.replace(/marco:,/g, "marco"+cantidadImagenes);

            $("#previews").append(linea);
            $("#marco"+cantidadImagenes).append(fileInput);
        }
        else{
            linea = linea.replace(/tamano:,/g, '0.00Kb');
            linea = linea.replace(/error:,/g, 'No es imagen!');
        }
    });

    function bytesToSize(bytes) {
        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes == 0) return '0 Byte';
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }

    
</script>
<script>
    var id_item = 0;       
    var idMed = 1;       
    function agregarItemDiagnostico(idDiag) {    
        var linea = $("#plantillaItemEnfermedad").html();
        linea = linea.replace(/{ID}/g, id_item);
        linea = linea.replace(/{DenfermedadNombre}/g, document.getElementById("enfermedadNombreAux_"+idDiag).value);
        linea = linea.replace(/{DenfermedadId}/g, document.getElementById("enfermedadIdAux_"+idDiag).value);
        linea = linea.replace(/{DobservacionEnfer}/g, "");
        linea = linea.replace(/{DcboxCasoN}/g);
        linea = linea.replace(/{DcboxDefinitivo}/g);
        linea = linea.replace(/{DcboxCasoNEstado}/g, 0);
        linea = linea.replace(/{DcboxDefinitivoEstado}/g, 0);

        $("#cargarItemDiagnostico tbody").append(linea);
        id_item = id_item + 1; 
        $('#modal-diagnostico').modal('hide');
    }
    
    function eliminarItem(id) {
        $("#row_" + id).remove();
    }

    var id_itemM = 0;  
    function agregarItemPrescripcion() {
        if (document.getElementById("id_disponible").value > 0) {
            paso=true

            if(document.getElementById("codigoProducto").value==""){
                alert('Busque un examen para agregarlo en la lista')
                paso=false
            }
            else{
                $("#cargarItemPrescripcion tbody tr").each(function(){
                    celda= jQuery(this).find("td:eq(1)");
                    id= celda.children().eq(1).val()
    
                    if(id==document.getElementById("idProductoID").value){
                        alert("Este item ya esta en la Lista")
                        paso=false
                        return true
                    }
                })
            }
                
            if(paso){
                if (Number(document.getElementById("id_disponible").value) >= Number(document.getElementById("id_cantidad").value)) {
                    var linea = $("#plantillaItemMedicamento").html();
                    linea = linea.replace(/{ID}/g, id_itemM);
                    linea = linea.replace(/{PmedicinaNombre}/g, document.getElementById("buscarProducto").value);
                    linea = linea.replace(/{PproductoId}/g, document.getElementById("idProductoID").value);
                    linea = linea.replace(/{PmedicinaId}/g,document.getElementById("idmedicamento").value);
                    linea = linea.replace(/{Pcantidad}/g,document.getElementById("id_cantidad").value);
                    linea = linea.replace(/{Pindicaciones}/g, "");

                    $("#cargarItemPrescripcion tbody").append(linea);
                    id_itemM = id_itemM + 1; 
                    $('#modal-prescripcion').modal('hide');
                
                    resetearCampos();
                }
            }
        }
    }

    function resetearCampos() {
        document.getElementById("id_cantidad").value = 1;
        document.getElementById("codigoProducto").value = "";
        document.getElementById("idProductoID").value = "";
        document.getElementById("buscarProducto").value = "";
        document.getElementById("id_disponible").value = "0";

    }

    var id_itemI = 0; 
    function agregarItemImagen(idImag) {
        paso=true
        $("#cargarItemImagen tbody tr").each(function(){
            celda= jQuery(this).find("td:eq(1)");
            id= celda.children().eq(1).val()

            if(id==idImag){
                alert("El examen de imagen "+document.getElementById("imagenNombreAux_"+idImag).value+" ya esta Ingresado en la Orden")
                paso=false
                return true
            }
        })

        if(paso){
            var linea = $("#plantillaItemImagen").html();
            linea = linea.replace(/{ID}/g, id_itemI);
            linea = linea.replace(/{ImagenNombre}/g, document.getElementById("imagenNombreAux_"+idImag).value);
            linea = linea.replace(/{ImagenId}/g, document.getElementById("imagenIdAux_"+idImag).value);
            linea = linea.replace(/{Iobservacion}/g, "");

            $("#cargarItemImagen tbody").append(linea);
            id_itemI = id_itemI + 1; 
            $('#modal-imagenes').modal('hide');
        }   
    }

    function agregarImagen() { 
        paso=true
        id=$("#idImagen").val();
        nombreImagen=$("#buscarImagen").val();

        $("#cargarItemImagen tbody tr").each(function(){
            celda= jQuery(this).find("td:eq(1)");
            idcelda= celda.children().eq(1).val()

            if(id==idcelda){
                paso=false
                return true
            }
        })
        
        if(parseInt(id)>0){
            if(paso){
                var linea = $("#plantillaItemImagen").html();
                linea = linea.replace(/{ID}/g, id);
                linea = linea.replace(/{ImagenNombre}/g, nombreImagen);
                linea = linea.replace(/{ImagenId}/g, id);
                linea = linea.replace(/{Iobservacion}/g, "");

                $("#cargarItemImagen tbody").append(linea);
                //id_itemI = id_itemI + 1; 
                //$('#modal-imagenes').modal('hide');

                $('#idImagen').val('0');
                $('#buscarImagen').val('');
            }
            else
                alert("El examen de imagen "+nombreImagen+" ya esta Ingresado en la Orden")
        }
        
    }

    var id_itemP = 0; 
    function agregarItemFacturacion(id_itemP) {    
        var linea = $("#plantillaItemFacturacion").html();
        linea = linea.replace(/{ID}/g, id_itemP);
        linea = linea.replace(/{FproductoNombre}/g, document.getElementById("productoNombreAux_"+id_itemP).value);
        linea = linea.replace(/{FproductoId}/g, document.getElementById("productoIdAux_"+id_itemP).value);
        linea = linea.replace(/{FprocedimientoAId}/g, document.getElementById("procedimientoAIdAux_"+id_itemP).value);
        linea = linea.replace(/{Fobservacion}/g, "");
        linea = linea.replace(/{Fcosto}/g, document.getElementById("productoCostoAux_"+id_itemP).value);
        
        $("#cargarItemFacturacion tbody").append(linea);
        id_itemP = id_itemP + 1; 
        $('#modal-facturacion').modal('hide');
    }
</script>

@endsection


