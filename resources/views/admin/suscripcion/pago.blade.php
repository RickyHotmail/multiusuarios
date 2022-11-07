@extends ('admin.layouts.admin')
@section('principal')

<style>
    label[for="fotoDeposito"]{
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

    .centrado-vertical{
        display: flex;
        align-items: center;
    }

    .tarjeta {
        background: #ddd;
        border-radius: 5px;
        padding: 10px;
    }

    .seleccionada{
        background: #ffe089;
        box-shadow:
        inset 0 -3em 3em rgba(0,0,0,0.1),
                0 0  0 2px rgb(255,255,255),
                0.3em 0.3em 1em rgba(0,0,0,0.3);
    }

    .lbl-cuenta, .lbl-titular, .lbl-cedula, .lbl-correo{
        font-weight: normal !important;
    }
</style>


<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Realizar un Pago</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Fecha de Pago</th>
                    <th>N° Documento</th>
                    <th>Valor</th>
                    <th>Estado</th>
                    <th>Comprobante</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suscripcion->pagos as $pago)
                    <tr class="text-center">
                        <td></td>
                        <td>{{ $pago->pago_fecha }}</td>
                        <td>{{ $pago->pago_documento }}</td>
                        <td>$ {{ number_format($pago->pago_valor,2) }}</td>
                        <td>
                            @if($pago->pago_estado==0)
                                <i style="color: grey" class="fa fa-exclamation-circle"></i> Aún no verificado
                            @elseif($pago->pago_estado==1)
                                <i style="color: green" class="fa fa-check-circle"></i> Verificado el {{ $pago->pago_fecha_validacion }}
                            @else
                                <i  style="color: red" class="fa fa-times"></i> Pago rechazado
                            @endif
                        </td>
                        <td>
                            <a target="_blank" href="{{url($pago->pago_comprobante)}}">ver comprobante</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<!-- /.modal -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Pago</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("pagos") }}" enctype="multipart/form-data" onsubmit="return verificarFoto()">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idPlan" class="col-sm-3 col-form-label">Plan</label>
                            <div class="col-sm-5">
                                <select id="idPlan" name="idPlan" class="form-control select2" data-live-search="true" required onchange="cambiarPrecio()">
                                    @foreach($planes as $plan)
                                        @if($plan->plan_nombre!="GRATUITO")
                                            <option value={{$plan->plan_id}}>{{$plan->plan_nombre}} ({{$plan->plan_cantidad_documentos}} documentos)</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="idPrecio" class="col-sm-3 col-form-label">Valor a Pagar</label>
                            <div class="col-sm-3">
                                <select id="idPrecio" class="form-control" data-live-search="true" disabled>
                                    @foreach($planes as $plan)
                                        @if($plan->plan_nombre!="GRATUITO")
                                            <option value={{$plan->plan_id}}>$ {{number_format($plan->plan_precio, 2)}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if(!$caducado)
                            <label style="color: #66cc55">Una vez aprobado el Pago, inciará un nuevo periodo de Suscripción</label>
                        @endif
                        <br>
                        <div class="form-group row">
                            <div onclick="cambiarCuenta(1,'Pichincha','123456789')" class="col-md-4 p-1">
                                <div id="marco1" class="p-3 tarjeta seleccionada">
                                    <div class="row centrado-vertical mb-3">
                                        <img class="ml-1 mr-1" src="{{url('img')}}/banco_pichincha.png" width="35px">
                                        <label class="lbl-banco m-0">Pichincha #123456789</label>
                                    </div>

                                    <label class="lbl-cuenta m-0">Cuenta Corriente</label>
                                    <label class="lbl-titular m-0"><strong>Paúl Guzman Pazmiño</strong></label>
                                    <label class="lbl-cedula m-0">C.I: <strong>0702932179</strong></label>
                                    <label class="lbl-correo m-0">mail: <strong>pagos@neopagupa.com</strong></label>
                                </div>
                            </div>
                            <div onclick="cambiarCuenta(2,'Machala', '123456789')" class="col-md-4 p-1">
                                <div id="marco2" class="p-3 tarjeta">
                                    <div class="row centrado-vertical mb-3">
                                        <img class="ml-1 mr-1" src="{{url('img')}}/banco_machala.png" width="35px">
                                        <label class="lbl-banco m-0">Machala #123456789</label>
                                    </div>

                                    <label class="lbl-cuenta m-0">Cuenta Ahorros</label>
                                    <label class="lbl-titular m-0"><strong>Paúl Guzman Pazmiño</strong></label>
                                    <label class="lbl-cedula m-0">C.I: <strong>0702932179</strong></label>
                                    <label class="lbl-correo m-0">mail: <strong>pagos@neopagupa.com</strong></label>
                                    </div>
                                </div>
                            <div onclick="cambiarCuenta(3,'Guayaquil', '123456789')" class="col-md-4 p-1">
                                <div id="marco3" class="p-3 tarjeta">
                                    <div class="row centrado-vertical mb-3">
                                        <img class="ml-1 mr-1" src="{{url('img')}}/banco_pichincha.png" width="35px">
                                        <label class="lbl-banco m-0">Pichincha #123456789</label>
                                    </div>

                                    <label class="lbl-cuenta m-0">Cuenta Corriente</label>
                                    <label class="lbl-titular m-0"><strong>Paúl Guzman Pazmiño</strong></label>
                                    <label class="lbl-cedula m-0">C.I: <strong>0702932179</strong></label>
                                    <label class="lbl-correo m-0">mail: <strong>pagos@neopagupa.com</strong></label>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" class="form-control" id="idBanco" name="idBanco" value="Pichincha">
                        <input type="hidden" class="form-control" id="idNumero" name="idCuenta" value="123456789">

                        

                        <div class="form-group row">
                            <label for="idDocumento" class="col-sm-3 col-form-label">Número de Documento</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="idDocumento" name="idDocumento" placeholder="# del depósito" required>
                            </div>
                        </div>

                        <div  class="form-group row">
                            <label class="col-sm-3 col-form-label">Carga el Comprobante de Pago</label>

                            <div class="col-sm-9">
                                <img style="width: 200px;" src="{{ url('img') }}/up_document.png" id="fotoDepositoP"><br>

                                <label for="fotoDeposito" ><i class='fa fa-upload' aria-hidden='true'></i> Cargar</label>
                                <input class="foto" style="display: none;" id="fotoDeposito" name="fotoDeposito" type="file"  accept=".png, .jpg, .jpeg, .gif">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.script -->
<script>
    document.getElementById("fotoDeposito").addEventListener("change", function () {
        readImage(this)
    });
    
    function readImage (input) {
        console.log("input")
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                //console.log("dir " +e.target.result)
                $('#'+input.name+'P').attr('src', e.target.result); // Renderizamos la imagen
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

<script type="text/javascript">
    function cambiarPrecio(){
        document.getElementById('idPrecio').value=document.getElementById('idPlan').value
    }

    
    function verificarFoto(){
        var file = document.getElementById("fotoDeposito");
        if(file.value==""){
            alert('Selecciona una imagen del Comprobante')
            return false;
        }
        else 
            return true;
    }

    function cambiarCuenta(id, banco, cuenta){
        document.getElementById('marco1').classList.remove('seleccionada')
        document.getElementById('marco2').classList.remove('seleccionada')
        document.getElementById('marco3').classList.remove('seleccionada')

        document.getElementById('marco'+id).classList.add('seleccionada')
        document.getElementById('idBanco').value=banco
        document.getElementById('idNumero').value=cuenta
    }
</script>