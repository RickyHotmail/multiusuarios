@extends ('admin.layouts.admin')
@section('principal')

<div class="card">
    <div class="card-header">
    <h3 class="card-title">Beneficios</h3>
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
                                <th>Pago</th>   
                                <th>Numero Cheque</th>   
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($datos))
                                @for ($i = 1; $i <= count($datos); ++$i)  
                                <tr>  
                                    <td class="text-center"> 
                                    <a href="{{ url("beneficioSocial/{$datos[$i]["id"]}/imprimir") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir Diaro"><i class="fa fa-print"></i></a>                   
                                    @if($datos[$i]["tipo"]=='Cheque')
                                    <a href="{{ url("/cheque/imprimir/{$datos[$i]["cheque"]}") }}" target="_blank" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Imprimir Cheque"><i class="fas fa-money-check-alt"></i></a>                   
                                    @endif
                                </td>                          
                                    <td class="text-center">{{ $datos[$i]["nombre"]}}</td>
                                    <td class="text-center">{{$datos[$i]["fecha"]}}</td>
                                    <td class="text-center">{{ $datos[$i]["valor"]}}</td>  
                                    <td class="text-center">{{ $datos[$i]["ncheque"]}}</td>                              
                                </tr>
                                @endfor
                            @endif 
                        </tbody>
                    </table>
                </div>
            </div>
        </div> 
        
    

   
    </form>
    <!-- /.card-body -->

</div>

@endsection



