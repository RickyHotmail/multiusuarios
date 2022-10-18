@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("depositoCaja") }}">
    @csrf
<div class="card card-secondary col-sm-8">
    <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title">Deposito de Caja</h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm"><i
                                class="fa fa-save"></i><span> Guardar</span></button>
                        <button type="button" id="cancelarID" name="cancelarID" onclick="javascript:location.reload()"
                            class="btn btn-default btn-sm not-active-neo"><i
                                class="fas fa-times-circle"></i><span> Cancelar</span></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- /.card-header -->
    <div class="card-body">
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Cuenta</h5>           
            <div class="card-body">
                <div class="form-group row">
                    <label for="idFecha" class="col-sm-2 col-form-label">Fecha</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="idFecha" name="idFecha" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idCaja" class="col-sm-2 col-form-label">Caja</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="caja_id" name="caja_id" required>
                            <option value="" label>--Seleccione una caja--</option>
                            @if($cajasxusuario)
                            @foreach($cajas as $caja)
                            @if($caja->caja_id == $cajasxusuario->caja_id)
                            <option value="{{$caja->caja_id}}" selected>{{$caja->caja_nombre}}</option>
                            @endif
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Banco</h5>   
            <div class="card-body">
            <div class="form-group row">
                            <label for="banco_id" class="col-sm-2 col-form-label">Banco</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="banco_id" name="banco_id" onclick="cargarCuenta();" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($bancos as $banco)
                                        <option value="{{$banco->banco_id}}">{{$banco->bancoLista->banco_lista_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                    </div>
                    <div class="form-group row">
                            <label for="cuenta_id" class="col-sm-2 col-form-label"># de Cuenta</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="cuenta_id" name="cuenta_id" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                </select>
                            </div>
                    </div> 
                <div class="form-group row">
                    <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idValor" name="idValor" placeholder = "0.00" required>
                    </div>
                </div>  
                <div class="form-group row">
                    <label for="idMensaje" class="col-sm-2 col-form-label">Descripcion</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idMensaje" name="idMensaje" required>
                    </div>
                </div> 
            </div>
        </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection
<script type="text/javascript">
function cargarCuenta(){
    $.ajax({
        url: '{{ url("cuentaBancaria/searchN") }}'+ '/' +document.getElementById("banco_id").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("banco_id").value
        },
        success: function(data){
            document.getElementById("cuenta_id").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            for (var i=0; i<data.length; i++) {
                document.getElementById("cuenta_id").innerHTML += "<option value='"+data[i].cuenta_bancaria_id+"'>"+data[i].cuenta_bancaria_numero+"</option>";
            }     
        },
    });
}
</script>