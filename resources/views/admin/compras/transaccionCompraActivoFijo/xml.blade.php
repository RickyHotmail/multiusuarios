@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Cargar Compras</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("comprasactivofijo/xml") }} " enctype="multipart/form-data"> 
        @csrf
            <div class="form-group row">
                <label for="idDescripcion" class="col-sm-1 col-form-label"><center>Archivo SRI : </center></label>
                <div class="col-sm-10">
                    <input type="file" id="file_sri" name="file_sri" class="form-control" required/>
                    <input type="hidden" id="puntoID" name="puntoID" value="@if(isset($punto)) {{$punto}} @endif"/>                  
                </div>
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>             
        </form>
        <hr>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Documento</th>
                    <th>Numero</th>
                    <th>Clave de Acceso</th>
                </tr>
            </thead>
            <tbody> 
                @if(isset($datos))
                    @for ($i = 1; $i <= count($datos); ++$i)   
                    <tr class="text-center">
                        <td><a href="{{ url("comprasactivofijo/xmlProcesar/{$datos[$i]['clave']}/{$punto}") }}" class="btn btn-sm btn-success"  data-toggle="tooltip" data-placement="top" title="Cargar Factura"><i class="fas fa-file-upload" aria-hidden="true"></i></a>
                            <a href="{{ url("comprasactivofijo/xmlProducto/{$datos[$i]['clave']}/{$punto}") }}" class="btn btn-sm btn-primary"  data-toggle="tooltip" data-placement="top" title="Cargar Prodcutos"><i class="fas fa-file-upload" aria-hidden="true"></i></a></td>
                        <td>{{ $datos[$i]['proveedor'] }}</td>
                        <td>{{ $datos[$i]['fecha'] }}</td>
                        <td>{{ $datos[$i]['doc'] }}</td>
                        <td>{{ $datos[$i]['numero'] }}</td>
                        <td>{{ $datos[$i]['clave'] }}</td>
                    </tr>   
                    @endfor
                @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection
