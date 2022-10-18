@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">ESTADO DE SITUACIÓN FINANCIERA</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form id="idForm" class="form-horizontal" method="POST"  action="{{ url("estadoFinanciero") }} ">
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
                <div class="col-sm-2">
                    <select class="custom-select select2" id="sucursal_id" name="sucursal_id" require>
                        <option value="0">Todas</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($sucursalC)) @if($sucursalC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select> 
                </div>
                <div class="col-sm-2">
                    <div class="icheck-secondary">
                        <input type="checkbox" id="arrastrar_saldos" name="arrastrar_saldos" @if(isset($arrastras)) @if($arrastras == '0') checked @endif @else checked @endif>
                        <label for="arrastrar_saldos" class="custom-checkbox"><center>Arrastrar saldos</center></label>
                    </div>                    
                </div>
                <div class="col-sm-1">
                        <button onclick="girarGif()" type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        <button onclick="setTipo('&pdf=descarga')" type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                </div>
            </div>
            <div class="form-group row">
                <label for="nivel" class="col-sm-1 col-form-label"><center>Nivel :</center></label>
                <div class="col-sm-1">
                    <select class="custom-select" id="nivel" name="nivel" require>
                        @foreach($niveles as $nivel)
                            <option value="{{$nivel->cuenta_nivel}}" @if(isset($nivelC)) @if($nivelC == $nivel->cuenta_nivel) selected @endif @endif>{{ $nivel->cuenta_nivel }}</option>
                        @endforeach
                    </select>                    
                </div>
                <label for="cuenta_inicio" class="col-sm-1 col-form-label"><center>Cuenta Inicio</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="cuenta_inicio" name="cuenta_inicio" require>
                        @foreach($cuentas as $cuenta)
                            <option value="{{$cuenta->cuenta_numero}}" @if(isset($ini)) @if($ini == $cuenta->cuenta_numero) selected @endif @endif>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                        @endforeach
                    </select>                    
                </div>
                <label for="cuenta_fin" class="col-sm-1 col-form-label"><center>Cuenta Fin</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="cuenta_fin" name="cuenta_fin" require>
                        @foreach($cuentas as $cuenta)
                            <option value="{{$cuenta->cuenta_numero}}" @if(isset($fin)) @if($fin == $cuenta->cuenta_numero) selected @endif @else @if($cuentaFinal == $cuenta->cuenta_id) selected @endif @endif>{{ $cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre }}</option>
                        @endforeach
                    </select>                    
                </div>
                <label for="cResultado" class="col-sm-1 col-form-label"><center>Resultado :</center></label>
                <div class="col-sm-3">                    
                    <select class="custom-select select2" id="cResultado" name="cResultado" require>
                        @foreach($cuentas as $cuenta)
                            <option value="{{$cuenta->cuenta_numero}}" @if(isset($resultadoC)) @if($resultadoC == $cuenta->cuenta_numero) selected @endif @else @if(isset($resultado)) @if($resultado == $cuenta->cuenta_id) selected @endif @endif @endif>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                        @endforeach
                    </select>                    
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6"> 
                </div>                
                <div class="col-sm-6">   
                    <div class="d-flex flex-row justify-content-end">
                        <span class="mr-2">
                        <i class="fas fa-square nivel-1"></i> Nivel 1
                        </span>
                        <span class="mr-2">
                        <i class="fas fa-square nivel-2"></i> Nivel 2
                        </span>
                        <span class="mr-2">
                        <i class="fas fa-square nivel-3"></i> Nivel 3
                        </span >
                        <span class="mr-2">
                        <i class="fas fa-square nivel-4"></i> Nivel 4
                        </span>
                        <span class="mr-2">
                        <i class="fas fa-square nivel-6"></i> Nivel 5
                        </span>
                        <span class="mr-2">
                        <i></i> Nivel 6
                        </span>
                    </div>
                </div>
            </div>            
            <hr>
            <table id="example4" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th>Código</th>
                        <th>Nombre</th>
                        @if(isset($sucursalC))
                            @foreach($sucuralesC as $sucursal)
                            <th>{{ $sucursal->sucursal_nombre }}</th>
                            @endforeach
                        @endif
                        <th>Total</th>  
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)    
                        <tr  style="background: @if($datos[$i]['nivel'] == 1) #C9FABE; @endif @if($datos[$i]['nivel'] == 2) #AFFFFB; @endif  @if($datos[$i]['nivel'] == 3) #D6AEF8; @endif  @if($datos[$i]['nivel'] == 4) #F9FA87; @endif  @if($datos[$i]['nivel'] == 5) #F9D07A; @endif">
                            @if($datos[$i]['nivel'] <=5)
                                <td align="left"><b>{{ $datos[$i]['numero'] }}</b><input type="hidden" name="idNum[]" value="{{ $datos[$i]['numero'] }}"/><input type="hidden" name="idNiv[]" value="{{ $datos[$i]['nivel'] }}"/></td>
                                <td align="left"><b>{{ $datos[$i]['nombre'] }}</b><input type="hidden" name="idNom[]" value="{{ $datos[$i]['nombre'] }}"/></td>                                
                                @for($j=1 ; $j <= $cantSucursal; $j++)
                                <td align="right"><b>$ {{ number_format($datos[$i][$j],2) }}</b></td>
                                @endfor
                                <td align="right"><b>$ {{ number_format($datos[$i]['total'],2) }}</b><input type="hidden" name="idTot[]" value="{{ $datos[$i]['total'] }}"/></td>
                            @else
                                <td align="left">{{ $datos[$i]['numero'] }}<input type="hidden" name="idNum[]" value="{{ $datos[$i]['numero'] }}"/><input type="hidden" name="idNiv[]" value="{{ $datos[$i]['nivel'] }}"/></td>
                                <td align="left">{{ $datos[$i]['nombre'] }}<input type="hidden" name="idNom[]" value="{{ $datos[$i]['nombre'] }}"/></td>  
                                @for($j=1 ; $j <= $cantSucursal; $j++)
                                <td align="right">$ {{ number_format($datos[$i][$j],2) }}</td>
                                @endfor
                                <td align="right">$ {{ number_format($datos[$i]['total'],2) }}<input type="hidden" name="idTot[]" value="{{ $datos[$i]['total'] }}"/></td>
                            @endif                            
                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        
            <hr>
            @if(isset($datos))
            <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                        <center><label for="totIng">Total Activo:</label></center>
                        <input type="text" class="form-control centrar-texto letra15" name="totAct" value='$ {{ number_format($totAct,2) }}' readonly>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div class="form-group">
                    <center><label class="letra15"> </label></center>
                        <center><label class="letra15"> - </label></center>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <center><label for="totEgr">Total Pasivo:</label></center>
                        <input type="text" class="form-control centrar-texto letra15" name="totPas"  value='$ {{ number_format(abs($totPas),2) }}' readonly>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div class="form-group">
                    <center><label class="letra15"> </label></center>
                        <center><label class="letra15"> - </label></center>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <center><label for="totEgr">Patrimonio Neto:</label></center>
                        <input type="text" class="form-control centrar-texto letra15" name="totPat"  value='$ {{ number_format(abs($totPat),2) }}' readonly>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div class="form-group">
                    <center><label class="letra15"> </label></center>
                        <center><label class="letra15"> = </label></center>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <center><label for="tot">Total Pasivo + Patrimonio Neto:</label></center>
                        <input type="text" class="form-control centrar-texto letra15" name="totUtil"  value='$ {{ number_format(abs($totPas) + abs($totPat) ,2) }}' readonly>
                    </div>
                </div>      
            </div>
            @endif
        </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
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
            form.append("excel", "descargar excel");
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

    function docReady(fn) {
        // see if DOM is already available
        if (document.readyState === "complete" || document.readyState === "interactive") {
            // call on next available tick
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }

        
    }    

    docReady(function() {
        console.log("cargando")

        for (var i = 2, row; row = tabla1.rows[i]; i++) {
            ultima=i
            verificarCompras(i)
        }
    });
</script>
@endsection