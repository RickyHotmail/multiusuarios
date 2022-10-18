@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Informe de Carga Masiva</h3>
        
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("generarreportecargamasiva") }}">
            @csrf
            <div class="col-md-12">
                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="row mb-2">
                            <label for="fecha_desde" class="col-sm-1 col-form-label text-right">Desde :</label>
                            <div class="col-sm-3">
                                <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fDesde)){echo $fDesde;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                            </div>
                            <label for="fecha_hasta" class="col-sm-1 col-form-label text-right">Hasta :</label>
                            <div class="col-sm-3">
                                <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fHasta)){echo $fHasta;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                            </div>

                            <div class="custom-control custom-checkbox col-sm2 pt-2">
                                <input class="custom-control-input" type="checkbox" id="incluirFechas" name="incluirFechas" value="1" <?php if(isset($fechasI)) echo "checked"; ?>>
                                <label for="incluirFechas" class="custom-control-label">Todo</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <label for="fecha_desde" class="col-sm-1 col-form-label text-right">Sucursal :</label>
                    <div class="col-sm-3">
                        <select class="form-control" name="sucursal">
                            @foreach($sucursales as $sucursal)
                                <option value={{$sucursal->sucursal_id}}>{{$sucursal->sucursal_nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="text-right">
                    <input class="btn btn-success" type="submit" value="Generar">
                </div>
            </div>
            <div class="col-md-12">
                <h4 class="text-center">Información a mostrar</h4>

                <h6 class="text-center">Marcar los campos que se visualizarán en el reporte generado en Excel</h6>

                <div class="row offset-md-1 col-md-10" style="background: #ddd; border-radius: 5px; padding: 10px">
                    <div class="col-md-3">
                        <h6 style="font-weight: bold">Datos de Cliente</h6>

                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk1" <?= $config->valor[0]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk1">
                                Cédula del Cliente
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk2" <?= $config->valor[1]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk2">
                                Nombres del Cliente
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk3" <?= $config->valor[2]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk3">
                                Teléfono del Cliente
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk4" <?= $config->valor[3]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk4">
                                Dirección del Cliente
                            </label>
                        </div>
                        <br>

                        <h6 style="font-weight: bold">Datos de Paciente</h6>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk5" <?= $config->valor[4]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk5">
                                Cédula del Paciente
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk6" <?= $config->valor[5]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk6">
                                Nombres del Paciente
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk7" <?= $config->valor[6]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk7">
                                Teléfono del Paciente
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk8" <?= $config->valor[7]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk8">
                                Dirección del Paciente
                            </label>
                        </div>
                        <br>

                        <h6 style="font-weight: bold">Medico</h6>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk9" <?= $config->valor[8]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk9">
                                Cédula Médico
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk10" <?= $config->valor[9]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk10">
                                Nombres Médico
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk11" <?= $config->valor[10]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk11">
                                Código de Homologación
                            </label>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <h6 style="font-weight: bold">Datos de Orden de Atención</h6>

                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk12" <?= $config->valor[11]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk12">
                                Número de Orden
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk13" <?= $config->valor[12]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk13">
                               Fecha de Creación
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk14" <?= $config->valor[13]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk14">
                                Obervación
                            </label>
                        </div>
                        <br>

                        <h6 style="font-weight: bold">Detalle de Valores</h6>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk15" <?= $config->valor[14]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk15">
                                LLeva Iva
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk16" <?= $config->valor[15]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk16">
                                Valor Iva
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk17" <?= $config->valor[16]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk17">
                                Valor Procedimiento
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk18" <?= $config->valor[17]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk18">
                                % Cobertura
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk19" <?= $config->valor[18]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk19">
                                Copago
                            </label>
                        </div>
                        <br>

                        <h6 style="font-weight: bold">Diagnósitico</h6>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk20" <?= $config->valor[19]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk20">
                                Código
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk21" <?= $config->valor[20]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk21">
                                Detalle
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk22" <?= $config->valor[21]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk22">
                                Observación
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <h6 style="font-weight: bold">Consultas/Procedimiento/Examen/Medicina</h6>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk23" <?= $config->valor[22]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk23">
                                Código de Producto
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk24" <?= $config->valor[23]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk24">
                                Nombre
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk25" <?= $config->valor[24]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk25">
                                Detalle del Producto
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk26" <?= $config->valor[25]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk26">
                                Fecha de Inicio de Autorización SRI
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk27" <?= $config->valor[26]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk27">
                                Fecha de Fin de Autorización SRI
                            </label>
                        </div>
                        <br>

                        <h6 style="font-weight: bold">Factura</h6>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk28" <?= $config->valor[27]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk28">
                                Tipo de documento
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk29" <?= $config->valor[28]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk29">
                                Numero de Factura
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk30" <?= $config->valor[29]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk30">
                                Fecha de facturación
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk31" <?= $config->valor[30]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk31">
                                Fecha de Inicio de Autorización SRI
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk32" <?= $config->valor[31]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk32">
                                Fecha de Fin de Autorización SRI
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                    <h6 style="font-weight: bold">Otros</h6>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk33" <?= $config->valor[32]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk33">
                                Región
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk34" <?= $config->valor[33]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk34">
                                Contrato
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk35" <?= $config->valor[34]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk35">
                                Nivel
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk36" <?= $config->valor[35]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk36">
                                Plan
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk37" <?= $config->valor[36]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk37">
                                RUC Prestador
                            </label>
                        </div>

                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk38" <?= $config->valor[37]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk38">
                                Nombre Comercial
                            </label>
                        </div>
                        <div class="col-md-12">
                            <input class="form-check-input" type="checkbox" name="chk39" <?= $config->valor[38]? 'checked value=1': ''?>>
                            <label class="form-check-label" for="chk39">
                                Razón Social Prestador
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.modal -->
@endsection