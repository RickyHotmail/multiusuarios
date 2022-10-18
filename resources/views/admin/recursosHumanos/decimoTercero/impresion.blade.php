@extends ('admin.layouts.admin')
@section('principal')

<div class="card">
    <div class="card-header">
    <h3 class="card-title">Decimo Tercero</h3>
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
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($decimo))
                                @foreach($decimo as $x)
                                <tr>  
                                    <td class="text-center"> 
                                   <a href="{{ url("decimoTercero/{$x->decimo_id}/imprimir") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir Rol"><i class="fa fa-print"></i></a>                   
                                   <a href="{{ url("diarioTercero/{$x->decimo_id}/imprimir") }}" target="_blank" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Imprimir Diario"><i class="fa fa-print"></i></a>                   
                                     
                                    </td>                               
                                    <td class="text-center">{{ $x->empleado->empleado_nombre}}</td>
                                    <td class="text-center">{{ $x->decimo_fecha}}</td>
                                    <td class="text-center">{{ $x->decimo_valor}}</td>                             
                                </tr>
                                @endforeach
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



