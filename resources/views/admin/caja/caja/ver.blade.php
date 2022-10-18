@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Ver Caja</h3>
        <!-- 
        <button onclick='window.location = "{{ url("caja") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    --> 
    <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">        
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Nombre</label>
            <div class="col-sm-10">
                <label class="form-control">{{$caja->caja_nombre}}</label>
            </div>
        </div>
        @if(Auth::user()->empresa->empresa_contabilidad == '1')
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Cuenta</label>
            <div class="col-sm-10">
                <label class="form-control">{{$caja->cuenta->cuenta_numero.'  - '.$caja->cuenta->cuenta_nombre}}</label>
            </div>
        </div> 
        @endif
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Sucursal</label>
            <div class="col-sm-10">
                <label class="form-control">{{$caja->sucursal->sucursal_nombre}}</label>
            </div>
        </div>        
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Estado</label>
            <div class="col-sm-10">
                @if($caja->caja_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                @endif
            </div>
        </div>
        <div class="form-group row">
                <label class="col-sm-2 col-form-label">Usuarios Asignados</label>
            </div>              
            <div class="form-group row">
                <div class="col-sm-12">
                <div class="well listview-pagupa">
                        <ul class="list-group">
                        @foreach($usuarios as $usuario)
                            @if(true)
                                <?php $puntoEstado=0 ?>
                                @foreach($cajaUsers as $cajaUser)
                                    @if($cajaUser->user_id == $usuario->user_id)
                                        <?php $puntoEstado=1 ?> 
                                    @endif
                                @endforeach                       
                                @if($puntoEstado==1)
                                    <li class="list-group-item"><i class="fa fa-check-square neo-azul fa-lg"></i>&nbsp;&nbsp;<label> {{ $usuario->user_nombre }}</label></li>
                                @endif                               
                            @endif                            
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>           
        <!-- /.card-footer -->
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection