@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Auditoría</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("auditoria") }}">
        @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idDescripcion" class="col-sm-1 col-form-label"><center>Usuarios</center></label>
                
                <div class="col-sm-4">
                    <select class="custom-select select2" id="usuario" name="usuario" require>
                        <option value="--TODOS--" label>--TODOS--</option>             
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->user_id }}" @if(isset($usuarioC)) @if($usuarioC == $usuario->user_id) selected @endif  @endif>{{ $usuario->user_nombre }}</option>
                            @endforeach
                    </select>
                    
                </div>
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div> 
            <div class="form-group row">
                <label for="usuario" class="col-sm-1 col-form-label"><center>Descripcion:</center></label>
                <div class="col-sm-11">
                <input type="text" class="form-control" id="idDescripcion" name="idDescripcion" @if(isset($des)) value="{{ $des }}"  @endif>
                </div>
            </div>
            
        </form>
        <hr>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Descripción</th>
                    <th>No. Documento</th>
                    <th>Usuario</th>
                    <th>Adicional</th>
                    <th>Documento</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($auditoria))
                    @foreach($auditoria as $x)
                    <tr>
                        <td class="text-center">{{ $x->auditoria_fecha}}</td>
                        <td class="text-center">{{ $x->auditoria_hora}}</td>
                        <td class="text-center">{{ $x->auditoria_descripcion}}</td>
                        <td class="text-center">{{ $x->auditoria_numero_documento}}</td>
                        <td class="text-center">{{ $x->user_nombre}}</td>
                        <td>{{ $x->auditoria_adicional}}</td>
                        @if($x->auditoria_documento_respaldo)
                            <td>
                                <a target="_blank" href="{{ url('verDocumentoAuditoria/'.$x->auditoria_id.'') }}">ver documento pdf</a>
                            </td>
                        @else
                            <td>-</td>
                        @endif
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection