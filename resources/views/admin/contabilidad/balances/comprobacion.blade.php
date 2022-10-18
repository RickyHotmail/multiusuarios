@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">BALANCE DE COMPROBACIÓN</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form id="idForm" class="form-horizontal" method="POST" action="{{ url("balanceComprobacion") }} ">
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
                <div class="col-sm-2">
                    <button onclick="girarGif()" type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <button onclick="setTipo('&pdf=descarga')" type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                </div>
            </div>
            <div class="form-group row">
                <label for="nombre_cuenta" class="col-sm-1 col-form-label"><center>Cuenta Inicio</center></label>
                <div class="col-sm-5">
                    <select class="custom-select select2" id="cuenta_inicio" name="cuenta_inicio" require>
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
                        <th>Saldo Anterior</th>
                        <th>Debe</th>
                        <th>Haber</th>
                        <th>Saldo Deudor</th>
                        <th>Saldo Acreedor</th>  
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)    
                        <tr>
                            <td>{{ $datos[$i]['numero'] }}<input type="hidden" name="idNum[]" value="{{ $datos[$i]['numero'] }}"/></td>
                            <td>{{ $datos[$i]['nombre'] }}<input type="hidden" name="idNom[]" value="{{ $datos[$i]['nombre'] }}"/></td>
                            <td class="text-center">$ {{ number_format($datos[$i]['saldoAnt'],2) }}<input type="hidden" name="idSal[]" value="{{ $datos[$i]['saldoAnt'] }}"/></td>
                            <td class="text-center">$ {{ number_format($datos[$i]['debe'],2) }}<input type="hidden" name="idDeb[]" value="{{ $datos[$i]['debe'] }}"/></td>
                            <td class="text-center">$ {{ number_format($datos[$i]['haber'],2) }}<input type="hidden" name="idHab[]" value="{{ $datos[$i]['haber'] }}"/></td>
                            <td class="text-center">$ {{ number_format(abs($datos[$i]['deudor']),2) }}<input type="hidden" name="idDeu[]" value="{{ $datos[$i]['deudor'] }}"/></td>
                            <td class="text-center">$ {{ number_format(abs($datos[$i]['acreedor']),2) }}<input type="hidden" name="idAcr[]" value="{{ $datos[$i]['acreedor'] }}"/></td>
                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </form>
        <hr>
        @if(isset($datos))
        <div class="form-group row">
            <label for="total_debe" class="col-sm-2 col-form-label"><center>Total Debe:</center></label>
            <div class="col-sm-2">
                <input type="text" class="form-control" value='$ {{ $totDebe }}' readonly>
            </div>
            <label for="total_haber" class="col-sm-2 col-form-label"><center>Total Haber:</center></label>
            <div class="col-sm-2">
                <input type="text" class="form-control" value='$ {{ $totHaber }}' readonly>
            </div>            
        </div>
        @endif
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