@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Cargar Compras</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form id="idForm" class="form-horizontal" method="POST" action="{{ url("reporte/xml") }} " enctype="multipart/form-data"> 
        @csrf
            <div class="form-group row">
                <label for="idDescripcion" class="col-sm-1 col-form-label"><center>Archivo SRI : </center></label>
                <div class="col-sm-9">
                    <input type="file" id="file_sri" name="file_sri" class="form-control" required/>
                    <input type="hidden" id="puntoID" name="puntoID" value="@if(isset($punto)) {{$punto}} @endif"/>                  
                </div>
                <div class="float-right">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <a href="javascript: history.go(-1)" class="btn btn-danger"><i class="fa fa-undo"></i>&nbsp;Atras</a>  
                </div>  
            </div>             
        </form>
        <hr>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                  
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Documento</th>
                    <th>Numero</th>
                    <th>Clave de Acceso</th>
                    <th>SubTotal</th>
                    <th>Descuento</th>
                    <th>Tarifa 12 %</th>
                    <th>Tarifa 0%</th>
                    <th>Iva 12 %</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody> 
                @if(isset($datos))
                    @for ($i = 1; $i <= count($datos); ++$i)   
                    <tr class="text-center">
                       <td>{{ $datos[$i]['proveedor'] }}</td>
                        <td>{{ $datos[$i]['fecha'] }}</td>
                        <td>{{ $datos[$i]['doc'] }}</td>
                        <td>{{ $datos[$i]['numero'] }}</td>
                        <td>'{{ $datos[$i]['clave'] }}'</td>
                        <td> {{ number_format($datos[$i]['subtotal'],2) }}</td>
                        <td> {{ number_format($datos[$i]['descuento'],2) }}</td>
                        <td> {{ number_format($datos[$i]['impuesto12'],2) }}</td>
                        <td> {{ number_format($datos[$i]['impuesto0'],2) }}</td>
                        <td> {{ number_format($datos[$i]['impuesto'],2) }}</td>
                        <td> {{ number_format($datos[$i]['total'],2) }}</td>
                    </tr>   
                    @endfor
                @endif
            </tbody>
        </table>
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

    setTimeout(function(){
        console.log("registro de la funcion")
        $("#idForm").submit(function(e) {
            girarGif()
            
        });
    }, 1200)
</script>
@endsection
