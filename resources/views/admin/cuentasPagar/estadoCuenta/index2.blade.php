<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEOPAGUPA | Sistema Contable</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('admin/imagenes/logo2.ico')}}" />
    <link rel="stylesheet" href="{{ asset('admin/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/dist/css/adminlte.min.css') }}">
</head>
<body>

<div class="card card-secondary card-tabs" style="position: absolute; width: 100%">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
            <li class="nav-item" style="margin-left: 4px">
                <a class="nav-link @if(isset($tab)) @if($tab == '1') active @endif @else active @endif" id="custom-tabs-estado-cuenta-tab" data-toggle="pill"
                    href="#custom-tabs-estado-cuenta" role="tab" aria-controls="custom-tabs-estado-cuenta"
                    aria-selected="true">Estado de Cuenta</a>
            </li>
            @if(Auth::user()->empresa->empresa_contabilidad == '1')
            <li class="nav-item">
                <a class="nav-link @if(isset($tab)) @if($tab == '2') active @endif @endif" id="custom-tabs-saldo-proveedor-tab" data-toggle="pill"
                    href="#custom-tabs-saldo-proveedor" role="tab" aria-controls="custom-tabs-saldo-proveedor"
                    aria-selected="false">Saldo a Proveedores</a>
            </li>
            @endif
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="custom-tabs-one-tabContent">
            <div class="tab-pane fade @if(isset($tab)) @if($tab == '1') show active @endif @else show active @endif" id="custom-tabs-estado-cuenta" role="tabpanel"
                aria-labelledby="custom-tabs-estado-cuenta-tab">
                <form id="idForm" class="form-horizontal" method="POST" action="{{ url("cxp/buscar") }}">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-1 col-form-label">Proveedor : </label>
                        <div class="col-sm-5">
                            <select class="custom-select select2" id="proveedorID" name="proveedorID" require>
                                <option value="0" @if(isset($proveedorC)) @if($proveedorC == 0) selected @endif @endif>Todos</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{$proveedor->proveedor_id}}" @if(isset($proveedorC)) @if($proveedorC == $proveedor->proveedor_id) selected @endif @endif>{{$proveedor->proveedor_nombre}}</option>
                                @endforeach
                            </select>  
                        </div>
                        <label for="sucursal_id" class="col-sm-1 col-form-label">Sucursal :</label>
                        <div class="col-sm-3">
                            <select class="custom-select" id="sucursal_id" name="sucursal_id" required>
                                <option value="0" @if(isset($sucurslaC)) @if($sucurslaC == 0) selected @endif @endif>Todas</option>
                                @foreach($sucursales as $sucursal)
                                <option value="{{$sucursal->sucursal_id}}" @if(isset($sucurslaC)) @if($sucurslaC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <center>
                                <button onclick="girarGif()" type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                <button onclick="setTipo('&pdf=descarga')" type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                                <button onclick="setTipo('&excel=descarga')" type="submit" id="excel" name="excel" class="btn btn-success"><i class="fas fa-file-excel"></i></button>
                            </center>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 invisible" id="filtroCorte">
                            <div class="row">
                                <label for="fecha_corte" class="col-sm-2 col-form-label">Fecha Corte:</label>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control" id="fecha_corte" name="fecha_corte"  value='<?php if(isset($fecC)){echo $fecC;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-1 invisible" id="filtroEspacio"></div>
                        <div class="col-sm-6" id="filtroFecha">
                            <div class="row">
                                <label for="fecha_desde" class="col-sm-2 col-form-label">Desde:</label>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                                </div>
                                <label for="fecha_desde" class="col-sm-2 col-form-label">Hasta:</label>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-1" id="filtroFechaTodo">
                            <div class="icheck-primary">
                                <input type="checkbox" id="fecha_todo" name="fecha_todo" @if(isset($todo)) @if($todo == 1) checked @endif @else checked @endif>
                                <!--label for="fecha_todo" class="custom-checkbox"><center>Todo</center></label-->
                            </div>                    
                        </div>
                        <div class="col-sm-5">
                            <div class="row" style="border-radius: 5px; border: 1px solid #ccc9c9;padding-top: 12px;padding-bottom: 10px;">
                                <div class="col-sm-6">
                                    <div class="custom-control custom-radio">
                                        <center>
                                            <input type="radio" class="custom-control-input" id="pago1" name="tipoConsulta" value="0" @if(isset($tipo)) @if($tipo == 0) checked @endif @else checked @endif onclick="pagos();">
                                            <label for="pago1" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">PAGOS</label>
                                        </center>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="custom-control custom-radio">
                                        <center>
                                            <input type="radio" class="custom-control-input" id="pago2" name="tipoConsulta" value="1" @if(isset($tipo)) @if($tipo == 1) checked @endif @endif onclick="pendientePago();">
                                            <label for="pago2" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">PENDIENTES DE PAGO</label>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idMonto" class="col-sm-1 col-form-label">Monto : </label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control derecha-texto" id="idMonto" name="idMonto"  value='@if(isset($mon)) {{ number_format($mon,2) }} @else 0.00 @endif'>
                        </div>
                        <div class="col-sm-1"></div>
                        <label for="idPago" class="col-sm-1 col-form-label">Pago : </label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control derecha-texto" id="idPago" name="idPago"  value='@if(isset($pag)) {{ number_format($pag,2) }} @else 0.00 @endif'>
                        </div>
                        <div class="col-sm-1"></div>
                        <label for="idSaldo" class="col-sm-1 col-form-label">Saldo : </label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control derecha-texto" id="idSaldo" name="idSaldo"  value='@if(isset($sal)) {{ number_format($sal,2) }} @else 0.00 @endif'>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover table-responsive sin-salto">
                        <thead>
                            <tr class="text-center neo-fondo-tabla">
                                <th>Documento</th>
                                <th>Numero</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Saldo</th>
                                <th>Pago</th>  
                                <th>Fecha Pago</th>
                                @if(Auth::user()->empresa->empresa_contabilidad == '1')
                                <th>Diario</th>       
                                @endif           
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $anterior_proveedor=0;
                                $anterior_factura=0;
                                $actual_proveedor=0;
                                $actual_factura=0;

                                $proveedor=0;
                                $factura=0;
                                
                                $saldo=0;
                                $debe=0;
                                $haber=0;

                                $total_monto=0;
                                $total_pago=0;
                                $total_saldo=0;
                            ?>
                            @if(isset($datos))
                                @foreach($datos as $dato)
                                    <?php 
                                        $actual_proveedor=$dato->proveedor_nombre;
                                        $actual_factura=$dato->cuenta_id;

                                        if($anterior_proveedor!=$actual_proveedor) $proveedor=1; else $proveedor=0;
                                        if($anterior_factura!=$actual_factura) $factura=1; else $factura=0;
                                    ?>
                                    
                                    @if($proveedor==1)
                                        <?php 
                                            $montoProveedor=0;
                                            $saldoProveedor=0;
                                            $pagoProveedor=0;
                                            $pagoMigradoProveedor=0;
                                            $anticipoProveedor=0;

                                            $i=array_search($dato->proveedor_nombre, array_column($saldos, 'proveedor_nombre'));
        
                                            if($i>=0){
                                                $montoProveedor=$saldos[$i]->monto;
                                                $saldoProveedor=$saldos[$i]->saldo;
                                                $pagoMigradoProveedor=$saldos[$i]->pago_migrado;

                                                if ($saldos[$i]->saldom>0) $saldoProveedor=$saldos[$i]->saldo+$saldos[$i]->saldom-$pagoMigradoProveedor;
                                            }

                                            $i=array_search($dato->proveedor_nombre, array_column($pagos, 'proveedor_nombre'));
        
                                            if($i>=0){
                                                $pagoProveedor=$pagos[$i]->pagos? $pagos[$i]->pagos:0;
                                                $anticipoProveedor=$pagos[$i]->anticipos? $pagos[$i]->anticipos:0;
                                            }
                                        ?>

                                        <tr>
                                            <td style="background:  #A7CCF3;" colspan="3">{{ $dato->proveedor_nombre }}</td>
                                            <td style="background:  #A7CCF3;">{{ number_format($montoProveedor,2) }}</td>
                                            <td style="background:  #A7CCF3;">{{ number_format($saldoProveedor,2) }}</td>
                                            <td style="background:  #A7CCF3;">{{ number_format($pagoProveedor+$anticipoProveedor,2) }}</td>
                                            <td style="background:  #A7CCF3;" colspan="3"></td>
                                        </tr>

                                        <?php 
                                            $total_monto+=round($montoProveedor,2);
                                            $total_pago+=round($pagoProveedor+$anticipoProveedor,2);
                                            $total_saldo+=round($saldoProveedor,2);
                                        ?>
                                    @endif
                                    
                                    @if($factura==1)
                                        <?php 
                                            $migrada=false;
                                            $anticipos=[];
                                            $pagos=[];
                                        ?>
                                        <tr>
                                            @if($dato->transaccion_numero)
                                                <td style="background:  #F3DCA7;">FACTURA</td>
                                                <td style="background:  #F3DCA7;">{{ $dato->transaccion_numero }}</td>
                                            @elseif($dato->lc_numero)
                                                <td style="background:  #F3DCA7;">Liquidación de Compra</td>
                                                <td style="background:  #F3DCA7;">{{ $dato->lc_numero }}</td>
                                            @elseif($dato->cabecera_ingreso_numero)
                                                <td style="background:  #F3DCA7;">Ingreso de Bodega</td>
                                                <td style="background:  #F3DCA7;">{{ $dato->cabecera_ingreso_numero }}</td>
                                            @else
                                                <td style="background:  #F3DCA7;">FACTURA</td>
                                                <td style="background:  #F3DCA7;">{{ substr($dato->cuenta_descripcion, 38) }}</td>
                                                <?php $migrada=true; ?>
                                            @endif

                                            
                                            <?php
                                                $pagoFac=0;
                                                $montoFac=0;
                                                $saldoFac=0;
                                                
                                                $encontre=false;

                                                foreach($totalF as $f){
                                                    if($f->cuenta_id==$dato->cuenta_id){
                                                        $encontre=true;
                                                        $montoFac=$f->cuenta_monto;
                                                        $saldoFac=$f->cuenta_saldo;

                                                        $pagoFac+= $f->pagos+$f->anticipos;
                                                        //if($f->cuenta_saldom>0 && $migrada) 
                                                            //$saldoFac=$f->cuenta_saldom-$pagoFac;
                                                        //else
                                                        
                                                        //if($saldoFac) $saldoFac=$f->cuenta_cuenta_monto-$pagoFac;
                                                    }
                                                    else{
                                                        if($encontre) break;

                                                    }
                                                }
                                            ?>


                                            
                                            <td style="background:  #F3DCA7;">{{ $dato->cuenta_fecha }} {{ number_format($saldoFac,2) }}</td>
                                            <td style="background:  #F3DCA7;">{{ number_format($montoFac,2) }}</td>
                                            <td style="background:  #F3DCA7;">{{ number_format($saldoFac,2) }}</td>
                                            <td style="background:  #F3DCA7;">{{ number_format($pagoFac,2) }}</td>
                                            <td style="background:  #F3DCA7;"></td>

                                            @if(Auth::user()->empresa->empresa_contabilidad == '1')
                                                <td style="background:  #F3DCA7;"><a href="{{ url("asientoDiario/ver/{$dato->diario_factura}") }}" target="_blank">{{ $dato->diario_factura }}</a></td>   
                                            @endif

                                            <td style="background:  #F3DCA7;"></td>
                                        </tr>
                                    @endif

                                    <?php
                                        $repetidoPago=false;
                                        for($i=0; $i<count($pagos); $i++){
                                            if($pagos[$i]==$dato->detalle_pago_id){
                                                $repetidoPago=true;
                                                break;
                                            }
                                        }
                                    ?>

                                    @if( !$repetidoPago)
                                        <?php $pagos[]=$dato->detalle_pago_id; ?>

                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ $dato->detalle_pago_id }}</td>
                                            <td>{{ number_format($dato->detalle_pago_valor,2) }}</td>
                                            <td>{{ $dato->pago_fecha }}</td>

                                            @if(Auth::user()->empresa->empresa_contabilidad == '1')
                                            <td>
                                                <a href="{{ url("asientoDiario/ver/{$dato->diario_codigo}") }}" target="_blank">{{ $dato->diario_codigo }}</a>
                                            </td>
                                            @endif

                                            <td>{{ $dato->detalle_pago_descripcion }}</td>
                                        </tr>
                                    @endif

                                    

                                    <?php
                                        $repetidoAnticipo=false;
                                        for($i=0; $i<count($anticipos); $i++){
                                            if($anticipos[$i]==$dato->descuento_id){
                                                $repetidoAnticipo=true;
                                                break;
                                            }
                                        }
                                    ?>

                                    @if($dato->descuento_valor && !$repetidoAnticipo)
                                        <?php $anticipos[]=$dato->descuento_id; ?>
                                        
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{$dato->descuento_id}}</td>
                                            <td>{{ number_format($dato->descuento_valor,2) }}</td>
                                            <td>{{ $dato->descuento_fecha }}</td>

                                            @if(Auth::user()->empresa->empresa_contabilidad == '1')
                                                <td><a href="{{ url("asientoDiario/ver/{$dato->diario_codigo}") }}" target="_blank">{{ $dato->diario_codigo }}</a></td>   
                                            @endif
                                            <td>DESCUENTO DE ANTICIPO DE PROVEEDOR</td>
                                        </tr>
                                    @endif

                                    <?php 
                                        $anterior_proveedor=$actual_proveedor;
                                        $anterior_factura=$actual_factura;
                                    ?>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="tab-pane fade @if(isset($tab)) @if($tab == '2') show active @endif @endif" id="custom-tabs-saldo-proveedor" role="tabpanel"
                aria-labelledby="custom-tabs-saldo-proveedor-tab">
                <form id="idForm2" class="form-horizontal" method="POST" action="{{ url("cxp/buscarSaldo") }}">
                    @csrf
                    <div class="form-group row">
                        <div class="col-sm-5">
                            <div class="row">
                                <label for="fecha_desde" class="col-sm-2 col-form-label">Desde:</label>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control" id="fecha_desde2" name="fecha_desde2"  value='<?php if(isset($fecI2)){echo $fecI2;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                                </div>
                                <label for="fecha_desde" class="col-sm-2 col-form-label">Hasta:</label>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control" id="fecha_hasta2" name="fecha_hasta2"  value='<?php if(isset($fecF2)){echo $fecF2;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                                </div>
                            </div>
                        </div>
                       
                        <label for="sucursal_id" class="col-sm-1 col-form-label">Sucursal :</label>
                        <div class="col-sm-3">
                            <select class="custom-select" id="sucursal_id2" name="sucursal_id2" required>
                                <option value="0" @if(isset($sucurslaC2)) @if($sucurslaC2 == 0) selected @endif @endif>Todas</option>
                                @foreach($sucursales as $sucursal)
                                <option value="{{$sucursal->sucursal_id}}" @if(isset($sucurslaC2)) @if($sucurslaC2 == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>
                    <div class="form-group row">
                        <label for="sucursal_id" class="col-sm-1 col-form-label">Cuenta :</label>
                        <div class="col-sm-3">
                            <select class="custom-select select2"  id="cuenta_id" name="cuenta_id" required>     
                            @foreach($cuentas as $cuenta)
                                <option value="{{$cuenta->cuenta_id}}" @if(isset($ini)) @if($ini == $cuenta->cuenta_id) selected @endif @endif>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <center>
                                <button onclick="girarGif()" type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                <button onclick="setTipo('&pdf=descarga')" type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                                <button onclick="setTipo('&excel=descarga')" type="submit" id="excel" name="excel" class="btn btn-success"><i class="fas fa-file-excel"></i></button>
                            </center>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-9"></div>
                        <label for="idSaldo" class="col-sm-1 col-form-label">Saldo : </label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control derecha-texto" id="idSaldo2" name="idSaldo2"  value='@if(isset($sal2)) {{ number_format($sal2,2) }} @else 0.00 @endif'>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover table-responsive sin-salto">
                        <thead>
                            <tr class="text-center neo-fondo-tabla">
                                <th>Ruc</th>
                                <th>Nombre</th>
                                <th>Saldo Anterior</th>
                                <th>Debe</th>
                                <th>Haber</th>
                                <th>Saldo Actual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($datosSaldo))
                                @for ($i = 1; $i <= count($datosSaldo); ++$i)                                                                   
                                        <tr>  
                                            <td>{{ $datosSaldo[$i]['ruc'] }}<input type="hidden" name="idRuc[]" value="{{ $datosSaldo[$i]['ruc'] }}"/></td>
                                            <td>{{ $datosSaldo[$i]['nom'] }}<input type="hidden" name="idNom[]" value="{{ $datosSaldo[$i]['nom'] }}"/></td>
                                            <td>{{ number_format($datosSaldo[$i]['ant'],2) }}<input type="hidden" name="idAnt[]" value="{{ $datosSaldo[$i]['ant'] }}"/></td>
                                            <td>{{ number_format($datosSaldo[$i]['deb'],2) }}<input type="hidden" name="idDeb[]" value="{{ $datosSaldo[$i]['deb'] }}"/></td>
                                            <td>{{ number_format($datosSaldo[$i]['hab'],2) }}<input type="hidden" name="idHab[]" value="{{ $datosSaldo[$i]['hab'] }}"/></td>
                                            <td>{{ number_format($datosSaldo[$i]['sal'],2) }}<input type="hidden" name="idSal[]" value="{{ $datosSaldo[$i]['sal'] }}"/></td>                    
                                        </tr>                                   
                                @endfor
                            @endif
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="div-gif" class="col-md-12 text-center" style="position: absolute;height: 300px; margin-top: 150px; display: none">
    <img src="{{ url('img/loading.gif') }}" width=90px height=90px style="align-items: center">
</div>
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

    function setTipo(t){
        tipo=t
    }

    setTimeout(function(){
        console.log("registro de la funcion")
        $("#idForm").submit(function(e) {
            if(tipo=="")  return
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

        $("#idForm2").submit(function(e) {
            if(tipo=="")  return
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

<script type="text/javascript">
    if (document.getElementById('pago1').checked) {
        pagos();
    }
    if (document.getElementById('pago2').checked) {
        pendientePago();
    }
    function pendientePago(){
        document.getElementById("filtroFecha").classList.add('invisible');
        document.getElementById("filtroFechaTodo").classList.add('invisible');
        document.getElementById("filtroCorte").classList.remove('invisible');
        document.getElementById("filtroEspacio").classList.remove('invisible');
    }
    function pagos(){
        document.getElementById("filtroFecha").classList.remove('invisible');
        document.getElementById("filtroFechaTodo").classList.remove('invisible');
        document.getElementById("filtroCorte").classList.add('invisible');
        document.getElementById("filtroEspacio").classList.add('invisible');
    }
</script>
</body>
</html>