@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("excelProveedor") }}" enctype="multipart/form-data">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Cargar Excel con Proveedores</h3>
            <div class="float-right">
                @if(isset($datos))
                <button type="submit" id="guardar" name="guardar" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                @endif
                 <!--     
                <button type="button" onclick='window.location = "{{ url("proveedor") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
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
                    <th>Ruc</th>
                    <th>Nombre</th>
                    <th>Nombre Comercial</th>
                    <th>Gerente</th>
                    <th>Direccion</th>
                    <th>Telefono</th>
                    <th>Celular</th>
                    <th>Email</th>
                    <th>Actividad</th>
                    <th>Fecha de Ingreso</th>
                    @if(Auth::user()->empresa->empresa_contabilidad == '1')
                    <th>Tipo</th>
                    <th>Lleva Contabilidad</th>
                    <th>Contribuyente</th>
                    <th>Cuenta por Pagar</th>
                    <th>Cuenta Anticipo</th>
                    @endif
                    <th>Tipo de Sujeto</th>
                    <th>Tipo de  Identificacion</th>              
                    <th>Ciudad</th> 
                    <th>Categoria de Proveedor</th>              
                </tr>
                </thead> 
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)   
                        <tr>
                            <td>{{ $datos[$i]['ruc'] }} <input type="hidden" name="idRuc[]" value="{{ $datos[$i]['ruc'] }}"/></td>
                            <td>{{ $datos[$i]['nom'] }} <input type="hidden" name="idNom[]" value="{{ $datos[$i]['nom'] }}"/></td>                            
                            <td>{{ $datos[$i]['nombComercial'] }} <input type="hidden" name="idNombComercial[]" value="{{ $datos[$i]['nombComercial'] }}"/></td>
                            <td>{{ $datos[$i]['gerente'] }} <input type="hidden" name="idGerente[]" value="{{ $datos[$i]['gerente'] }}"/></td>
                            <td>{{ $datos[$i]['direccion'] }} <input type="hidden" name="idDireccion[]" value="{{ $datos[$i]['direccion'] }}"/></td>
                            <td>{{ $datos[$i]['telefono'] }} <input type="hidden" name="idTelefono[]" value="{{ $datos[$i]['telefono'] }}"/></td>
                            <td>{{ $datos[$i]['celular'] }} <input type="hidden" name="idCelular[]" value="{{ $datos[$i]['celular'] }}"/></td>
                            <td>{{ $datos[$i]['email'] }} <input type="hidden" name="idEmail[]" value="{{ $datos[$i]['email'] }}"/></td>
                            <td>{{ $datos[$i]['actividad'] }} <input type="hidden" name="iActividad[]" value="{{ $datos[$i]['actividad'] }}"/></td>
                            <td>{{ $datos[$i]['fecha'] }} <input type="hidden" name="idFecha[]" value="{{ $datos[$i]['fecha'] }}"/></td>
                            <td>{{ $datos[$i]['tipo'] }} <input type="hidden" name="idTipo[]" value="{{ $datos[$i]['tipo'] }}"/></td>
                            <td>{{ $datos[$i]['llevaContabilidad'] }} <input type="hidden" name="idLlevaContabilidad[]" value="{{ $datos[$i]['llevaContabilidad'] }}"/></td>
                            <td>{{ $datos[$i]['contribuyente'] }} <input type="hidden" name="idContribuyente[]" value="{{ $datos[$i]['contribuyente'] }}"/></td>
                            <td>{{ $datos[$i]['cuentaPagar'] }} <input type="hidden" name="idCuentaPagar[]" value="{{ $datos[$i]['cuentaPagar'] }}"/></td>
                            <td>{{ $datos[$i]['cuentaAntitipo'] }} <input type="hidden" name="idCuentaAntitipo[]" value="{{ $datos[$i]['cuentaAntitipo'] }}"/></td>
                            <td>{{ $datos[$i]['tipoSujeto'] }} <input type="hidden" name="idTipoSujeto[]" value="{{ $datos[$i]['tipoSujeto'] }}"/></td>
                            <td>{{ $datos[$i]['tipoIdentificacion'] }} <input type="hidden" name="idTipoIdentificacion[]" value="{{ $datos[$i]['tipoIdentificacion'] }}"/></td>
                            <td>{{ $datos[$i]['ciudad'] }} <input type="hidden" name="idCiudad[]" value="{{ $datos[$i]['ciudad'] }}"/></td>
                            <td>{{ $datos[$i]['categoriaProveedor'] }} <input type="hidden" name="idCategoriaProveedor[]" value="{{ $datos[$i]['categoriaProveedor'] }}"/></td>
                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </div>         
    </div>
</form>
@endsection