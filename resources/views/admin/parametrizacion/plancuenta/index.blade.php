@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("excelCuenta") }}" enctype="multipart/form-data">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Cargar Excel Plan de Cuentas</h3>
            <div class="float-right">
                @if(isset($datos))
                <button type="submit" id="guardar" name="guardar" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                @endif
                <button type="button" onclick='window.location = "{{ url("cuenta") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            @if(!isset($datos))
            <div class="row">
                <label for="excelProd" class="col-sm-1 col-form-label">Cargar Excel</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" id="excelProv" name="excelProv" accept=".xls,.xlsx" required>
                </div>
                <div class="col-sm-2">
                    <button type="submit" id="cargar" name="cargar" class="btn btn-primary btn-sm"><i class="fas fa-spinner"></i>&nbsp;Cargar</button>
                </div>
            </div>
            </br>
            @endif
            <table class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                <tr class="text-center neo-fondo-tabla">                   
                    <th>Numero</th>
                    <th>Nombre</th>
                    <th>Secuencial</th>
                    <th>Nivel</th>
                    <th>Ceunta Padre</th>     
                </tr>
                </thead> 
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)   
                        <tr>
                            <td>{{ $datos[$i]['numero'] }} <input type="hidden" name="idNumero[]" value="{{ $datos[$i]['numero'] }}"/></td>
                            <td>{{ $datos[$i]['nombre'] }} <input type="hidden" name="idNombre[]" value="{{ $datos[$i]['nombre'] }}"/></td>                            
                            <td>{{ $datos[$i]['secuencial'] }} <input type="hidden" name="idSecuencial[]" value="{{ $datos[$i]['secuencial'] }}"/></td>
                            <td>{{ $datos[$i]['Nivel'] }} <input type="hidden" name="idNivel[]" value="{{ $datos[$i]['Nivel'] }}"/></td>
                            <td>{{ $datos[$i]['Padre'] }} <input type="hidden" name="idPadre[]" value="{{ $datos[$i]['Padre'] }}"/></td>
                          
                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </div>         
    </div>
</form>
@endsection