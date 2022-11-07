@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Empresas</h3>
    </div>
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th>RUC</th>
                    <th>Descripción</th>
                    <th>correo</th>
                    <th>Teléfono Convencionale</th>
                    <th>Teléfono Celular</th>
                    <th>Ciudad</th>  
                    <th>Plan</th>
                    <th>Registro</th>
                    <th>Caduca</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody> 
                @foreach($empresas as $empresa)
                    <tr>
                        <td class="text-center">{{ $empresa->empresa_ruc }}</td>
                        <td class="text-center">{{ $empresa->empresa_nombreComercial }}</td>
                        <td class="text-center">{{ $empresa->empresa_email }}</td>
                        <td class="text-center">{{ $empresa->empresa_telefono }}</td>
                        <td class="text-center">{{ $empresa->empresa_celular }}</td>
                        <td class="text-center">{{ $empresa->empresa_ciudad }}</td>
                        
                        @if($empresa->suscripcion)
                            <td class="text-rigth">{{ $empresa->suscripcion->plan->plan_nombre }}</td>
                            <td class="text-rigth">{{ Date('d-m-Y', strtotime($empresa->created_at)) }}</td>
                            <td class="text-rigth">{{ Date('d-m-Y', strtotime($empresa->suscripcion->suscripcion_fecha_finalizacion)) }}</td>
                        @else
                            <td class="text-rigth">-</td>
                            <td class="text-rigth">{{ Date('d-m-Y', strtotime($empresa->created_at)) }}</td>
                            <td class="text-rigth">-</td>
                        @endif
                        <td class="text-center form-row">
                            <form method="POST" action="{{ url("administracion/{$empresa->empresa_id}/desactivar") }}" onsubmit="return validarEliminar('{{$empresa->empresa_nombreComercial}}')">
                                @csrf
                                <button class="btn btn-xs btn-danger mr-1" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </form>
                            @if($empresa->empresa_estado==1)
                                <form method="POST" action="{{ url("administracion/{$empresa->empresa_id}/desactivar") }}" onsubmit="return validarDesactivar('{{$empresa->empresa_nombreComercial}}')">
                                    @csrf
                                    <button class="btn btn-xs btn-success mr-1" data-toggle="tooltip" data-placement="top" title="Desactivar"><i class="fa fa-lock" aria-hidden="true"></i></button>
                                </form>
                            @else
                                <form method="POST" action="{{ url("administracion/empresa/{$empresa->empresa_id}/activar") }}">
                                    @csrf
                                    <button class="btn btn-xs btn-dark mr-1" data-toggle="tooltip" data-placement="top" title="Activar"><i class="fa fa-lock" aria-hidden="true"></i></button>
                                </form>
                            @endif

                            <a href="{{ url("administracion/empresa/{$empresa->empresa_id}/verusuarios") }}" class="btn btn-xs btn-primary mr-1" data-toggle="tooltip" data-placement="top" title="Resetear Password"><i class="fa fa-user" aria-hidden="true"></i></a>
                            <a href="{{ url("administracion/pagos/{$empresa->empresa_id}") }}" class="btn btn-xs btn-neo-morado mr-1" data-toggle="tooltip" data-placement="top" title="Pagos"><i class="fa fa-cog" aria-hidden="true"></i></a>
                            <a href="{{ url("administracion/empresa/{$empresa->empresa_id}") }}/verpermisos" class="btn btn-xs btn-secondary mr-1" data-toggle="tooltip" data-placement="top" title="Cambiar Plan"><i class="fa fa-cog" aria-hidden="true"></i></a>
                            <a href="" class="btn btn-xs btn-info mr-1" data-toggle="tooltip" data-placement="top" ><i class="fa fa-cog" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    function validarDesactivar(empresa){
        return confirm("Vas a Deshabilitar a la Empresa "+empresa+" \n ¿Estas Seguro?")
    }

    function validarEliminar(empresa){
        return confirm("Se eliminara la Empresa "+empresa+", y toda la información relacionada, esta acción no se puede deshacer \n ¿Continuar con la Eliminación permanente?")
    }
</script>

@endsection
