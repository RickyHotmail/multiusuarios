@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Informe Individual de Clientes</h3>
        
    </div>
    <!-- /.card-header -->
    <div class="card-body">
    <form class="form-horizontal" method="get" action="{{ url("informeindividualplano") }}">
        @csrf
        <div class="col-md-12">
            <div class="form-group row">
                <div class="col-sm-12">
                    <div class="row mb-2">
                        <label for="fecha_desde" class="col-sm-1 col-form-label text-right">Desde :</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fDesde)){echo $fDesde;}else{ echo(date("Y")."-".date("m")."-01");} ?>' required>
                        </div>
                        <label for="fecha_hasta" class="col-sm-1 col-form-label text-right">Hasta :</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fHasta)){echo $fHasta;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                        </div>

                        <!--div class="custom-control custom-checkbox col-sm2 pt-2">
                            <input class="custom-control-input" type="checkbox" id="incluirFechas" name="incluirFechas" value="1" <?php if(isset($fechasI)) echo "checked"; ?>>
                            <label for="incluirFechas" class="custom-control-label">Todo</label>
                        </div-->
                    </div>
                </div>
            </div>

            <div class="row">
                <label for="fecha_desde" class="col-sm-1 col-form-label text-right">Sucursal :</label>
                <div class="col-sm-3">
                    <select class="form-control" name="sucursal">
                        @foreach($sucursales as $sucursal)
                            <option value={{$sucursal->sucursal_id}} @if($sucursal_id==$sucursal->sucursal_id) selected @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-right">
                <input class="btn btn-success" type="submit" value="Buscar">
            </div>
        </div>
    </div>

    <form id="my_form" action="{{ url('generarindividualplano') }}" method="post"></form>


    <table class="table table-bordered table-hover table-responsive sin-salto">
        <thead>
            <tr class="text-center neo-fondo-tabla">
                <th>Identificación</th>
                <th>Cliente</th>
                <th>Fecha de Nacimiento</th>
                <th>Sexo</th>
                <th>Acción</th>
            </tr>
        </thead> 
        <tbody>
            @if(isset($ordenes))
            <?php $i=0 ?>
                @foreach($ordenes as $orden)
                    <?php
                        $agregar=true;

                        for($j=0; $j<=$i; $j++){
                            if($ordenes[$j]->paciente->paciente_cedula==$orden->paciente->paciente_cedula && $j!=$i){
                                $agregar=false;
                                break;
                            }
                        }

                        $i++;
                    ?>

                    @if($agregar)
                        <tr>
                            <td>{{ $orden->paciente->paciente_cedula }}</td>
                            <td>{{ $orden->paciente->paciente_apellidos }} {{ $orden->paciente->paciente_nombres }}</td>
                            <td>{{ $orden->paciente->paciente_fecha_nacimiento }}</td>
                            <td>{{ $orden->paciente->paciente_sexo }}</td>
                            <td>
                                <input form="" type="hidden" class="form-control" id="fecha_desde" name="fechaA"  value='<?php if(isset($fDesde)){echo $fDesde;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                                <input type="hidden" class="form-control" id="fecha_hasta" name="fechaB"  value='<?php if(isset($fHasta)){echo $fHasta;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                                <input type="hidden" name="paciente_id" value="{{ $orden->paciente->paciente_id }}">

                                <a target="_blank" href="{{ url('/generarindividualplano') }}?fechaA={{ $fDesde }}&fechaB={{ $fHasta }}&paciente_id={{ $orden->paciente_id }}&sucursal_id={{$sucursal_id}}" class="btn btn-primary">Descargar</a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<!-- /.modal -->
@endsection