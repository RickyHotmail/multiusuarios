@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<form class="form-horizontal" onsubmit="return comprobarDatos();" method="POST" action="{{ route('entidadProcedimiento.guardarProcedimientos', [$cliente->cliente_id]) }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Asignar Procedimmiento</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("entidadProcedimiento") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <input class="invisible" id="cliente_id" name="cliente_id" value="{{$cliente->cliente_id}}" />
            <div class="card-body">
                <div class="form-group row">
                    <label for="cliente_nombre" class="col-sm-3 col-form-label">Nombre de la Aseguradora</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="cliente_nombre" value="{{$cliente->cliente_nombre}}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="entidad_id" class="col-sm-3 col-form-label">Empresa</label>
                    <div class="col-sm-9">
                        <select class="custom-select select2" id="entidad_id" name="entidad_id" onchange="activar();" required>
                            <option value="" label>--Seleccione una opcion--</option>                               
                                @foreach($entidades as $entidad)
                                    <option value="{{$entidad->entidad_id}}">{{$entidad->entidad_nombre}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="especialidad_id" class="col-sm-3 col-form-label">Especialidad</label>
                    <div class="col-sm-9">
                        <select class="custom-select select2" id="especialidad_id" name="especialidad_id" onchange="renovarLista();" disabled required>
                            <option value="" label>--Seleccione una opcion--</option>                               
                                @foreach($especialidades as $especialidad)
                                    <option value="{{$especialidad->especialidad_id}}">{{$especialidad->especialidad_nombre}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                <hr>
                <style>
                    #caja-barra{
                        position:relative;
                        width: 202px;
                        height:12px;
                        background: #dbd1b7
                    }

                    #valor-barra{
                        position:absolute;
                        width: 0px;
                        height:10px;
                        top:1px;
                        left:1px;
                        background: #e7bd27
                    }
                </style>
                <!--div class="col-md-12">
                    <div class="offset-lg-9 col-lg-3 offset-md-6 col-md-6 text-right row">
                        <label>Progreso: </label>
                        <div class="p-2">
                            <div id="caja-barra">
                                <div id="valor-barra"></div>
                            </div>
                        </div>
                        <label id="porcentaje-barra">0</label>
                        <label>%</label>
                    </div>
                </div-->
                <div class="row">
                    <div class="offset-md-9 col-md-1 custom-control custom-checkbox pt-1">
                        <input class="custom-control-input" type="checkbox"  id="checkMarcar">
                        <label for="checkMarcar" class="custom-control-label">Marcar Todos</label>
                    </div>

                    <div class="row col-md-2">
                        <input class="form-control col-sm-3 text-right mr-2" id="porcentaje" name="porcentaje" value="0">
                        <label class="pt-1 m-0 mr-4">%</label>
                        <button type="button" onclick="marcarTodosPorcentajes()" class="btn btn-primary">Aplicar</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                        <div id="mien" class="table-responsive">
                            @include ('admin.agendamientoCitas.entidadProcedimiento.itemProcedimiento')
                            <table id="cargarItemProcedimiento" class="table table-striped table-hover" style="margin-bottom: 6px;">
                                <thead>
                                    <tr class="letra-blanca fondo-azul-claro text-center">
                                        <!--<th><input id="cbox1_" name="cbox1_" style="margin-bottom: 3px;"  type="checkbox" onclick="selectAll();" /></th>-->
                                        <th></th>
                                        <th>Codigo</th>
                                        <th>Descripcion</th>
                                        <th>Especialidad</th>
                                        <th>Precio Aseg</th> 
                                        <th>% Cobertura</th>                                              
                                        <th>Cobertura</th>                                              
                                        <th>COPAGO</th>                                              
                                    </tr>
                                </thead>
                                <tbody>
                                        
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div>
</form>
@endsection
<script>
    setTimeout(function(){
        $(document).ready(function () {  
            $('#checkMarcar').on('click', function () {
                var checked_status = this.checked;
    
                $(".miCheck").each(function () {
                    this.checked = checked_status;

                    id=this.id.split("Pcheckbox")[1]
                    console.log(id+"  -  "+checked_status+this.id)
                    
                    if(id>=0){
                        if(checked_status==true)
                            unlockedRow(id)
                        else
                            lockedRow(id);
                    }
                });
            });
        })
    }, 600)
</script>

<script>        
    var cont = 0;
    /* ----------------------- */
       
    /* ----------------------- */
    window.onload = function load(){
        cargarProcedimiento();
    }
    function activar(){
        document.getElementById("especialidad_id").disabled = false;
    }
    function renovarLista(){
        if(cont > 0){
            for(var i=0; i<cont; i++){
                $("#row_"+i).remove();
            }
        }
        cargarProcedimiento2();
    }

    /*
    function actualizarBarra(){
        actual=200/CANTIDAD_CARGAR*CARGADOS/6*2
        console.log("actual "+actual+"       cargados "+CARGADOS+"      cantidad cargar "+CANTIDAD_CARGAR)
        document.getElementById('porcentaje-barra').innerHTML=Math.round(actual)
        document.getElementById('valor-barra').style.width=Math.abs(actual)*2
    }

    function inicializarBarra(){
        //CANTIDAD_CARGAR=0;
        //CARGADOS=0;
        document.getElementById('porcentaje-barra').innerHTML=0
        document.getElementById('valor-barra').style.width=0
    }
    */
     
    function cargarProcedimiento(){   
        $.ajax({
            url: '{{ url("entidadProcedimiento/searchN") }}'+ '/' +document.getElementById("especialidad_id").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("especialidad_id").value
            },                      
            success: function(data){
                    for (var i=0; i<data.length; i++) {
                        var linea = $("#plantillaItemProcedimiento").html();
                        var cod = data[i].procedimiento_id;     
                        
                        console.log("1 ---------------")
                        linea = linea.replace(/{PcheckboxEstado}/g, 0);
                        linea = linea.replace(/{Pcheckbox}/g, 0);
                        linea = linea.replace(/{ID}/g, i);
                        linea = linea.replace(/{Pcodigo}/g, data[i].producto_codigo);
                        linea = linea.replace(/{Pdescripcion}/g, data[i].producto_nombre);
                        linea = linea.replace(/{Pprocedimiento}/g, data[i].procedimiento_id);
                        linea = linea.replace(/{Pespecialidad}/g, document.getElementById("especialidad_id").value);
                        linea = linea.replace(/{Pcliente_id}/g, document.getElementById("cliente_id").value);
                        var combo = document.getElementById("especialidad_id");
                        var especialidadNombre = combo.options[combo.selectedIndex].text; 
                        linea = linea.replace(/{PespecialidadN}/g, especialidadNombre);

                        costo = cargarProcedimientoAsignados(cod);
                        cobertura=cargarValorAsignado(cod);

                        console.log(cargarProcedimientoAsignados(cod));
                        console.log("2 ---------------")
                        console.log(cargarValorAsignado(cod));

                        linea = linea.replace(/{Pcosto}/g, costo);
                        if(data[i].grupo_nombre == "Laboratorio"){
                            linea = linea.replace(/{Ptipo}/g, "%");  
                        }else{
                            linea = linea.replace(/{Ptipo}/g, "$");
                        }                                      
                        
                        linea = linea.replace(/{Pcobertura}/g, cobertura);
                        
                        $("#cargarItemProcedimiento tbody").append(linea); 
                        var pala= "#Pcheckbox"+i;
                        if(cargarValorAsignado(cod)!=0){
                            $(pala).prop('checked', true); 
                            unlockRow(i);
                        }
                    }
                    cont = data.length
                
            },
        });
    }

    function cargarProcedimiento2(){
        $.ajax({
            url: '{{ url("procedimientosAsignadosClienteEspecialidad") }}',
            dataType: "json",
            type: "GET",
            data: {
                especialidad: document.getElementById("especialidad_id").value,
                cliente: document.getElementById("cliente_id").value,
                entidad: document.getElementById("entidad_id").value
            },                      
            success: function(data){
                console.log(data)
                
                for (var i=0; i<data.length; i++) {
                    var linea = $("#plantillaItemProcedimiento").html();
                    var cod = data[i].procedimiento_id;     
                                
                    linea = linea.replace(/{PcheckboxEstado}/g, 0);
                    linea = linea.replace(/{Pcheckbox}/g, 0);
                    linea = linea.replace(/{ID}/g, i);
                    linea = linea.replace(/{Pcodigo}/g, data[i].producto_codigo);
                    linea = linea.replace(/{Pdescripcion}/g, data[i].producto_nombre);
                    linea = linea.replace(/{Pprocedimiento}/g, data[i].procedimiento_id);
                    linea = linea.replace(/{Pespecialidad}/g, document.getElementById("especialidad_id").value);
                    linea = linea.replace(/{Pcliente_id}/g, document.getElementById("cliente_id").value);

                    var combo = document.getElementById("especialidad_id");
                    var especialidadNombre = combo.options[combo.selectedIndex].text;
                    linea = linea.replace(/{PespecialidadN}/g, especialidadNombre);

                    costo=data[i].valor?? 0;
                    porcentaje=data[i].ep_valor;

                    linea = linea.replace(/{Pcosto}/g, data[i].valor?? 0);

                    //if(data[i].grupo_nombre == "Laboratorio"){
                        linea = linea.replace(/{Ptipo}/g, "%");  
                    //}else{
                    //    linea = linea.replace(/{Ptipo}/g, "$");
                    //}                                      
                    
                    linea = linea.replace(/{Pcobertura}/g, data[i].ep_valor);
                    
                    if(data[i].ep_valor>0){
                        desc=parseFloat(costo)*parseFloat(data[i].ep_valor)/100;
                        copago=costo-desc;

                        linea = linea.replace(/{Pcobertura2}/g, desc.toFixed(2));
                        linea = linea.replace(/{Pcopago}/g, copago.toFixed(2));
                    }

                    $("#cargarItemProcedimiento tbody").append(linea);

                    if(data[i].ep_valor>0){
                        $("#Pcheckbox"+i).prop('checked', true); 
                        unlockRow(i);

                        desc=parseFloat(costo)*parseFloat(data[i].ep_valor)/100;
                        copago=costo-desc;

                        linea = linea.replace(/{Pcobertura2}/g, desc);
                        linea = linea.replace(/{Pcopago}/g, copago);

                        console.log(copago)
                    }
                    
                    
                }
                cont = data.length
                
            },
        });
    }

    function cargarProcedimientoAsignados(cod){ 
        var auxiliar = 0.0; 
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ url("procedimientosAsignados/searchN") }}',
            dataType: "json",
            async: false,
            type: "POST",
            data: {
                procedimiento: cod,
                aseguradora: document.getElementById("cliente_id").value
            },            
            success: function(data) {
                //if(data!="") {
                    if (data[0].procedimientoA_valor > 0){
                        auxiliar = data[0].procedimientoA_valor;                                       
                    }
                    console.log("cargando procedimiento asignado "+data)
                //}

            },
            error: function(){ 
                console.log("error petición ajax");
            },            
        });
        return auxiliar;          
    } 
    function cargarValorAsignado(cod){ 
        var auxiliar = 0.0; 
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ url("valorAsignado/searchN") }}',
            dataType: "json",
            async: false,
            type: "POST",
            data: {
                procedimiento: cod,
                entidad: document.getElementById("entidad_id").value
            },            
            success: function(data) {
                //if (data != ""){
                    console.log("data[0] "+data[0])
                    auxiliar = data[0].ep_valor;
                //}

            },
            error: function(){ 
                console.log("error petición ajax");
            },            
        });
        return auxiliar;          
    }

    function marcarTodosPorcentajes(){
        var nFilas = $("#cargarItemProcedimiento tbody tr").length;

        for(f=0; f<nFilas; f++){
            if ($("#Pcheckbox"+f).is(':checked')){
                $("#Pcobertura"+f).val($("#porcentaje").val())
            }
        }
    }

    function unlockRow(id){                 
        if ($("#Pcheckbox"+id).is(':checked') ) {                
            $("#Ptipo"+id).css("background-color", "white");                    
            $("#Pcobertura"+id).css("background-color", "white");
            $("#Ptipo"+id).attr("readonly",false);
            $("#Pcobertura"+id).attr("readonly",false);
            document.getElementById("PcheckboxEstado"+id).value = "1";                
        } else {
            $("#Ptipo"+id).css("background-color", "#e9ecef;");
            $("#Pcobertura"+id).css("background-color", "#e9ecef;");
            $("#Pcobertura"+id).attr("readonly",true);
            $("#Ptipo"+id).attr("readonly",true);
            document.getElementById("PcheckboxEstado"+id).value = "0";
            document.getElementById("Pcobertura"+id).value = "0";
        }
    }

    function unlockedRow(id){                 
            $("#Ptipo"+id).css("background-color", "white");                    
            $("#Pcobertura"+id).css("background-color", "white");
            $("#Ptipo"+id).attr("readonly",false);
            $("#Pcobertura"+id).attr("readonly",false);
            document.getElementById("PcheckboxEstado"+id).value = "1";
    }

    function lockedRow(id){                 
        $("#Ptipo"+id).css("background-color", "#e9ecef;");
        $("#Pcobertura"+id).css("background-color", "#e9ecef;");
        $("#Pcobertura"+id).attr("readonly",true);
        $("#Ptipo"+id).attr("readonly",true);
        document.getElementById("PcheckboxEstado"+id).value = "0";
        document.getElementById("Pcobertura"+id).value = "0";
    }


    function comprobarDatos(){
        var nFilas = $("#cargarItemProcedimiento tbody tr").length;

        for(f=0; f<nFilas; f++){
            if ($("#Pcheckbox"+f).is(':checked')){
                if(parseFloat($("#Pcobertura"+f).val())==0){
                    alert("A marcar una casilla debe ingresar la información")
                    $("#Pcobertura"+f).focus();
                    return false;
                }
            }
        }

        return true;
    }
</script>