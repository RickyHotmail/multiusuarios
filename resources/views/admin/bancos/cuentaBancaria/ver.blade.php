@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Cuenta Bancaria</h3>
        <!-- <button onclick='window.location = "{{ url("cuentaBancaria") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        -->   
        <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">        
        <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Banco</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</label>                          
                </div>
            </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label"># de Cuenta</label>
            <div class="col-sm-10">
                <label class="form-control">{{$cuentaBancaria->cuenta_bancaria_numero}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="producto_compra_venta" class="col-sm-2 col-form-label">Tipo</label>
            <div class="col-sm-10">
                @if($cuentaBancaria->cuenta_bancaria_tipo == '1')<label class="form-control">Cuenta de Ahorros</label>@endif
                @if($cuentaBancaria->cuenta_bancaria_tipo == '2')<label class="form-control">Cuenta Corriente</label>@endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Saldo Inicial</label>
            <div class="col-sm-10">
                <label class="form-control">{{$cuentaBancaria->cuenta_bancaria_saldo_inicial}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Jefe</label>
            <div class="col-sm-10">
                <label class="form-control">{{$cuentaBancaria->cuenta_bancaria_jefe}}</label>
            </div>
        </div>
        
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Cuenta Inventario</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$cuentaBancaria->cuenta->cuenta_numero.' - '.$cuentaBancaria->cuenta->cuenta_nombre}}</label>                          
                </div>
            </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Estado</label>
            <div class="col-sm-10">
                @if($cuentaBancaria->cuenta_bancaria_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                @endif
            </div>
        </div>            
        <!-- /.card-footer -->
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection