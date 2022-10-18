@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="@if(is_null($datos)) {{ url("excelPrescripcion") }} @else {{ url("excelPrescripcionGuardar") }} @endif"  enctype="multipart/form-data">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Cargar Excel con Productos</h3>
            <div class="float-right">

                @if(isset($datos))
                <button type="submit" id="guardar" name="guardar" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                @else
                    <a class="btn btn-info btn-sm" href="{{ asset('admin/archivos/FORMATO_PRODUCTOS_PRESCRIPCION.xlsx') }}" download="FORMATO SUBIR PRESCRIPCIONES"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
                @endif
                <button type="button" onclick='window.location = "{{ url("producto") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            @if(!isset($datos))
            <div class="row">
                <label for="excelProd" class="col-sm-1 col-form-label">Cargar Excel</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" id="excelProd" name="excelProd" accept=".xls,.xlsx" required>
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
                        <th>Fecha de Despacho</th>
                        <th>Orden Receta</th>
                        <th>Identificación</th>
                        <th>Cliente</th>
                        <th>Medicamento</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>% Utilidad</th>
                        <th>Utilidad</th>
                        <th>Total</th>
                    </tr>
                </thead> 
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i < count($datos[0]); ++$i)
                            <?php
                                $Excel_date = $datos[0][$i][0]; 
                                $unix_date = ($Excel_date - 25569) * 86400;
                                $Excel_date = 25569 + ($unix_date / 86400);
                                $unix_date = ($Excel_date - 25569) * 86400;
                                $fecha_despacho = gmdate("Y-m-d", $unix_date);
                            ?>
                            <tr>
                                <td>{{ $fecha_despacho }} <input type="hidden" name="fec[]" value="{{ $fecha_despacho }}"/></td>
                                <td>
                                    <select name="ordSelect[]">
                                        <option value=0>Ninguna</option>
                                        @if($datos[0][$i][20]!=null)
                                            @foreach($datos[0][$i][20] as $orden)
                                                <option value={{ $orden[0] }}>{{ $orden[1] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="text" name="ord[] class="form-control">
                                </td>
                                <td>{{ $datos[0][$i][1] }} <input type="hidden" name="ide[]" value="{{ $datos[0][$i][1] }}"/></td>
                                <td>{{ $datos[0][$i][2] }} <input type="hidden" name="cli[]" value="{{ $datos[0][$i][2] }}"/></td>
                                <td>{{ $datos[0][$i][3] }} <input type="hidden" name="med[]" value="{{ $datos[0][$i][3] }}"/></td>
                                <td>{{ $datos[0][$i][4] }} <input type="hidden" name="can[]" value="{{ $datos[0][$i][4] }}"/></td>
                                <td>{{ $datos[0][$i][5] }} <input type="hidden" name="pre[]" value="{{ $datos[0][$i][5] }}" id="pre{{$i}}"/></td>

                                <?php
                                    $subtotal= floatval($datos[0][$i][4]*$datos[0][$i][5]);
                                    $tot_desc= $subtotal*0.10;
                                    $total=$subtotal+$tot_desc;
                                ?>

                                <td><input class="text-right" onkeyup="calcularUtilidad({{$i}})" onchange="calcularUtilidad({{$i}})" type="number" min=0 max=50 step="1" name="p_u[]" value="10" id="p_u{{ $i }}"></td>
                                <td name="uti[]" id="uti{{ $i }}"><?= number_format($tot_desc, 2, ".", "") ?></td>

                                <td name="tot[]" id="tot{{ $i}}"><?= number_format($total, 2, ".", "") ?></td>
                            </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </div>         
    </div>
</form>
<script>
    function calcularUtilidad(i){
        porcentaje= parseInt(document.getElementById("p_u"+i).value)
        pre=parseFloat(document.getElementById("pre"+i).value)


        uti=document.getElementById("uti"+i)
        tot=document.getElementById("tot"+i)



        tot_des=pre*porcentaje/100,2


        uti.innerHTML=(tot_des).toFixed(2)
        tot.innerHTML=(pre + tot_des).toFixed(2)
        console.log("tot_des "+tot_des)
    }
</script>
@endsection