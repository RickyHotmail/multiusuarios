@extends ('admin.layouts.admin')
@section('principal')

    <div class="card card-secondary">
        <!-- /.card-header -->
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title">Nota de Crébito de Banco</h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                    <!--
                    <button onclick='window.location = "{{ url("listanotaCreditoBancario") }}";' class="btn btn-default btn-sm float-right"><i
                class="fa fa-undo"></i>&nbsp;Atras</button>
                -->   
                    <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button> 
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de la Nota de Débito</h5>
        <div class="card-body">
            <div class="form-group row">
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                    <label>Numero</label>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <label class="form-control">{{$notaCredito->nota_numero}}</label>
                </div>
               
            </div>
            <div class="form-group row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="banco_id" class="col-sm-2 col-form-label">Banco</label>
                        <div class="col-sm-10">
                            <label class="form-control">{{ $notaCredito->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="cuentaB_id" class="col-sm-2 col-form-label">Cuenta</label>
                        <div class="col-sm-10">
                        <label class="form-control">{{ $notaCredito->cuentaBancaria->cuenta_bancaria_numero}}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="idFecha" class="col-sm-2 col-form-label">Fecha</label>
                        <div class="col-sm-10">
                            <label class="form-control">{{$notaCredito->nota_fecha}}</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idBeneficiario" class="col-sm-2 col-form-label">Beneficiario</label>
                        <div class="col-sm-10">
                        <label class="form-control">{{$notaCredito->nota_beneficiario}}</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="idMensaje" class="col-sm-2 col-form-label">Motivo</label>
                        <div class="col-sm-10">
                        <label class="form-control">{{$notaCredito->nota_descripcion}}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table id="item" class="table table-striped table-hover boder-sar tabla-item-factura" style="white-space: normal!important; border-collapse: collapse;">
                    <thead>
                        <tr class="letra-blanca" style="background-color: #0c7181;">
                            <th>MOVIMIENTO</th>
                            <th>TIPO</th>
                            <th>DESCRIPCIÓN </th>                            
                            <th class="centrar-texto">VALOR</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                    @if(isset($notaCredito)) 
                        @foreach($notaCredito->detallesTipoMovimiento as $x)
                            <tr>                           
                                <td>{{ $x->tipoMovBanco->tipo_nombre}}</td> 
                                <td>@if($x->movimientonc_tipo) {{ $x->movimientonc_tipo}} @else CREDITO  @endif</td>  
                                <td>{{ $x->movimientonc_descripcion}} </td> 
                                <td class="text-center"> <?php echo '$' . number_format($x->movimientonc_valor, 2)?> </td>                                                    
                            </tr>
                        @endforeach
                    @endif                        
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8"></div>
        </div>
        <div class="row">
            <div class="col-sm-8"></div>
            <div class="col-sm-2">
                <center><label class="col-form-label">TOTAL</label></center>
            </div>
            <div class="col-sm-2">
            <label class="form-control centrar-texto"  id="IdDIF">{{$sumatoriaTipoMov}}</label>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    
</script>
@endsection
