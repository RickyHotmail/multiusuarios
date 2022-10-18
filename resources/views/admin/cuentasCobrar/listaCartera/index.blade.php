@extends ('admin.layouts.admin')
@section('principal')
<form id="idForm" class="form-horizontal" method="POST"  action="{{ url("listaCartera") }} ">
    <div class="card card-secondary" style="position: absolute; width: 100%">
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h3 class="card-title">Lista de Cartera</h3>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button id="guardarID" name="guardarID" type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i><span> Guardar Cheque Anticipado</span></button>
                        <button type="button" id="cancelarID" name="cancelarID" onclick="javascript:location.reload()" class="btn btn-default btn-sm not-active-neo"><i class="fas fa-times-circle"></i><span> Cancelar</span></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            
            <div class="form-group row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="fecha_desde" class="col-sm-2 col-form-label">Desde:</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                        </div>
                        <label for="fecha_desde" class="col-sm-2 col-form-label">Hasta:</label>
                        <div class="col-sm-3">
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                        </div>
                        <div class="col-sm-1">
                            <div class="icheck-primary">
                                <input type="checkbox" id="fecha_todo" name="fecha_todo" @if(isset($todo)) @if($todo == 1) checked @endif @else checked @endif>
                                <label for="fecha_todo" class="custom-checkbox"><center>Todo</center></label>
                            </div>                    
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nombre_cuenta" class="col-sm-2 col-form-label">Cliente:</label>
                        <div class="col-sm-10">
                            <select class="custom-select select2" id="clienteID" name="clienteID" required>
                                <option value="0" @if(isset($clienteC)) @if($clienteC == 0) selected @endif @endif>Todos</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{$cliente->cliente_id}}" @if(isset($clienteC)) @if($clienteC == $cliente->cliente_id) selected @endif @endif>{{$cliente->cliente_nombre}}</option>
                                @endforeach
                            </select>                    
                        </div> 
                    </div>
                    <div class="form-group row">
                        <label for="idBanco" class="col-lg-2 col-md-2 col-form-label">Sucursal :</label>
                        <div class="col-lg-7 col-md-7">
                            <select class="custom-select" id="sucursal_id" name="sucursal_id" required>
                                <option value="0" @if(isset($sucurslaC)) @if($sucurslaC == 0) selected @endif @endif>Todas</option>
                                @foreach($sucursales as $sucursal)
                                <option value="{{$sucursal->sucursal_id}}" @if(isset($sucurslaC)) @if($sucurslaC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" onclick="girarGif()" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                            <button type="submit" onclick="setTipo('&pdf=descarga')" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                            <button type="submit" onclick="setTipo('&excel=descarga')" id="excel" name="excel" class="btn btn-success"><i class="fas fa-file-excel"></i></button>
                        </div>
                    </div>
                    
                </div>
                <div class="col-sm-1">
                    <div style="border-radius: 5px; border: 1px solid #ccc9c9;padding-top: 10px;">
                        <div class="form-group row izquierda15 " style="padding-right: 30px;">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="formaCredito" name="formaCredito" @if(isset($tipoCre)) @if($tipoCre == 'on') checked @endif @else checked @endif>
                                <label for="formaCredito" class="custom-control-label">Credito</label>
                            </div>
                        </div>
                        <div class="form-group row izquierda15 derecha15">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="formaContado" name="formaContado" @if(isset($tipoCon)) @if($tipoCon == 'on') checked @endif @else checked @endif>
                                <label for="formaContado" class="custom-control-label">Contado</label>
                            </div>
                        </div>
                        <div class="form-group row izquierda15 derecha15">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="formaEfectivo" name="formaEfectivo" @if(isset($tipoEfe)) @if($tipoEfe == 'on') checked @endif @else checked @endif>
                                <label for="formaEfectivo" class="custom-control-label">Efectivo</label>
                            </div>
                        </div>
                        <div class="form-group row izquierda15 derecha15">
                            <div class="custom-control custom-checkbox">
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div style="border-radius: 5px; border: 1px solid #ccc9c9;padding-top: 10px;">
                        <div class="form-group row izquierda15 derecha15" >
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input custom-control-input-primary" value="0" type="radio" id="customRadio1" name="tipoConsulta" @if(isset($tipo)) @if($tipo == 0) checked @endif @else checked @endif>
                                <label for="customRadio1" class="custom-control-label">General</label>
                            </div>
                        </div>
                        <div class="form-group row izquierda15 derecha15">
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input custom-control-input-primary" value="1" type="radio" id="customRadio2" name="tipoConsulta" @if(isset($tipo)) @if($tipo == 1) checked @endif @endif>
                                <label for="customRadio2" class="custom-control-label">Lista de Deudas</label>
                            </div>
                        </div>
                        <div class="form-group row izquierda15 derecha15">
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input custom-control-input-primary" value="2" type="radio" id="customRadio3" name="tipoConsulta" @if(isset($tipo)) @if($tipo == 2) checked @endif @endif>
                                <label for="customRadio3" class="custom-control-label">Lista de Pagos</label>
                            </div>
                        </div>
                        <div class="form-group row izquierda15 derecha15">
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input custom-control-input-primary" value="3" type="radio" id="customRadio4" name="tipoConsulta" @if(isset($tipo)) @if($tipo == 3) checked @endif @endif>
                                <label for="customRadio4" class="custom-control-label">Estado de Cuenta</label>
                            </div>
                        </div>
                    </div>
                </div>     
                <div class="col-sm-3">
                    <div class="row izquierda10 derecha10">
                        <label for="idDesde" class="col-sm-6 col-form-label">Total Monto</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control derecha-texto" id="idMonto" name="idMonto"  value='@if(isset($monto)) {{ number_format($monto,2) }} @else 0.00 @endif' readonly>
                        </div>
                    </div>
                    <div class="row izquierda10 derecha10">
                        <label for="idDesde" class="col-sm-6 col-form-label">Total Saldo</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control derecha-texto" id="idSaldo" name="idSaldo"  value='@if(isset($saldo)) {{ number_format($saldo,2) }} @else 0.00 @endif' readonly>
                        </div>
                    </div>   
                    <div class="row izquierda10 derecha10">
                        <label for="idDesde" class="col-sm-6 col-form-label">Facturas Vencidas</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control derecha-texto" id="idVencidas" name="idVencidas"  value='@if(isset($vencidas)) {{ number_format($vencidas,2) }} @else 0.00 @endif' readonly>
                        </div>
                    </div>
                    <div class="row izquierda10 derecha10">
                        <label for="idDesde" class="col-sm-6 col-form-label">Facturas a Vencer</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control derecha-texto" id="idAVencer" name="idAVencer"  value='@if(isset($vencer)) {{ number_format($vencer,2) }} @else 0.00 @endif' readonly>
                        </div>
                    </div>
                </div>                
            </div>     
            <table id="example4" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th>Ruc</th>
                        <th>Nombre</th>
                        <th>Documento</th>
                        <th>Monto</th>
                        @if(isset($tipo)) @if($tipo != 3)<th>Pagos</th>@endif @endif
                        <th>Saldo</th>  
                        <th>Fecha</th>
                        <th>Termino</th>                  
                        <th>Plazo</th>
                        <th>Transc.</th>
                        <th>Ret.</th>
                        <th WIDTH="8%">N° Cheque.</th>
                        <th WIDTH="15%">Banco.</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)   
                            <tr>
                                <td @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla'] && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['ruc'] }}<input type="hidden" name="idRuc[]" value="{{ $datos[$i]['ruc'] }}"/><input type="hidden" name="idTot[]" value="{{ $datos[$i]['tot'] }}"/></td>
                                <td @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['nom'] }}<input type="hidden" name="idNom[]" value="{{ $datos[$i]['nom'] }}"/></td>
                                <td @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['doc'] }}<input type="hidden" name="idDoc[]" value="{{ $datos[$i]['doc'] }}"/></td>
                                <td class="centrar-texto" @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>@if($datos[$i]['mon'] <> '') {{ number_format($datos[$i]['mon'],2) }} @endif<input type="hidden" name="idMon[]" value="{{ $datos[$i]['mon'] }}"/></td>
                                @if(isset($tipo)) @if($tipo != 3)<td class="centrar-texto" @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>@if($datos[$i]['pag'] <> '') {{ number_format($datos[$i]['pag'],2) }} @endif<input type="hidden" name="idPag[]" value="{{ $datos[$i]['pag'] }}"/></td>@endif @endif
                                <td class="centrar-texto" @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>@if($datos[$i]['sal'] <> '') {{ number_format($datos[$i]['sal'],2) }} @endif<input type="hidden" name="idSal[]" value="{{ $datos[$i]['sal'] }}"/></td>
                                <td class="centrar-texto" @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['fec'] }}<input type="hidden" name="idFec[]" value="{{ $datos[$i]['fec'] }}"/></td>
                                @if($datos[$i]['tot'] == '2')
                                    <td colspan="4" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['ter'] }}<input type="hidden" name="idTer[]" value="{{ $datos[$i]['ter'] }}"/><input type="hidden" name="idPla[]" value="{{ $datos[$i]['pla'] }}"/><input type="hidden" name="idTra[]" value="{{ $datos[$i]['tra'] }}"/><input type="hidden" name="idRet[]" value="{{ $datos[$i]['ret'] }}"/></td>
                                    @if($datos[$i]['nom']=='FACTURA')
                                    <td class="centrar-texto" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif><input type="text"  class="form-controltext" name="ncheque[]"  value="{{$datos[$i]['cheque']}}"><input type="hidden" name="idfac[]" value="{{$datos[$i]['ide']}}"></td>
                                    <td  @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>
                                        
                                        <select  id="bancoID" style="font-size:20px" name="bancoID" >
                                            <option value =''   selected>Seleccionar Banco</option>
                                            @foreach($bancos as $banco)
                                                <option value="{{$banco->banco_lista_nombre}}"@if(!empty($datos[$i]['banco'])) @if($banco->banco_lista_nombre==$datos[$i]['banco']) selected @endif @endif>{{$banco->banco_lista_nombre}}  </option>
                                            @endforeach
                                        </select> 
                                        
                                    </td>
                                    @else
                                    <td class="centrar-texto"  @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif><input type="hidden" name="ncheque[]"  value="{{$datos[$i]['cheque']}}"><input type="hidden" name="idfac[]" value="{{$datos[$i]['ide']}}"></td>
                                    <td   @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>
                                        <input type="hidden" name="bancoID[]" value="">
                                    </td>   
                                    @endif
                                @else
                                    <td class="centrar-texto" @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['ter'] }}<input type="hidden" name="idTer[]" value="{{ $datos[$i]['ter'] }}"/></td>
                                    <td class="centrar-texto" @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['pla'] }}<input type="hidden" name="idPla[]" value="{{ $datos[$i]['pla'] }}"/></td>
                                    <td class="centrar-texto" @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['tra'] }}<input type="hidden" name="idTra[]" value="{{ $datos[$i]['tra'] }}"/></td>
                                    <td class="centrar-texto" @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['ret'] }}<input type="hidden" name="idRet[]" value="{{ $datos[$i]['ret'] }}"/></td>
                                    @if($datos[$i]['nom']=='FACTURA')
                                    <td class="centrar-texto" @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif><input type="text" class="form-controltext"  name="ncheque[]" value="{{$datos[$i]['cheque']}}"><input type="hidden" name="idfac[]" value="{{$datos[$i]['ide']}}"></td>
                                    <td  @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>
                                        
                                        <select style="font-size:20px" id="bancoID[]" name="bancoID[]" >
                                            <option value =''  selected> Seleccionar Banco </option>
                                            @foreach($bancos as $banco)
                                                <option value="{{$banco->banco_lista_nombre}}" @if(!empty($datos[$i]['banco'])) @if($banco->banco_lista_nombre==$datos[$i]['banco']) selected @endif @endif>{{$banco->banco_lista_nombre}}</option>
                                            @endforeach
                                        </select> 
                                        
                                    </td>
                                    @else
                                    <td class="centrar-texto" @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif><input type="hidden" name="ncheque[]" value="{{$datos[$i]['cheque']}}"><input type="hidden" name="idfac[]" value="{{$datos[$i]['ide']}}"></td>
                                    <td  @if($datos[$i]['tot'] == '0') @if($datos[$i]['tra'] > $datos[$i]['pla']  && $datos[$i]['tra'] >0 ) style="background:  #DE9090;" @endif @endif @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>
                                        <input type="hidden" name="bancoID[]" value="">
                                    </td>   
                                    @endif
                                
                                @endif
                            
                            </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
           
        </div>
        <!-- /.card-body -->
   
    </div>
</form>
<div id="div-gif" class="col-md-12 text-center" style="position: absolute;height: 300px; margin-top: 150px; display: none">
    <img src="{{ url('img/loading.gif') }}" width=90px height=90px style="align-items: center">
</div>
<script>
    function girarGif(){
        document.getElementById("div-gif").style.display="inline"
        console.log("girando")
    }

    function ocultarGif(){
        document.getElementById("div-gif").style.display="none"
        console.log("no girando")
    }

    tipo=""
    function setTipo(t){tipo=t}

    setTimeout(function(){
        console.log("registro de la funcion")
        $("#idForm").submit(function(e) {
            if(tipo=="") return
            var form = $(this);
            var actionUrl = form.attr('action');


            console.log("submit "+actionUrl)
            console.log(form.serialize())
            console.log(form)
            girarGif()
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize()+tipo,
                success: function(data) {
                    setTimeout(function(){
                        ocultarGif()
                        tipo=""
                    }, 1000)
                }
            });
        });
    }, 1200)
</script>
@endsection