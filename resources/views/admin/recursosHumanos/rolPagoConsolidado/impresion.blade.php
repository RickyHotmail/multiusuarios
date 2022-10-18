@extends ('admin.layouts.admin')
@section('principal')

<div class="card">
    <div class="card-header">
    <h3 class="card-title">Rol Consolidada</h3>
    </div>
    
    <form class="form-horizontal" >      
        @csrf 
        <div class="card"> 
            <br>     
                
            <div class="card-body">
                <div class="table-responsive">
                   
                    <table id="example5" name="example5" class="table table-bordered table-hover table-responsive sin-salto">
                        <thead>
                            
                            <tr class="letra-blanca fondo-azul-claro">
                                <th></th>
                                <th>Empleado</th>
                                <th>Fecha</th>
                                <th>Dias Trabajados</th>
                                <th>Sueldo</th>
                                <th>Pago</th>   
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($data))
                                @for ($i = 1; $i <= count($data); ++$i)  
                                <tr>  
                                    <td class="text-center"> 
                                   <a href="{{ url("rolindividual/{$data[$i]["idrol"]}/imprimir") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir Rol"><i class="fa fa-print"></i></a>                   
                                   <a href="{{ url("rolindividual/{$data[$i]["idrol"]}/imprimirdiario") }}" target="_blank" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Imprimir Diario"><i class="fa fa-print"></i></a>                   
                                  
                                   @if($data[$i]["cheque"]!=0)
                                    <a href="{{ url("/cheque/imprimir/{$data[$i]["cheque"]}") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir Cheque"><i class="fas fa-money-check-alt"></i></a>                   
                                   @endif
                                   <!-- /.card-body <a href="{{ url("rolindividual/{$data[$i]["idrol"]}/imprimirdiariocontabilizado") }}" target="_blank" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Imprimir Diario"><i class="fa fa-print"></i></a>                   
                                     -->  
                                    </td>                               
                                    <td class="text-center">{{ $data[$i]["empleado"]}}</td>
                                    <td class="text-center">{{$data[$i]["fecha"]}}</td>
                                    <td class="text-center">{{ $data[$i]["dias"]}}</td>
                                    <td class="text-center">{{ $data[$i]["sueldo"]}}</td>
                                    <td class="text-center">{{ $data[$i]["pago"]}}</td>                             
                                </tr>
                                @endfor
                            @endif 
                        </tbody>
                    </table>
                </div>
            </div>
        </div> 
        
    

   
    </form>
    

</div>

@endsection



