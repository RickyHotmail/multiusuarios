@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Cargar Retenciones Recibidas</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form onsubmit="girarGif()" class="form-horizontal" method="POST" action="{{ url("retencionRecibidaXML") }} " enctype="multipart/form-data"> 
        @csrf
            <div class="form-group row">
                <label for="idDescripcion" class="col-sm-1 col-form-label"><center>Archivo SRI : </center></label>
                <div class="col-sm-10">
                    <input type="file" id="file_sri" name="file_sri" class="form-control" required/>               
                </div>
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-spinner"></i>&nbsp;&nbsp;Procesar</button></center>
                </div>
            </div>             
        </form>
        <hr>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Numero</th>
                    <th>Clave de Acceso</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody> 
                @if(isset($datos))
                    @for ($i = 1; $i <= count($datos); ++$i)   
                    <tr class="text-center">
                        <td>
                        @if($datos[$i]['estado'] == 'cargada')
                            <i class="fa fa-circle neo-azul"></i>&nbsp;&nbsp;&nbsp;&nbsp;{{ $datos[$i]['mensaje'] }}
                        @endif
                        @if($datos[$i]['estado'] == 'si')
                            <i class="fa fa-check neo-verde"></i>&nbsp;&nbsp;&nbsp;&nbsp;{{ $datos[$i]['mensaje'] }}
                        @endif
                        @if($datos[$i]['estado'] == 'no')
                            <i class="fa fa-times neo-rojo"></i>&nbsp;&nbsp;&nbsp;&nbsp;{{ $datos[$i]['mensaje'] }}
                        @endif
                        </td>
                        <td>{{ $datos[$i]['cliente'] }}</td>
                        <td>{{ $datos[$i]['fecha'] }}</td>
                        <td>{{ $datos[$i]['numero'] }}</td>
                        <td>{{ $datos[$i]['clave'] }}</td>
                        @if($datos[$i]['estado'] == 'no')
                            <td>PENDIENTES</td>
                        @else
                            <td>PROCESADAS</td>
                        @endif
                    </tr>   
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
