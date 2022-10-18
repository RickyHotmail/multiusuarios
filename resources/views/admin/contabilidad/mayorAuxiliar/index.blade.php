@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Mayor Auxiliar</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form id="idForm" class="form-horizontal" method="POST" action="{{ url("mayorAuxiliar") }} ">
            @csrf
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fDesde)){echo $fDesde;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="fecha_hasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fHasta)){echo $fHasta;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="sucursal_id" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="sucursal_id" name="sucursal_id" require>
                        <option value="0">Todas</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($sucursalC)) @if($sucursalC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select> 
                </div>
                <div class="col-sm-1">
                    <button type="submit" onclick="girarGif()" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <button onclick="setTipo('&pdf=descarga')" type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                    <button onclick="setTipo('&excel=descarga')" type="submit" id="excel" name="excel" class="btn btn-success"><i class="fas fa-file-excel"></i></button>
                </div>
            </div>
            <div class="form-group row">
                <label for="nombre_cuenta" class="col-sm-1 col-form-label"><center>Cuenta Inicio</center></label>
                <div class="col-sm-5">
                    <select class="custom-select select2" id="cuenta_inicio" name="cuenta_inicio" onchange="autoCuenta();" require>
                        @foreach($cuentas as $cuenta)
                            <option value="{{$cuenta->cuenta_numero}}" @if(isset($ini)) @if($ini == $cuenta->cuenta_numero) selected @endif @endif>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                        @endforeach
                    </select>                    
                </div>
                <label for="nombre_cuenta" class="col-sm-1 col-form-label"><center>Cuenta Fin</center></label>
                <div class="col-sm-5">
                    <select class="custom-select select2" id="cuenta_fin" name="cuenta_fin" require>
                        @foreach($cuentas as $cuenta)
                            <option value="{{$cuenta->cuenta_numero}}" @if(isset($fin)) @if($fin == $cuenta->cuenta_numero) selected @endif @else @if($cuentaFinal == $cuenta->cuenta_id) selected @endif @endif>{{ $cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre }}</option>
                        @endforeach
                    </select>                    
                </div>
            </div>
            <hr>
            <table id="example4" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Documento</th>
                        <th>Número</th>
                        <th>Debe</th>
                        <th>Haber</th>
                        <th>Saldo</th>
                        <th>Referencia</th>
                        <th>Diario</th>
                        <th>Comentario</th>
                        <th>Sucursal</th>  
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        <?php 
                            $anterior=0;
                            $actual=0;
                            $tfila=0;  //0.-cabecera  1.-detalle   2.-sumas
                            
                            $saldo=0;
                            $debe=0;
                            $haber=0;
                        ?>

                        @foreach($datos as $dato)    
                            
                            <?php 
                                $actual=$dato->cuenta_numero;

                                if($anterior!=$actual) $tfila=0;
                                if($anterior==$actual) $tfila=1;
                            ?>

                            @if($tfila==0)
                                @if($anterior>0)
                                    <tr>
                                        <td style="background:  #BCC0C4;"></td>
                                        <td style="background:  #BCC0C4;"></td>
                                        <td style="background:  #BCC0C4;"></td>

                                        <td style="background:  #BCC0C4;"></td>
                                        <td style="background:  #BCC0C4;"></td>

                                        <td class="derecha-texto" style="background:  #BCC0C4;">{{ number_format($debe,2) }}</td>
                                        <td class="derecha-texto" style="background:  #BCC0C4;">{{ number_format($haber,2) }}</td>
                                        <td  style="background:  #BCC0C4;"></td>

                                        <td style="background:  #BCC0C4;"></td>
                                        
                                        <td style="background:  #BCC0C4;"></td>
                                        <!--td style="background:  #BCC0C4;"></td-->
                                        <td style="background:  #BCC0C4;"></td>
                                    </tr>

                                    <?php 
                                        $debe=0;
                                        $haber=0;
                                    ?>
                                @endif
                                <tr>
                                    <td style="background:  #70B1F7;">{{ $dato->cuenta_numero }}<input type="hidden" name="idCod[]" value="{{ $dato->cuenta_numero }}"/></td>
                                    <td style="background:  #70B1F7;">{{ $dato->cuenta_nombre }}<input type="hidden" name="idNom[]" value="{{ $dato->cuenta_nombre }}"/></td>
                                    <td style="background:  #70B1F7;"></td>

                                    <td style="background:  #70B1F7;"></td>
                                    <td style="background:  #70B1F7;"></td>

                                    <td</td>
                                    <td  class="derecha-texto" style="background:  #70B1F7;"></td>

                                    <td  class="derecha-texto" style="background:  #70B1F7;"></td>
                                    <td class="derecha-texto" style="background:  #70B1F7;">@if($dato->saldo <> '') {{ number_format($dato->saldo,2) }} @endif<input type="hidden" name="idAct[]" value="{{ $dato->saldo }}"/></td>
                                    
                                    <td style="background:  #70B1F7;"></td>
                                    <!--td style="background:  #70B1F7;"></td-->
                                    <td style="background:  #70B1F7;"></td>
                                </tr>
                                <?php $saldo=$dato->saldo; ?>
                            @endif
                            <?php 
                                $debe=$debe+$dato->detalle_debe;
                                $haber=$haber+$dato->detalle_haber;
                            
                                $saldo+=$dato->detalle_debe;
                                $saldo-=$dato->detalle_haber;
                            ?>
                            <tr>
                                <td><input type="hidden" name="idCod[]" value="{{ $dato->cuenta_numero }}"/></td>
                                <td><input type="hidden" name="idNom[]" value="{{ $dato->cuenta_nombre }}"/></td>
                                <td>{{ $dato->diario_fecha }}<input type="hidden" name="idFec[]" value="{{ $dato->diario_fecha }}"/></td>

                                <td class="text-center">{{ $dato->detalle_tipo_documento }}<input type="hidden" name="idDoc[]" value="{{ $dato->detalle_tipo_documento }}"/></td>
                                <td class="text-center">{{ $dato->detalle_numero_documento }}<input type="hidden" name="idNum[]" value="{{ $dato->detalle_numero_documento }}"/></td>

                                <td class="derecha-texto">@if($dato->detalle_debe <> '') {{ number_format($dato->detalle_debe,2) }} @endif<input type="hidden" name="idDeb[]" value="{{ $dato->detalle_debe }}"/></td>
                                <td class="derecha-texto">@if($dato->detalle_haber <> '') {{ number_format($dato->detalle_haber,2) }} @endif<input type="hidden" name="idHab[]" value="{{ $dato->detalle_haber }}"/></td>

                                <td class="derecha-texto">@if($dato->saldo <> '') {{ number_format($saldo,2) }} @endif<input type="hidden" name="idAct[]" value="{{ $saldo }}"/></td>
                                <td class="text-center">{{ $dato->diario_beneficiario }}<input type="hidden" name="idBen[]" value="{{ $dato->diario_beneficiario }}"/></td>
                                <td class="text-center"><a href="{{ url('asientoDiario/ver') }}/{{$dato->diario_codigo}}" target="_blank">{{ $dato->diario_codigo }}</a><input type="hidden" name="idDia[]" value="{{ $dato->diario_codigo }}"/></td>

                                <!--td>{{ $dato->detalle_comentario }} {{ $dato->diario_comentario }}<input type="hidden" name="idCom[]" value="{{ $dato->detalle_comentario }} {{ $dato->diario_comentario }}"/></td-->
                                <td class="text-center" @if($tfila==0) style="background: #70B1F7;" @endif @if($tfila==2) style="background: #C0C4C5;" @endif>{{ $dato->sucursal_nombre }}<input type="hidden" name="idSuc[]" value="{{ $dato->sucursal_nombre }}"/></td>
                            </tr>

                            <?php $anterior=$actual?>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </form>
    </div>
    <!-- /.card-body -->
</div>
<div id="div-gif" class="col-md-12 text-center" style="position: absolute;height: 300px; margin-top: 150px; display: none">
    <img src="{{ url('img/loading.gif') }}" width=90px height=90px style="align-items: center">
</div>
<!-- /.card -->
<script type="text/javascript">
    function autoCuenta(){        
        $("#cuenta_fin").val(document.getElementById("cuenta_inicio").value).change()
    }
</script>
<script>
    function girarGif(){
        document.getElementById("div-gif").style.display="inline"
        console.log("girando")
    }

    function ocultarGif(){
        document.getElementById("div-gif").style.display="none"
        console.log("no girando")
    }

    tipo=""
    function setTipo(t){tipo=t}

    setTimeout(function(){
        console.log("registro de la funcion")
        $("#idForm").submit(function(e) {
            if(tipo=="") return
            var form = $(this);
            var actionUrl = form.attr('action');


            console.log("submit "+actionUrl)
            console.log(form.serialize())
            console.log(form)
            girarGif()
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize()+tipo,
                success: function(data) {
                    setTimeout(function(){
                        ocultarGif()
                        tipo=""
                    }, 1000)
                }
            });
        });
    }, 1200)
</script>
@endsection