@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Listado de Clientes</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">

        <div class="form-group row">
            <label class="col-sm-2">Seleccione una lista: </label>

            <form method="POST" action="{{ url("envioCorreos") }}">
                @csrf
                <select class="form-control" onchange="this.form.submit()" name="tipo_correo">
                    <option @if($tipo==1) selected @endif value=1>Clientes</option>
                    <option @if($tipo==2) selected @endif value=2>Proveedores</option>
                </select>
            </form>
        </div>

        <form method="POST" action="{{ url("enviarCorreoMasivo") }}">
            @csrf
        
            <div class="card-body">
                <div class="form-group row">
                    <label for="idTidentificacion" class="col-sm-2 col-form-label">Asunto:</label>
                    <input required class="col-sm-5" class="form-control" name="asunto">
                </div>

                <div class="form-group row">
                    <label for="textarea" class="col-sm-2 col-form-label">Body:</label>
                    <textarea name="body" class="col-md-5" required  id="body" rows="10"></textarea>
                </div>

                <div class="text-right col-sm-7">
                    <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;Enviar Correo Masivo</button>
                </div>
            </div>

            <div class="col-sm-3">
                <input onchange="cambiarEstado()" type="checkbox" id="todos">
                <label for="todos">Marcar Todos</label>
            </div>

            <table id="example10" class="table table-bordered table-hover table-responsive ">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th></th>
                        <th>Cedula</th>
                        <th>Nombre</th>
                        <th>Tipo de Cliente</th>
                        <th>Telefono</th>
                        <th>Email</th>
                    </tr>
                </thead> 
                <tbody>
                    <?php
                        function validarEmail($email) {
                            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                return true;
                            }
                            else {
                                return false;
                            }
                        }
                    ?>
                    <?php $c=0 ?>

                    @if($tipo==1)
                        @foreach($clientes as $cliente)
                            <?php if(validarEmail($cliente->cliente_email)){
                                $c++;
                            ?>
                                <tr class="text-center">
                                    <td><input id="check_<?= $c; ?>" type="checkbox" value="<?= $c?>"></input></td>
                                    <td>{{ $cliente->cliente_cedula}}</td>
                                    <td>{{ $cliente->cliente_nombre}}</td>
                                    <td>{{ $cliente->tipoCliente->tipo_cliente_nombre}}</td>
                                    <td>{{ $cliente->cliente_telefono}}</td>
                                    <td>{{ $cliente->cliente_email }} <input type="hidden" id="name_<?= $c; ?>" value="{{ $cliente->cliente_nombre }},{{ $cliente->cliente_email }}"></td>
                                </tr>
                            <?php } ?>
                        @endforeach
                    @else
                        @foreach($proveedores as $proveedor)
                            <?php if(validarEmail($proveedor->proveedor_email)){ 
                                $c++;
                            ?>
                                <tr class="text-center">
                                    <td><input id="check_<?= $c; ?>" type="checkbox" value="<?= $c?>"></td>
                                    <td>{{ $proveedor->proveedor_ruc }}</td>
                                    <td>{{ $proveedor->proveedor_nombre }}</td>
                                    <td>{{ $proveedor->tipo }}</td>
                                    <td>{{ $proveedor->proveedor_telefono }}</td>
                                    <td>{{ $proveedor->proveedor_email }}<input type="hidden" id="name_<?= $c; ?>" value="{{$proveedor->proveedor_nombre}},{{ $proveedor->cliente_email }}"></td>
                                </tr>
                            <?php } ?>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<script type="text/javascript">
    checkBox = document.addEventListener('click', event => {
        if(event.target.id!="todos"){
            if (event.target.tagName.toLowerCase() === 'input' && event.target.getAttribute('type') === 'checkbox') {
                div = (event.target.id.split("_"))
                input=document.getElementById("name_"+div[1])

                if(event.target.checked)
                    input.setAttribute("name","name[]");
                else
                    input.setAttribute("name","");
            }
        }
    });
    
    function cambiarEstado(){
        var tabla = document.getElementById("example10");
        checkTodos=document.getElementById("todos")

        for (var i = 1, row; row = tabla.rows[i]; i++) {

            input=document.getElementById("name_"+i)
            check=document.getElementById("check_"+i)

            if(todos.checked){
                input.setAttribute("name","name[]");
                check.checked=true
            }
            else{
                input.setAttribute("name","");
                check.checked=false

                console.log("aqui abajio")
            }
        }
    }

    
    

    function tipo(){
        var combo = document.getElementById("idTipoCliente");
        var idTipoCliente = combo.options[combo.selectedIndex].text;
        div = document.getElementById('tiposeguro');
        if(idTipoCliente=="Aseguradora"){
            div.style.display = '';  
            $('#idAbreviatura').prop("required", true);     
        }
        else{
            document.getElementById('idAbreviatura').value=" ";
            div.style.display = 'none';
            $('#idAbreviatura').removeAttr("required");     
        }   
    }
    
    
    function chequearMarcados(){
        var tabla = document.getElementById("example10");

        for (var i = 1, row; row = tabla.rows[i]; i++) {
            input=document.getElementById("name_"+i)

            if(document.getElementById("check_"+i).checked)
                input.setAttribute("name","name[]");
            else
                input.setAttribute("name","");
        }
    }

    function marcarTodos(){
        console.log("marcados verificar")
        var tabla = document.getElementById("example10");

        for (var i = 0, row; row = tabla.rows[i]; i++) {
            col = row.cells[0]
            console.log(col)
        }
    }

    setTimeout(function(){
        chequearMarcados()
    }, 1000)
</script>
@endsection