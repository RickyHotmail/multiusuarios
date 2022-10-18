@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Roles</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal"  method="POST" action="{{ url("rolreporteDetallado") }} ">
        @csrf 
            <div class="float-right">
                            <button type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                            <button type="submit" id="excel" name="excel" class="btn btn-success"><i class="fas fa-file-excel"></i></button>               
            </div>   
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                
                <label for="nombre_empleado" class="col-sm-1 col-form-label"><center>empleado:</center></label>
                <div class="col-sm-5">
                    <select class="custom-select select2" id="nombre_empleado" name="nombre_empleado" >
                        <option value="0" label>--TODOS--</option>                       
                        @foreach($empleado as $empleado)
                            <option  value="{{$empleado->empleado_id}}">{{$empleado->empleado_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div> 
                <div class="col-sm-1">
                <button type="submit" id="buscar" name="buscar" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>   
            </div>
            <div class="form-group row">
            <label for="sucursal" class="col-sm-1 col-form-label"><center>sucursales:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="sucursal" name="sucursal" >
                        <option value="0" >--TODOS--</option> 
                        @foreach($sucursales as $sucursal)
                            <option  value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div> 
            </div>
            <div class="card-body table-responsive p-0" style="height: 600px;">
            <div class="d-flex flex-row justify-content-end">
                <span class="mr-2">
                <i class="fas fa-square text-pagado"></i> Pagado
                </span>

                <span>
                <i class="fas fa-square text-acumulado"></i> Acumulado
                </span>
            </div>
                <table id="example33" class="table table-bordered table-hover table-responsive sin-salto">
                    <thead>
                        <tr class="text-center">
                        <th colspan="2"></th>
                        @if($ingre>0)
                       
                            <th colspan="{{$ingre+1}}">Ingresos</th>  
                        @endif
                        @if($egre>0)
                            <th colspan="{{$egre+1}}">Egresos</th> 
                        @endif
                        <th ></th> 
                        @if($bene>0)
                            <th colspan="{{$bene}}">Beneficios</th> 
                        @endif
                        @if($otros>0)
                            <th colspan="{{$otros}}">Otros</th> 
                        @endif
                        <th ></th> 
                        </tr>
                        <tr class="text-center">
                        <input type="hidden" name="contador[]" value="Cedula">
                            <th>Cedula</th>
                            <input type="hidden" name="contador[]" value="Nombre">
                            <th>Nombre</th> 
                            @foreach($rubros as $rubro)
                                @if($rubro->rubro_tipo =='2')
                                <th>{{ $rubro->rubro_descripcion}}</th><input type="hidden" name="contador[]" value="{{ $rubro->rubro_descripcion}}">  
                                @endif 
                            @endforeach
                            <th>Total Ingresos</th> <input type="hidden" name="contador[]" value="Total Ingresos">
                            @foreach($rubros as $rubro)
                                @if($rubro->rubro_tipo =='1')
                                <th>{{ $rubro->rubro_descripcion}}</th> <input type="hidden" name="contador[]" value="{{ $rubro->rubro_descripcion}}">   
                                @endif 
                            @endforeach
                            <th>Total Egresos</th> <input type="hidden" name="contador[]" value="Total Egresos">
                            <th>Total</th><input type="hidden" name="contador[]" value="Total Egresos">
                            @foreach($rubros as $rubro) 
                                @if($rubro->rubro_tipo =='3')
                                <th>{{ $rubro->rubro_descripcion}}</th><input type="hidden" name="contador[]" value="{{ $rubro->rubro_descripcion}}"> 
                                @endif 
                            @endforeach
                            @foreach($rubros as $rubro) 
                                @if($rubro->rubro_tipo =='4')
                                    <th>{{ $rubro->rubro_descripcion}}</th>  <input type="hidden" name="contador[]" value="{{ $rubro->rubro_descripcion}}">
                                @endif  
                            @endforeach
                            <th>Total A Pagar</th> <input type="hidden" name="contador[]" value="Total A Pagar">
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($datos))
                            @for ($i = 1; $i <= count($datos); ++$i)  
                            <tr>  
                                <td class="text-center">{{ $datos[$i]['cedula'] }}</td> <input type="hidden" name="detalle[]" value="{{ $datos[$i]['cedula'] }}">
                                <td class="text-center">{{ $datos[$i]['nombre'] }}</td><input type="hidden" name="detalle[]" value="{{ $datos[$i]['nombre'] }}">
                                @foreach($rubros as $rubro)
                                    @if($rubro->rubro_tipo =='2')
                                        <td class="text-center">{{number_format($datos[$i][$rubro->rubro_nombre],2)}}</td><input type="hidden" name="detalle[]" value="{{number_format($datos[$i][$rubro->rubro_nombre],2)}}">  
                                    @endif 
                                @endforeach
                                <td class="text-center">{{number_format($datos[$i]['totalingresos'],2) }}</td><input type="hidden" name="detalle[]" value="{{number_format($datos[$i]['totalingresos'],2) }}">
                                @foreach($rubros as $rubro)
                                    @if($rubro->rubro_tipo =='1')
                                        <td class="text-center">{{number_format($datos[$i][$rubro->rubro_nombre],2)}}</td> <input type="hidden" name="detalle[]" value="{{number_format($datos[$i][$rubro->rubro_nombre],2)}}"> 
                                    @endif 
                                @endforeach
                                <td class="text-center">{{number_format($datos[$i]['totalegresos'] ,2)}}</td><input type="hidden" name="detalle[]" value="{{number_format($datos[$i]['totalegresos'] ,2)}}">
                                <td class="text-center">{{number_format($datos[$i]['totalingresos']-$datos[$i]['totalegresos'],2) }}</td><input type="hidden" name="detalle[]" value="{{number_format($datos[$i]['totalingresos']-$datos[$i]['totalegresos'],2) }}">
                                @foreach($rubros as $rubro) 
                                    @if($rubro->rubro_tipo =='3')
                                        <?php $vari='E'.$rubro->rubro_nombre; ?>
                                        @if(isset($datos[$i][$vari]))
                                            <input type="hidden" value="{{$datos[$i][$vari]}}" >
                                        @endif
                                        <td  @if(isset($datos[$i][$vari]))  @if($datos[$i][$vari]=='Pagado')  style="background:  #70B1F7;" @endif  @if($datos[$i][$vari]=='Acumulado')  style="background:  #B1E2DD;" @endif @endif>  {{number_format($datos[$i][$rubro->rubro_nombre],2)}}</td>
                                    @endif  
                                @endforeach
                                @foreach($rubros as $rubro) 
                                    @if($rubro->rubro_tipo =='4')
                                        <td class="text-center" >{{number_format($datos[$i][$rubro->rubro_nombre],2)}}</td><input type="hidden" name="detalle[]" value="{{number_format($datos[$i][$rubro->rubro_nombre],2)}}">
                                    @endif  
                                @endforeach
                                <td class="text-center">{{number_format($datos[$i]['total'],2)}}</td><input type="hidden" name="detalle[]" value="{{number_format($datos[$i]['total'],2)}}">
                            </tr>
                            @endfor
                        @endif     
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
   <?php
  
    if(isset($fechahasta)){  
        ?>
         document.getElementById("fecha_hasta").value='<?php echo($fechahasta); ?>';
         <?php
    }
    if(isset($fechadesde)){  
        ?>
         document.getElementById("fecha_desde").value='<?php echo($fechadesde); ?>';
         <?php
    }
    if(isset($nombre_empleado)){  
    ?>
         document.getElementById("nombre_empleado").value='<?php echo($nombre_empleado); ?>';
         <?php
    }
    if (isset($sucursalid)) {
        ?>
    document.getElementById("sucursal").value='<?php echo($sucursalid); ?>';
         <?php
        }
          ?>
</script>
@endsection