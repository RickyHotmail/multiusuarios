@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Transferencia</h3>
        <button type="button" onclick='window.location = "{{ url("transferenciasiembra/nuevo") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
        
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Fecha</th>
                    <th>Numero Juveniles</th>
                    <th>Sistema Cultivo</th>
                    <th>Estado</th>
                                 
                </tr>
            </thead>            
            <tbody>
                @if(isset($transferencias))
                    @foreach($transferencias as $transferencia)
                    <tr class="text-center">
                        <td></td>  
                        <td>{{ $transferencia->siembra->siembra_codigo}}</td>  
                        <td>{{ $transferencia->siembrapadre->siembra_codigo}}</td>   
                        <td>{{ $transferencia->transferencia_fecha}}</td>  
                        <td>{{ $transferencia->transferencia_volumen}}</td>
                        <td>{{ $transferencia->transferencia_cultivo}}</td>  
                        <td>{{ $transferencia->transferencia_estado}}</td>
                            
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<!-- /.modal -->

@endsection