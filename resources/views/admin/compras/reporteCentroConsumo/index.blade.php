@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Reporte de Centro de Consumoss</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listaCc") }} "> 
        @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idDescripcion" class="col-sm-1 col-form-label"><center>C. Consumo : </center></label>
                <div class="col-sm-3">
                    <select class="form-control select2" id="idCentroc" name="idCentroc" data-live-search="true">
                        @foreach($CentroConsumos as $CentroConsumo)
                            <option value="{{$CentroConsumo->centro_consumo_id}}" >                                
                                {{$CentroConsumo->centro_consumo_nombre}}
                            </option>
                        @endforeach
                    </select>  
                </div>
                <div class="col-sm-1">
                    <div class="icheck-secondary">
                        <input type="checkbox" id="idtodo" name="idtodo" @if(isset($todo)) @if($todo == '1') checked @endif @else checked @endif>
                        <label for="idtodo" style="border-right: 10px;"><center>Todo</center></label>
                    </div>  
                </div>
                <div class="col-sm-1">
                    <center><button onclick="girarGif()" type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>            
        </form>
        <hr>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th>Fecha</th>
                    <th>Documento</th>
                    <th>Numero</th>
                    <th>Proveedor</th>  
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Descripcion</th>
                    <th>Cantidad</th>                  
                    <th>Costo</th>                  
                    <th>Iva</th> 
                    <th>Total</th>    
                </tr>
            </thead>            
            <tbody>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)
                    @if($datos[$i]['tot'] == '1')
                    <tr>
                        <td style="background:  #97cdb5;">{{ $datos[$i]['fec'] }}</td>
                        <td style="background:  #97cdb5;"></td>
                        <td style="background:  #97cdb5;"></td>
                        <td style="background:  #97cdb5;"></td>
                        <td style="background:  #97cdb5;"></td>
                        <td style="background:  #97cdb5;"></td>
                        <td style="background:  #97cdb5;"></td>
                        <td style="background:  #97cdb5;"></td>
                        <td style="background:  #97cdb5;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #97cdb5;">{{ number_format($datos[$i]['iva'],2) }}</td>
                        <td style="background:  #97cdb5;">{{ number_format($datos[$i]['tol'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '2')
                    <tr>
                        <td>{{ $datos[$i]['fec'] }}</td>
                        <td>{{ $datos[$i]['doc'] }}</td>
                        <td>{{ $datos[$i]['num'] }}</td>
                        <td>{{ $datos[$i]['pro'] }}</td>
                        <td>{{ $datos[$i]['cod'] }}</td>
                        <td>{{ $datos[$i]['cat'] }}</td>
                        <td>{{ $datos[$i]['des'] }}</td>
                        <td>{{ $datos[$i]['can'] }}</td>
                        <td>{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td>{{ number_format($datos[$i]['iva'],2) }}</td>
                        <td>{{ number_format($datos[$i]['tol'],2) }}</td>
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
</script>
@endsection