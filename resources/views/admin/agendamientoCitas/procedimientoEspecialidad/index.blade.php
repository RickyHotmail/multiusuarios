@extends ('admin.layouts.admin')
@section('principal')
<form method="post" action="{{ url('procedimiento/actualizarGrupo') }}">
    @csrf
    <div class="card card-secondary">
        <div class="card-header" style="position:sticky; top:0; z-index:99999">
            <h3 class="card-title">Procedimiento Especialidad</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div>
                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="especialidad_id">Nombre de la Especialidad: </label>
                    </div>
                    <div class="form-group col-md-4">
                        <select id="especialidad_id" name="especialidad_id" class="form-control" onchange="document.location.href='{{ url('procedimientoEspecialidad') }}?especialidad_id='+this.value">
                            @foreach($especialidades as $esp)
                                <option value="{{ $esp->especialidad_id }}" @if(isset($seleccionada)) @if($seleccionada==$esp->especialidad_id) selected @endif @endif>{{ $esp->especialidad_nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="custom-control custom-checkbox">
                <input class="custom-control-input" type="checkbox"  id="checkMarcar">
                <label for="checkMarcar" class="custom-control-label">Marcar Todos</label>
            </div>

            <table class="table table-bordered table-hover table-responsive sin-salto" id="tbEspecialidades">
                <thead>
                    <tr class="text-center neo-fondo-tabla">  
                        <th></th>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Valor</th>
                        <th>Dscripcion</th>                                                              
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                    <tr class="text-center">
                        <td>
                            <?php $marcado=false; ?>
                            @foreach($procedimientoEspecialidades as $procedimiento)
                                @if($procedimiento->producto_id==$producto->producto_id)
                                    @php $marcado=true @endphp
                                    @break;
                                @endif
                            @endforeach
                            <div class="row">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input miCheck" type="checkbox" name="producto[{{ $producto->producto_id }}]" id="producto[{{ $producto->producto_id }}]" @if($marcado) checked @endif>
                                    <label for="producto[{{ $producto->producto_id }}]" class="custom-control-label"></label>
                                </div>
                                <a href="{{ url("procedimientoEspecialidad/{$producto->producto_id}/especialidad")}}" class="btn btn-xs btn-secondary"  data-toggle="tooltip" data-placement="top" title="Asignar Especialidades"><i class="fa fa-tasks" aria-hidden="true"></i></a>
                            </div>
                        </td>
                        <td>{{ $producto->producto_codigo }}</td>
                        <td>{{ $producto->producto_nombre }}</td>
                        <td><?php echo '$' . number_format($producto->producto_precio_costo, 2)?></td>
                        <td>@if($producto->producto_tipo == '1') ARTICULO @else SERVICIO  @endif</td>                                                      
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
</form>
<script>
    setTimeout(function(){
        $(document).ready(function () {  
            $('#checkMarcar').on('click', function () {
                var checked_status = this.checked;
    
                $(".miCheck").each(function () {
                    this.checked = checked_status;
                });
            });
        })
    }, 600)
</script>
@endsection