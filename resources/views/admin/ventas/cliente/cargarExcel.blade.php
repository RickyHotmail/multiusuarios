@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("excelCliente") }}" enctype="multipart/form-data">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Cargar Excel con Clientes</h3>
            <div class="float-right">
                @if(isset($datos))
                <button type="submit" id="guardar" name="guardar" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                @endif
                <!-- <button type="button" onclick='window.location = "{{ url("cliente") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->      
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>    
            </div>
        </div>
        <div class="card-body">
            @if(!isset($datos))
            <div class="row">
                <label for="excelProd" class="col-sm-1 col-form-label">Cargar Excel</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" id="excelClient" name="excelClient" accept=".xls,.xlsx" required>
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
                    <th>Ced</th>
                    <th>Nombre</th>                  
                    <th>Direccion</th>
                    <th>Telefono</th>
                    <th>Celular</th>
                    <th>Email</th>
                    <th>Fecha</th>
                    <th>Lleva Contabilidad</th>
                    <th>Tiene Credito</th>
                    <th>Ciudad</th>
                    <th>Tipo de  Identificacion</th>
                    <th>Tipo de  Cliente</th>
                    <th>Categoria de Cliente</th>
                    <th>Credito</th>
                </tr>
                </thead> 
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)   
                        <tr>
                            <td>{{ $datos[$i]['ced'] }} <input type="hidden" name="idCed[]" value="{{ $datos[$i]['ced'] }}"/></td>
                            <td>{{ $datos[$i]['nom'] }} <input type="hidden" name="idNom[]" value="{{ $datos[$i]['nom'] }}"/></td>                            
                             <td>{{ $datos[$i]['direccion'] }} <input type="hidden" name="idDireccion[]" value="{{ $datos[$i]['direccion'] }}"/></td>
                            <td>{{ $datos[$i]['telefono'] }} <input type="hidden" name="idTelefono[]" value="{{ $datos[$i]['telefono'] }}"/></td>
                            <td>{{ $datos[$i]['celular'] }} <input type="hidden" name="idCelular[]" value="{{ $datos[$i]['celular'] }}"/></td>
                            <td>{{ $datos[$i]['email'] }} <input type="hidden" name="idEmail[]" value="{{ $datos[$i]['email'] }}"/></td>
                            <td>{{ $datos[$i]['fecha'] }} <input type="hidden" name="idFecha[]" value="{{ $datos[$i]['fecha'] }}"/></td>
                            <td>{{ $datos[$i]['llevaContabilidad'] }} <input type="hidden" name="idLlevaContabilidad[]" value="{{ $datos[$i]['llevaContabilidad'] }}"/></td>
                            <td>{{ $datos[$i]['tieneCredito'] }} <input type="hidden" name="idTieneCredito[]" value="{{ $datos[$i]['tieneCredito'] }}"/></td>
                            <td>{{ $datos[$i]['ciudad'] }} <input type="hidden" name="idCiudad[]" value="{{ $datos[$i]['ciudad'] }}"/></td>
                            <td>{{ $datos[$i]['tipoIdentificacion'] }} <input type="hidden" name="idTipoIdentificacion[]" value="{{ $datos[$i]['tipoIdentificacion'] }}"/></td>
                            <td>{{ $datos[$i]['tipoCliente'] }} <input type="hidden" name="idTipoCliente[]" value="{{ $datos[$i]['tipoCliente'] }}"/></td>
                            <td>{{ $datos[$i]['categoriaCliente'] }} <input type="hidden" name="idCategoriaCliente[]" value="{{ $datos[$i]['categoriaCliente'] }}"/></td>
                            <td>{{ $datos[$i]['credito'] }} <input type="hidden" name="idCredito[]" value="{{ $datos[$i]['credito'] }}"/></td>
                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </div>         
    </div>
</form>
@endsection