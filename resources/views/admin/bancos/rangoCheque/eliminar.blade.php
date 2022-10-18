@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar este Rango de Documento?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('rangoCheque.destroy', [$rangoCheque->rango_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
            <!-- 
                <button type="button" onclick='window.location = "{{ url("rangoCheque") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">            
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Cuenta Bancaria</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$rangoCheque->cuentaBancaria->cuenta_bancaria_numero.'  -  '.$rangoCheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</label>                          
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Rango de Inicio</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$rangoCheque->rango_inicio}}</label>
                </div>
            </div>  
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Rango de Fin</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$rangoCheque->rango_fin}}</label>                          
                </div>
            </div>           
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($rangoCheque->rango_estado=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
        </div>
        <!-- /.card-body -->      
        <!-- /.card-footer -->
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection