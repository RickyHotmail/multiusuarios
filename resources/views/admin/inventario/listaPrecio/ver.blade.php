@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Ver Lista de Precios</h3>
        <!--   
        <button onclick='window.location = "{{ url("listaPrecio") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        --> 
        <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <label class="col-sm-2 col-form-label">Nombre</label>
            <div class="col-sm-10">
                <label class="form-control">{{$lista->lista_nombre}}</label>
            </div>
        </div> 
        <hr>
        <div class="form-group row">
            <div class="col-sm-12" style="margin-bottom: 0px;">
                <table class="table table-striped table-hover" style="margin-bottom: 6px;">
                    <thead>
                        <tr class="letra-blanca fondo-azul-claro text-center">                                                
                            <th>Productos</th>
                            <th>Precios</th>                                          
                        </tr>
                    </thead>
                    <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)
                            <tr>
                                <td>{{ $datos[$i]['producto_nombre'] }}</td>
                                <td>
                                    @for ($filPrecio = 1; $filPrecio <= count($datos[$i]['precios']); ++$filPrecio)
                                    <label class="lista-precio">{{ $datos[$i]['precios'][$filPrecio]['plazo'].' dias' }} <br> {{'$ '.$datos[$i]['precios'][$filPrecio]['precio'] }} </label>
                                    @endfor
                                </td>
                            </tr>
                        @endfor
                    @endif
                    </tbody>
                </table>
            </div>
        </div>           
    </div>
</div>
@endsection