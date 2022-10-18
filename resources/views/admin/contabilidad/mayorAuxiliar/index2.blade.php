<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEOPAGUPA | Sistema Contable</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('admin/imagenes/logo2.ico')}}" />
    <link rel="stylesheet" href="{{ asset('admin/plugins/fontawesome-free/css/all.min.css') }}">
    
    <link rel="stylesheet" href="{{ asset('admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/dist/css/adminlte.min.css') }}">

</head>
<body>
    <div class="card card-secondary" style="position: absolute; width: 100%">
        <div class="card-header">
            <h3 class="card-title">Mayor Auxiliar..</h3>
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
            </form>
            <hr>
            <table id="example4" class="table table-bordered table-hover table-responsive sin-salto  dataTable no-footer">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th>Cuenta</th>
                        <th>Fecha</th>
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
                            $tfila=0;
                            
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
                                        <td class="text-right" style="background:  #BCC0C4;">{{ number_format($debe,2) }}</td>
                                        <td class="text-right" style="background:  #BCC0C4;">{{ number_format($haber,2) }}</td>
                                        <td  style="background:  #BCC0C4;"></td>
                                        <td style="background:  #BCC0C4;"></td>
                                        <td style="background:  #BCC0C4;"></td>
                                        <td style="background:  #BCC0C4;"></td>
                                        <td style="background:  #BCC0C4;"></td>
                                    </tr>

                                    <?php 
                                        $debe=0;
                                        $haber=0;
                                    ?>
                                @endif
                                <tr>
                                    <td style="background:  #70B1F7;">
                                        {{ $dato->cuenta_numero }}   -   {{ $dato->cuenta_nombre }}
                                    </td>
                                    <td style="background:  #70B1F7;"></td>
                                    <td style="background:  #70B1F7;"></td>
                                    <td class="text-right" style="background:  #70B1F7;"></td>
                                    <td class="text-right" style="background:  #70B1F7;"></td>

                                    <?php
                                        $saldo=0;
                                        foreach($saldos as $sd){
                                            if($sd->cuenta_id==$dato->cuenta_id){
                                                $saldo=$sd->saldo;
                                                break;
                                            }
                                        }
                                    ?>
                                    <td class="text-right" style="background:  #70B1F7;">{{ number_format($saldo,2) }}</td>
                                    <td style="background:  #70B1F7;"></td>
                                    <td style="background:  #70B1F7;"></td>
                                    <td style="background:  #70B1F7;"></td>
                                    <td style="background:  #70B1F7;"></td>
                                </tr>
                            
                            @endif
                            <?php 
                                $debe=$debe+$dato->detalle_debe;
                                $haber=$haber+$dato->detalle_haber;
                            
                                $saldo+=$dato->detalle_debe;
                                $saldo-=$dato->detalle_haber;
                            ?>
                            <tr>
                                <td></td>
                                <td>{{ $dato->diario_fecha }}</td>
                                <td class="text-center">{{ $dato->detalle_tipo_documento }} # {{ $dato->detalle_numero_documento }}</td>
                                <td class="text-right">@if($dato->detalle_debe) {{ number_format($dato->detalle_debe,2) }} @endif</td>
                                <td class="text-right">@if($dato->detalle_haber) {{ number_format($dato->detalle_haber,2) }} @endif</td>
                                <td class="text-right">{{ number_format($saldo,2) }}</td>
                                <td class="text-center">{{ $dato->diario_beneficiario }}</td>
                                <td class="text-center"><a href="{{ url('asientoDiario/ver') }}/{{$dato->diario_codigo}}" target="_blank">{{ $dato->diario_codigo }}</td>
                                <td class="text-left">{{ $dato->diario_comentario }}</td>
                                <td class="text-center">{{ $dato->sucursal_nombre }}</td>
                            </tr>

                            <?php $anterior=$actual?>
                        @endforeach

                        <tr>
                            <td style="background:  #BCC0C4;"></td>
                            <td style="background:  #BCC0C4;"></td>
                            <td style="background:  #BCC0C4;"></td>
                            <td class="text-right" style="background:  #BCC0C4;">{{ number_format($debe,2) }}</td>
                            <td class="text-right" style="background:  #BCC0C4;">{{ number_format($haber,2) }}</td>
                            <td style="background:  #BCC0C4;"></td>
                            <td style="background:  #BCC0C4;"></td>
                            <td style="background:  #BCC0C4;"></td>
                            <td style="background:  #BCC0C4;"></td>
                            <td style="background:  #BCC0C4;"></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('admin/dist/js/adminlte.js') }}"></script>




    <div id="div-gif" class="col-md-12 text-center" style="position: absolute;height: 300px; margin-top: 150px; display: none">
        <img src="{{ url('img/loading.gif') }}" width=90px height=90px style="align-items: center">
    </div>


    <script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
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

        $(document).ready(function() {
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                event.preventDefault();
                return false;
                }
            });

            setTimeout(function() {
                $(".mensajeria2").fadeOut(1500);
            },1000);
            $('.select2').select2();
        })
    </script>
    

</body>

</html>