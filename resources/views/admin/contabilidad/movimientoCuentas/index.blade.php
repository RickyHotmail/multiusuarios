@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Movimiento de Cuentas</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST"   action="{{ url("movimientoCuenta") }} ">
        @csrf
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="fecha_hasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="nombre_cuenta" class="col-sm-1 col-form-label"><center>Cuenta</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="nombre_cuenta" name="nombre_cuenta" require>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($cuentas as $cuenta)
                            <option value="{{$cuenta->cuenta_numero}}" @if(isset($cuentaC)) @if($cuentaC == $cuenta->cuenta_numero) selected @endif  @endif>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                        @endforeach
                    </select>                    
                </div>
                <div class="col-sm-1">
                    <center><button  onclick="girarGif()" type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>
        </form>
        <hr>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th>Fecha</th>
                    <th>Debe</th>
                    <th>Haber</th>
                    <th>Saldo Actual</th>
                    <th>Diario</th>
                    <th>No. Cuenta</th>
                    <th>Cuenta</th>  
                    <th>Documento</th>  
                    <th>Comentario</th>  
                </tr>
            </thead>
            <tbody>
                <?php $saldo=0.00; $totaldebe=0.0; $totalhaber=0.0;?>
                @if(isset($detalleDiario))
                    <?php $saldo = $saldoAnterior; ?>
                    <tr class="text-center">
                        <td></td>
                        <td></td>
                        <td> </td>
                        <th> {{$saldoAnterior}} </th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @foreach($detalleDiario as $x)
                    <tr class="text-center">
                        <td>{{ $x->diario_fecha}}</td>
                        <td> <?php echo number_format($x->detalle_debe, 2)?> </td>
                        <td> <?php echo number_format($x->detalle_haber, 2)?> </td>
                        <?php 
                            $saldo = $saldo + $x->detalle_debe - $x->detalle_haber; 
                            $totaldebe = $totaldebe + $x->detalle_debe;
                            $totalhaber = $totalhaber + $x->detalle_haber;
                        ?>
                        <th> {{number_format($saldo, 2)}} </th>
                        <td><a href="{{ url("asientoDiario/ver/{$x->diario_codigo}") }}" target="_blank">{{ $x->diario_codigo}}<a></td>
                        <td>{{ $x->cuenta_numero}}</td>
                        <td>{{ $x->cuenta_nombre}}</td>
                        <td>{{ $x->detalle_tipo_documento}}</td>
                        <td>@if(isset($x->diario->egresoCaja->diario_id)) {{$x->diario->egresoCaja->egreso_descripcion}} @else {{ $x->detalle_comentario}} @endif </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <hr>
        <div class="form-group row">
            <label for="total_debe" class="col-sm-2 col-form-label"><center>Total Debe:</center></label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="total_debe" name="total_debe"  value='<?php echo  number_format($totaldebe,2) ?>' readonly>
            </div>
            <label for="total_haber" class="col-sm-2 col-form-label"><center>Total Haber:</center></label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="total_haber" name="total_haber"  value='<?php echo number_format($totalhaber,2) ?>' readonly>
            </div>  
            <label for="total_saldo" class="col-sm-2 col-form-label"><center>Saldo:</center></label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="total_saldo" name="total_saldo"  value='<?php echo number_format($saldo,2) ?>' readonly>
            </div>            
        </div>
    </div>
    <!-- /.card-body -->
</div>
<div id="div-gif" class="col-md-12 text-center" style="position: absolute;height: 300px; margin-top: 150px; display: none">
    <img src="{{ url('img/loading.gif') }}" width=90px height=90px style="align-items: center">
</div>
<script>
    function girarGif(){
        document.getElementById("div-gif").style.display="inline"
        console.log("girando")
    }
</script>
@endsection