@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Cierre</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listaCierreResultado") }} "> 
        @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
            </div>   
            <div class="form-group row">  
                <label for="sucursal" class="col-sm-1 col-form-label"><center>Sucursal</center></label>
                <div class="col-sm-4">
                    <select class="form-control select2" id="sucursal" name="sucursal" data-live-search="true">
                        <option value="0" label>--TODOS--</option>  
                        @foreach($sucursales as $sucursal)
                            <option  value="{{$sucursal->sucursal_id}}" @if(isset($idsucursal)) @if($sucursal->sucursal_nombre==$idsucursal) selected @endif @endif>                                
                                {{$sucursal->sucursal_nombre}}
                            </option>
                        @endforeach
                    </select>  
                </div>
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>            
        </form>
        <hr>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th>   
                    </th>
                    <th>AÑO</th>
                    <th>DIARIO</th>
                    <th>SUCURSAL</th>
                </tr>
            </thead> 
            <tbody> 
            @if(isset($diarios)) 
                @foreach($diarios as $x)
                    <tr class="text-center">
                        <td>
                            <a href="{{ url("asientoDiario/ver/{$x->diario_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Visualizar"><i class="fa fa-eye"></i></a> 
                            <a href="{{ url("asientoDiario/imprimir/{$x->diario_id}") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a>    
                            <a href="{{ url("asientoDiarioC/eiminar/{$x->diario_id}") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                        <td>{{DateTime::createFromFormat('Y-m-d', $x->diario_fecha)->format('Y') }}</td>  
                        <td>{{ $x->diario_codigo}}</td>     
                        <td>{{ $x->sucursal->sucursal_nombre}}</td>         
                    </tr>
            @endforeach
        @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<script>
     <?php
    if(isset($fecI)){  
        ?>
         document.getElementById("idDesde").value='<?php echo($fecI); ?>';
         <?php
    }
    if(isset($fecF)){  
        ?>
         document.getElementById("idHasta").value='<?php echo($fecF); ?>';
         <?php
    }
    ?>
</script>
@endsection
