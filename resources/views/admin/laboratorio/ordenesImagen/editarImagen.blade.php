@extends ('admin.layouts.admin')
@section('principal')

<style>
    label[for="pdfImagen"]{
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        background-color: #106BA0;
        display: inline-block;
        transition: all .5s;
        cursor: pointer;
        padding: 5px 10px !important;
        text-transform: uppercase;
        width: fit-content;
        text-align: center;
    }
</style>

<form class="form-horizontal" method="post" action="{{ url('actualizarOrdenImagen') }}">
<input class="invisible" id="orden_id" name="orden_id" value="{{$orden->orden_id}}">
    @csrf
    <div class="card card-secondary" style="min-height: 600px">
        <div class="col-12">
            <div class="card card-secondary"  style="min-height: 550px">
                <div class="card-header">
                    <h3 class="card-title">Ver Resultados de Imagen Guardados</h3>
                    <div class="float-right">
                        <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                        <button type="button" onclick='history.back()' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                    </div>
                </div>
                
                    <div class="col-12">
                        <div class="card-body">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                                <div class="table-responsive">
                                    @include ('admin.citasMedicas.atencionCitas.itemImagen')
                                    
                                    <label>Busqueda:</label>

                                    <div class="row mb-3">
                                        <div class="col-md-1 text-right">
                                            <input id="idImagen" name="idImagen" type="hidden" value="0">
                                            <buttom id="btAnadirImagen" class="btn btn-success btn-sm mt-1" onclick="agregarImagen()"><i class="fa fa-plus"></i></buttom>
                                        </div>
                                        <div class="col-md-4">
                                            <input id="buscarImagen" name="buscarImagen" type="text" class="form-control" placeholder="Buscar Imagen" >
                                        </div>
                                    </div>
                                    <table id="cargarItemImagen" class="table table-striped table-hover" style="margin-bottom: 6px;">
                                        <thead>
                                            <tr class="letra-blanca fondo-azul-claro text-center">
                                                <th></th>
                                                <th>Imagenes</th>
                                                <th>Indicaciones</th>
                                                <!--th><a class="btn btn-default btn-sm float-right" style="padding: 2px 8px;" data-toggle="modal" data-target="#modal-imagenes"><i class="fa fa-plus"></i></a></th-->
                                                <th width="10"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orden->detalleImagen as $det)
                                                <tr class="text-center" id="row_{{ $det->imagen_id }}">
                                                    <td><a onclick='eliminarItem({{ $det->imagen_id }});' class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
                                                    <td>{{ $det->imagen->producto->producto_nombre }}<input class="invisible" name="ImagenNombre[]" value="Rayos x Antebrazo 4Ys-18"> <input class="invisible" name="ImagenId[]" value="{{ $det->imagen_id }}"></td>
                                                    <td><input class="form-control" name="Iobservacion[]" value="{{ $det->detalle_indicacion }}"></td>           
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label for="observacion" class="col-sm-5 col-form-label">Otros:</label>
                                    <textarea class="form-control" id="otros_imagen"   name="otros_imagen" > </textarea>
                                </div>  
                            </div> 
                        </div>
                    </div>
                
            </div>
        </div>
    </div>
</form>

<script>
    function eliminarItem(id) {
        $("#row_" + id).remove();
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
</script>
@section('scriptAjax')
    <script src="{{ asset('admin/js/ajax/autocompleteImagen.js') }}"></script>
@endsection
<script type="text/javascript">
    function cargarIframe(url){
        iframe=document.getElementById("iframe1")
        iframe.src = url;
    }
    function agregar() {
        $.ajax({
        url: '{{ url("laboratorio/searchN") }}'+'/'+$('input:radio[name=radioempleado]:checked').val(),
        dataType: "json",
        async:false,
        type: "GET",
        data: {
            buscar: $('input:radio[name=radioempleado]:checked').val()
        },
        success: function(data){
            $("#tabla > tbody").html("");
            for (var i=0; i<data.length; i++) {
                var linea = $("#plantillaItem").html();
                    linea = linea.replace(/{valor}/g, data[i].detalle_nombre );
                    linea = linea.replace(/{valorid}/g, data[i].detalle_id );
                    $.ajax({
                        url: '{{ url("laboratoriovalores/searchN") }}'+ '/' +data[i].detalle_id,
                        dataType: "json",
                        type: "GET",
                        async:false,
                        data: {
                            buscar: data[i].detalle_id
                        },
                        success: function(data){
                            if(data.length<=0){
                                linea =linea.replace(/{valor2}/g, '<input type="text" id="valores[]" name="valores[]" value="" class="form-control"  required >' );
                            }
                            else{
                                var select='';
                                for (var i=0; i<data.length; i++) {
                                    select+='<option value="'+data[i].valor_id+'" >'+data[i].valor_nombre+'</option>';
                                }
                                linea =linea.replace(/{valor2}/g, '<select class="custom-select" id="valores[]" name="valores[]"  require>'+select+'</select>' );                                
                            }
           
                        },
                    });
                    
                    $("#tabla tbody").append(linea);         
            }           
        },
    });
    }
    function clickButton(){

    $.ajax({
            type:"post",
            url:'{{ url("analisisLaboratorio") }}',
            data: 
            {  
               'idanalisis' :document.getElementById('idanalisis').value,
              
            },
            cache:false,
            success: function (html) 
            {
               alert('Data Send');
               $('#msg').html(html);
            }
            });
            return false;
     }
</script>
@endsection
