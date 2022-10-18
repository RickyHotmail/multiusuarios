@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Contabilizacion Mensual</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listarContabilizado") }}">
        @csrf
            <div class="form-group row">
                <div class="col-sm-3">
                    <input type="month" name="fechames" id="fechames" class="form-control" value='<?php echo(date("Y")."-".date("m")); ?>' onclick="dias();" onchange="dias();" onkeyup="dias();">                           
                    <input type="hidden" id="fecha_desde" name="fecha_desde" value="<?php echo(date("Y")."-".date("m")); ?>">
                    <input type="hidden" id="fecha_hasta" name="fecha_hasta" value="<?php echo(date("Y")."-".date("m")); ?>">
                </div> 
                <div class="col-sm-1">         
                <button type="submit" id="extraer" name="extraer" class="btn btn-success float-right"><i class="fa fa-search"></i><span> Buscar</span></button>                   
                </div>                  
            </div>     
            <table id="example4" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center">
                        <th></th>
                        <th>Fecha</th>
                        <th>Diario Pago</th>     
                        <th>Diario Provisiones</th>              
                    </tr>
                </thead>
                <tbody>
                    @if(isset($rol))
                        @for ($i = 1; $i <= count($rol); ++$i)  
                            <tr class="text-center">
                                <td>
                                    <a href="{{ url("listarContabilizado/{$rol[$i]['pago_id']}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    <a href="{{ url("listarContabilizado/{$rol[$i]['pago_id']}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                </td>
                                <td>{{ $rol[$i]['fecha']}}</td>
                                <td><a href="{{ url("asientoDiario/ver/{$rol[$i]['pago_numero']}") }}" target="_blank">{{ $rol[$i]['pago_numero'] }}</a></td>
                                <td><a href="{{ url("asientoDiario/ver/{$rol[$i]['provisiones_numero']}") }}" target="_blank">{{ $rol[$i]['provisiones_numero'] }}</a></td>
                            </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </form>
    </div>
    <!-- /.card-body -->
</div>

<script type="text/javascript">
function cargarmetodo() {
    if('<?php echo($fechames); ?>'){  
        document.getElementById("fechames").value='<?php echo($fechames); ?>';
    }
    let fecha2 = new Date(document.getElementById("fechames").value);
    fecha2.setMinutes(fecha2.getMinutes() + fecha2.getTimezoneOffset());
    var anioactual = fecha2.getFullYear();
    var _mesactual = fecha2.getMonth()+1;
    var diasMes = new Date(anioactual, _mesactual, 0).getDate();
    if (_mesactual < 10) //ahora le agregas un 0 para el formato date
    {
    var mesactual = "0" + _mesactual;
    } else {
    var mesactual = _mesactual;
    }
   
    let fecha_minimo = anioactual + '-' + mesactual + '-01'; 
    let fecha_maximo = anioactual + '-' + mesactual + '-' + diasMes; 

    document.getElementById("fecha_desde").value = fecha_minimo;
    
    document.getElementById("fecha_hasta").value = fecha_maximo;
    

}

function dias(){
    let fecha2 = new Date(document.getElementById("fechames").value);
    fecha2.setMinutes(fecha2.getMinutes() + fecha2.getTimezoneOffset());
    var anioactual = fecha2.getFullYear();
    var _mesactual = fecha2.getMonth()+1;
    var diasMes = new Date(anioactual, _mesactual, 0).getDate();
    if (_mesactual < 10) //ahora le agregas un 0 para el formato date
    {
    var mesactual = "0" + _mesactual;
    } else {
    var mesactual = _mesactual;
    }
   
    let fecha_minimo = anioactual + '-' + mesactual + '-01'; 
    let fecha_maximo = anioactual + '-' + mesactual + '-' + diasMes; 

    document.getElementById("fecha_desde").value = fecha_minimo;
    
    document.getElementById("fecha_hasta").value = fecha_maximo;

}

</script>
@endsection