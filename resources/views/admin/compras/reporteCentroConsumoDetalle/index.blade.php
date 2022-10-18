@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Reporte de Centro de Consumosg</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form id="idForm" class="form-horizontal" method="POST" action="{{ url("listaConsumo") }} "> 
        @csrf
            <div class="float-right">
                <button onclick="setTipo('&pdf=descarga')" type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                <button onclick="setTipo('&excel=descarga')" type="submit" id="excel" name="excel" class="btn btn-success"><i class="fas fa-file-excel"></i></button>               
            </div>   
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idHECTAREAS" class="col-sm-1 col-form-label"><center>HECTAREAS:</center></label>
                <div class="col-sm-1">
                    <input type="text" class="form-control" id="hectareas" name="hectareas"  value='@if(isset($hectarea)) {{$hectarea}} @endif' required>
                </div>
                <label for="idDescripcion" class="col-sm-1 col-form-label"><center>C. Consumo : </center></label>
                
                <div class="col-sm-2">
                    <select class="form-control select2" id="idCentroc" name="idCentroc" data-live-search="true">
                    <option value="0" label>--TODOS--</option>  
                        @foreach($CentroConsumos as $CentroConsumo)
                            <option value="{{$CentroConsumo->centro_consumo_id}}" >                                
                                {{$CentroConsumo->centro_consumo_nombre}}
                            </option>
                        @endforeach
                    </select>  
                </div>
                
                <div class="col-sm-1">
                    <center><button onclick="setTipo('search')" type="submit" name="buscar" id="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>            
        </form>
        <hr>
        <table id="example4" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                   
                    <th>CONTROL PRESUPUESTAL </th>
                    @if(isset($fechas))
                        @for ($i = 1; $i <= count($fechas); ++$i)
                            <th>{{ $fechas[$i]['fecha'] }}</th>
                        @endfor
                    @endif
                    <th>Total 2022</th>
                    <th>Variacion</th>
                   
                </tr>
                
            </thead>            
            <tbody>
            <tr class="text-center neo-fondo-tabla"> 
                <td style="background:  #dee2e6; font-weight: bold;">HECTAREAS</td>
                @if(isset($fechas))
                        @for ($i = 1; $i <= count($fechas); ++$i)
                    <td style="background:  #dee2e6; font-weight: bold;">@if(isset($hectarea)) {{$hectarea}} @endif</td>
                    @endfor
                @endif
                <td style="background:  #dee2e6; font-weight: bold;">@if(isset($hectarea)) {{$hectarea}} @endif</td>
                <td style="background:  #dee2e6; font-weight: bold;">%</td>
            </tr>
            <tr class="text-center neo-fondo-tabla"> 
                <td style="background:  #dee2e6; font-weight: bold;">DIAS</td>
                <?php $tota=0?>
                @if(isset($dias))
                        @for ($i = 1; $i <= count($dias); ++$i)
                        <td style="background:  #dee2e6; font-weight: bold;">{{ $dias[$i]['fecha'] }}</td>
                        <?php $tota=$tota+$dias[$i]['fecha']?>
                    @endfor
                @endif
                <td style="background:  #dee2e6; font-weight: bold;">{{$tota}}</td>
                <td style="background:  #dee2e6; font-weight: bold;"></td>
            </tr>
            <tr class="text-center neo-fondo-tabla"> 
                <td style="background:  #dee2e6; font-weight: bold;">RUBROS DEL PRESUPUESTO</td>
                @if(isset($fechas))
                        @for ($i = 1; $i <= count($fechas); ++$i)
                    <td style="background:  #dee2e6; font-weight: bold;"></td>
                    @endfor
                @endif
                <td style="background:  #dee2e6; font-weight: bold;"></td>
                <td style="background:  #dee2e6; font-weight: bold;"></td>
            </tr>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)
                    @if($datos[$i]['tot'] == '1')
                    <tr> 
                        <td style="background:  #97cdb5; font-weight: bold;">{{ $datos[$i]['doc'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #97cdb5; font-weight: bold;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #97cdb5; font-weight: bold;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #97cdb5; font-weight: bold;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '2')
                    <tr>
                        <td>{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td >{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td>{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td>{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '3')
                    <tr>
                        <td style="background:  #ede3c5; font-weight: bold;">{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #ede3c5; font-weight: bold;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #ede3c5; font-weight: bold;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #ede3c5; font-weight: bold;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '4')
                    <tr>
                        <td style="background:  #97cdb5; font-weight: bold;">{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #97cdb5; font-weight: bold;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #97cdb5; font-weight: bold;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #97cdb5; font-weight: bold;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '5')
                    <tr>
                        <td style="background:  #ede3c5;">{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #ede3c5;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #ede3c5;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #ede3c5;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '6')
                    <tr>
                        <td style="background:  #97cdb5; font-weight: bold;">{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #97cdb5; font-weight: bold;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #97cdb5; font-weight: bold;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #97cdb5; font-weight: bold;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '7')
                    <tr>
                        <td style="background:  #ede3c5; font-weight: bold;">{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #ede3c5; font-weight: bold;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #ede3c5; font-weight: bold;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #ede3c5; font-weight: bold;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '8')
                    <tr>
                        <td style="background:  #97cdb5; font-weight: bold;">{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #97cdb5; font-weight: bold;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #97cdb5; font-weight: bold;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #97cdb5; font-weight: bold; ">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                @endfor
            @endif               
            </tbody>
        </table>
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
            if(tipo=="search"){
                girarGif()
                return
            }
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
</script>
@endsection