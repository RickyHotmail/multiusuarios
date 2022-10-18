@extends ('admin.layouts.admin')
@section('principal')
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
                            @if(isset($datos))
                                @for ($i = 1; $i <= count($datos); ++$i)                                 
                                    <tr class="invisible">
                                        <input type="hidden" name="idNom[]" value="{{ $datos[$i]['nom'] }}"/>
                                        <input type="hidden" name="idDoc[]" value="{{ $datos[$i]['doc'] }}"/>
                                        <input type="hidden" name="idNum[]" value="{{ $datos[$i]['num'] }}"/>
                                        <input type="hidden" name="idFec[]" value="{{ $datos[$i]['fec'] }}"/>
                                        <input type="hidden" name="idMon[]" value="{{ $datos[$i]['mon'] }}"/>
                                        <input type="hidden" name="idSal[]" value="{{ $datos[$i]['sal'] }}"/>
                                        <input type="hidden" name="idPag[]" value="{{ $datos[$i]['pag'] }}"/>
                                        <input type="hidden" name="idFep[]" value="{{ $datos[$i]['fep'] }}"/>
                                        <input type="hidden" name="idDia[]" value="{{ $datos[$i]['dia'] }}"/>
                                        <input type="hidden" name="idTip[]" value="{{ $datos[$i]['tip'] }}"/>
                                        <input type="hidden" name="idTot[]" value="{{ $datos[$i]['tot'] }}"/>
                                    </tr>
                                    <tr>
                                        @if($datos[$i]['tot'] == '1')
                                            <td style="background:  #A7CCF3;" colspan="3">{{ $datos[$i]['nom'] }}</td>
                                            <td style="background:  #A7CCF3;">{{ number_format($datos[$i]['mon'],2) }}</td>
                                            <td style="background:  #A7CCF3;">{{ number_format($datos[$i]['sal'],2) }}</td>
                                            <td style="background:  #A7CCF3;">{{ number_format($datos[$i]['pag'],2) }}</td>
                                            <td style="background:  #A7CCF3;" colspan="3"></td>  
                                        @endif
                                        @if($datos[$i]['tot'] == '2')    
                                            <td style="background:  #F3DCA7;">{{ $datos[$i]['doc'] }}</td>
                                            <td style="background:  #F3DCA7;">{{ $datos[$i]['num'] }}</td>
                                            <td style="background:  #F3DCA7;">{{ $datos[$i]['fec'] }}</td>
                                            <td style="background:  #F3DCA7;">{{ number_format($datos[$i]['mon'],2) }}</td>
                                            <td style="background:  #F3DCA7;">{{ number_format($datos[$i]['sal'],2) }}</td>
                                            <td style="background:  #F3DCA7;">{{ number_format($datos[$i]['pag'],2) }}</td>
                                            <td style="background:  #F3DCA7;">{{ $datos[$i]['fep'] }}</td>
                                            @if(Auth::user()->empresa->empresa_contabilidad == '1')
                                            <td style="background:  #F3DCA7;"><a href="{{ url("asientoDiario/ver/{$datos[$i]['dia']}") }}" target="_blank">{{ $datos[$i]['dia'] }}</a></td>   
                                            @endif
                                            <td style="background:  #F3DCA7;">{{ $datos[$i]['tip'] }}</td>   
                                        @endif 
                                        @if($datos[$i]['tot'] == '3')    
                                            <td>{{ $datos[$i]['doc'] }}</td>
                                            <td>{{ $datos[$i]['num'] }}</td>
                                            <td>{{ $datos[$i]['fec'] }}</td>
                                            <td>{{ $datos[$i]['mon'] }}</td>
                                            <td>{{ $datos[$i]['sal'] }}</td>
                                            <td>{{ number_format($datos[$i]['pag'],2) }}</td>
                                            <td>{{ $datos[$i]['fep'] }}</td>
                                            @if(Auth::user()->empresa->empresa_contabilidad == '1')
                                            <td><a href="{{ url("asientoDiario/ver/{$datos[$i]['dia']}") }}" target="_blank">{{ $datos[$i]['dia'] }}</a></td>   
                                            @endif
                                            <td>{{ $datos[$i]['tip'] }}</td>   
                                        @endif                  
                                    </tr>                                
                                @endfor
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
@endsection