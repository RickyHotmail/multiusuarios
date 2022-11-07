@extends ('admin.layouts.admin')
@section('principal')

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Autorizar Pagos</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Fecha de Pago</th>
                    <th>Cuenta</th>
                    <th>N° Documento</th>
                    <th>Valor</th>
                    <th>Estado</th>
                    <th>Comprobante</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suscripcion->pagos as $pago)
                <tr class="text-center">
                    <td></td>
                    <td>{{ $pago->pago_fecha }}</td>
                    <td>{{ $pago->pago_banco_nombre }} #{{ $pago->pago_banco_numero }}</td>
                    <td>{{ $pago->pago_documento }}</td>
                    <td>{{ number_format($pago->pago_valor,2) }}</td>
                    
                    <td>
                        @if($pago->pago_estado==0)
                            Aún no verificado
                        @elseif($pago->pago_estado==1)
                            Verificado el {{ $pago->pago_fecha_verificacion }}
                        @else
                            Pago rechazado
                        @endif
                    </td>
                    <td>
                        <a target="_blank" href="{{url($pago->pago_comprobante)}}">ver comprobante</a>
                    </td>
                    <td>
                        <form method="post" action="{{url('administracion/verificarPago')}}" onsubmit="return confirm('Desea validar este Pago')">
                            @csrf
                            <input type="hidden" name="idPago" value="{{$pago->pago_id}}">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
