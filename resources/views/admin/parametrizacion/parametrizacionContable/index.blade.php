@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Parametrizacion Contable</h3>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("parametrizacionContableBuscar") }}">
            @csrf
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="sucursal_id" name="sucursal_id" onchange="limpiarTabla();" require>
                        @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($sucursalC)) @if($sucursalC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select> 
                </div>
                <div class="col-sm-1 centrar-texto">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
        <table id="example4" class="table table-bordered table-responsive table-hover sin-salto">
            <thead>
                <tr class="neo-fondo-tabla">
                    <th class="text-center"></th>
                    <th>Nombre</th>
                    <th >Cuenta Contable</th>
                    <th class="text-center">Usar una Cuenta General</th>                                       
                </tr>
            </thead> 
            <tbody id="idDetalle">
                @if(isset($parametrizacionContable))
                    @foreach($parametrizacionContable as $parametrizacionContable)
                    <tr>
                        <td class="text-center">
                            <a href="{{ url("parametrizacionContable/{$parametrizacionContable->parametrizacion_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            <a href="{{ url("parametrizacionContable/{$parametrizacionContable->parametrizacion_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        </td>
                        <td>{{ $parametrizacionContable->parametrizacion_nombre}}</td>
                        <td>@if($parametrizacionContable->cuenta) {{ $parametrizacionContable->cuenta->cuenta_numero.'  -  '.$parametrizacionContable->cuenta->cuenta_nombre}} @else SIN CUENTA @endif</td> 
                        <td class="text-center">
                            @if($parametrizacionContable->parametrizacion_cuenta_general=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>                                         
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    function limpiarTabla(){
        document.getElementById("idDetalle").innerHTML = 'No hay datos disponibles en la tabla';
    }
</script>
@endsection
