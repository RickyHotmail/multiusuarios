@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Cheques</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listaCheque") }}">
        @csrf
            <div class="form-group row">
                <div class="col-sm-6">
                    <div class="row">
                        <label for="idDesde" class="col-sm-2 col-form-label">Desde :</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="idDesde" name="idDesde"  @if(isset($fechaI)) value='{{ $fechaI }}'  @else value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif required>
                        </div>
                        <label for="idHasta" class="col-sm-2 col-form-label">Hasta :</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="idHasta" name="idHasta"  @if(isset($fechaF)) value='{{ $fechaF }}'  @else value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif required>
                        </div>
                    </div>
                </div>
                <label for="idTodos" class="col-sm-1 col-form-label"><center>Estado : </center></label>
                <div class="col-sm-1 col-form-label" style="margin-bottom : 0px;">
                    <div class="demo-checkbox">
                        <input type="radio" value="0"  id="idTodos" class="with-gap radio-col-deep-orange" name="idTodos" @if(isset($estadC)) @if($estadC == 0) checked @endif  @else checked @endif />
                        <label class="form-check-label" for="check1">Todos</label>
                    </div>
                </div>           
                <div class="col-sm-1 col-form-label" style="margin-bottom : 0px;">
                    <div class="demo-checkbox">
                        <input type="radio" value="1"  id="idActivos" class="with-gap radio-col-deep-orange" name="idTodos" @if(isset($estadC)) @if($estadC == 1) checked @endif  @endif/>
                        <label class="form-check-label" for="check2">Activos</label>
                    </div>
                </div>            
                <div class="col-sm-1 col-form-label" style="margin-bottom : 0px;">
                    <div class="demo-checkbox">
                        <input type="radio" value="2" id="idAnulados" class="with-gap radio-col-deep-orange" name="idTodos" @if(isset($estadC)) @if($estadC == 2) checked @endif  @endif/>
                        <label class="form-check-label" for="check3">Anulados</label>
                    </div>
                </div>
                <div class="col-sm-2">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>
            <div class="form-group row">
                <label for="idBanco" class="col-sm-1 col-form-label">Banco : </label>
                <div class="col-sm-4">
                    <select class="custom-select" id="banco_id" name="banco_id" onchange="cargarCuenta();" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($bancos as $banco)
                            <option value="{{$banco->banco_id}}" @if(isset($bancoC))  @if($bancoC == $banco->banco_id) selected @endif @endif>{{$banco->bancoLista->banco_lista_nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <label for="idBanco" class="col-sm-2 col-form-label"><center>Cuenta Bancaria : </center></label>
                <div class="col-sm-5">
                    <select class="custom-select" id="cuenta_id" name="cuenta_id" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @if(isset($cuentaBancaria)) <option value="{{ $cuentaBancaria->cuenta_bancaria_id }}" selected>{{ $cuentaBancaria->cuenta_bancaria_numero }}</option> @endif
                    </select>
                </div> 
            </div>                        
        </form>
        <hr>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Fecha Emision</th>
                    <th># Cheque</th>
                    <th>Diario</th>
                    <th>Beneficiario</th>
                    <th>Valor</th>                    
                    <th>Fecha Pago</th> 
                    <th>Estado</th>                   
                </tr>
            </thead>
            <tbody>
                @if(isset($listadoCheques))
                    @foreach($listadoCheques as $cheque)
                    <tr class="text-center">
                        <td><a href="{{ url("cheque/imprimir/{$cheque->cheque_id}") }}" target="_blank" class="btn btn-sm btn-secondary"  data-toggle="tooltip" data-placement="bottom" title="Imprimir"><i class="fa fa-print" aria-hidden="true"></i></a></td>
                        <td>{{ $cheque->cheque_fecha_emision}}</td>
                        <td>{{ $cheque->cheque_numero}}</td>
                        @if(isset($cheque->detalleDiario))
                            <td>
                            @foreach($cheque->detalleDiario as $detalleDiario)
                            <a href="{{ url("asientoDiario/ver/{$detalleDiario->diario->diario_codigo}")}}" target="_blank">{{$detalleDiario->diario->diario_codigo}}</a>
                            @endforeach
                            </td>
                        @else
                            <td></td>
                        @endif
                        <td>{{ $cheque->cheque_beneficiario}}</td>
                        <td>{{ number_format($cheque->cheque_valor,2)}}</td>                        
                        <td>{{ $cheque->cheque_fecha_pago}}</td>    
                        <td>
                            @if($cheque->cheque_estado=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>                                                     
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
<script type="text/javascript">
function cargarCuenta(){
    $.ajax({
        url: '{{ url("cuentaBancaria/searchN") }}'+ '/' +document.getElementById("banco_id").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("banco_id").value
        },
        success: function(data){
            document.getElementById("cuenta_id").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            for (var i=0; i<data.length; i++) {
                document.getElementById("cuenta_id").innerHTML += "<option value='"+data[i].cuenta_bancaria_id+"'>"+data[i].cuenta_bancaria_numero+"</option>";
            }           
        },
    });
}
</script>