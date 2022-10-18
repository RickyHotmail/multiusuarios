@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("reporteBancario") }}">
@csrf
    <div class="card card-secondary" style="position: absolute; width: 100%">
        <div class="card-header">
            <h3 class="card-title">Rerpote Bancario</h3>                                 
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="tipo" class="col-sm-1 col-form-label"><center>Documento:</center></label>
                <div class="col-sm-3">
                    <select class="custom-select select2" id="tipo" name="tipo" >
                        <option value="Cheque" label>Cheque</option> 
                        <option value="Deposito" label>Deposito</option>
                        <option value="Transferencia" label>Transferencia</option>
                        <option value="Nota Debito" label>Nota Debito</option>
                        <option value="Nota Credito" label>Nota Credito</option>         
                    </select>                                   
                </div>
                
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>

                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <div class="col-sm-1">
                    <div class="icheck-success">
                        <input type="checkbox" id="fecha_todo" name="fecha_todo">
                        <label for="fecha_todo" class="custom-checkbox"><center>Todo</center></label>
                    </div>                    
                </div>
                
                
            </div>   
            <div class="form-group row">
                <label for="nombre_sucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-3">
                    <select class="custom-select select2" id="sucursal" name="sucursal" >
                        <option value="--TODOS--" label>--TODOS--</option>                       
                        @foreach($sucursal as $sucursales)
                            <option id="{{$sucursales->sucursal_nombre}}" name="{{$sucursales->sucursal_nombre}}" value="{{$sucursales->sucursal_nombre}}" @if(isset($idsucursal)) @if($sucursales->sucursal_nombre==$idsucursal) selected @endif @endif>{{$sucursales->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <label for="banco" class="col-sm-1 col-form-label"><center>Bancos:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="banco" name="banco" onchange="cargarCuenta();">
                        <option value="--TODOS--" label>--TODOS--</option>                       
                        @foreach($bancos as $banco)
                            <option id="{{$banco->banco_lista_nombre}}" name="{{$banco->banco_lista_nombre}}" value="{{$banco->banco_lista_id}}" @if(isset($idbanco)) @if($banco->banco_lista_nombre==$idbanco) selected @endif @endif>{{$banco->banco_lista_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <label for="cuenta_id" class="col-sm-1 col-form-label"><center>Cuenta:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="cuenta_id" name="cuenta_id" >
                    <option value="--TODOS--" label>--TODOS--</option>  
                        @if(isset($cuentas))
                            @if(count($cuentas))
                                @foreach($cuentas as $cuenta)
                                    <option id="{{$cuenta->cuenta_bancaria_numero}}" name="{{$cuenta->cuenta_bancaria_numero}}" value="{{$cuenta->cuenta_bancaria_numero}}" @if(isset($cuenta_id)) @if($cuenta->cuenta_bancaria_numero==$cuenta_id) selected @endif @endif>{{$cuenta->cuenta_bancaria_numero}}</option>
                                @endforeach
                            @endif
                        @endif
                    </select>                                     
                </div>
                <div class="col-sm-1">
                    <button onclick="girarGif()" type="submit"  class="btn btn-primary btn-sm" data-toggle="modal"><i class="fa fa-search"></i></button>
                </div>
            </div>
            <hr>        
            <table id="example1" class="table table-bordered table-responsive table-hover sin-salto">
                <thead>              
                    <tr class="text-center neo-fondo-tabla">     
                        <th></th>               
                        <th>Fecha</th>
                        <th>Tipo Documento</th>
                        <th>Numero Documento</th>
                        <th>Valor</th>
                        <th>Diario</th> 
                    </tr>
                </thead>
                          
                <tbody>                                                                        
                    @if(isset($cheque)) 
                        @foreach($cheque as $x)
                        <tr class="text-center">
                                <td>    
                                </td>  
                                <td>{{ $x->fecha}}</td>  
                                <td>Cheque</td>     
                                <td>{{ $x->Numero}}</td>  
                                <td>{{ $x->Valor}}</td> 
                                <td class="text-center"><a href="{{ url("asientoDiario/ver/{$x->Diario}")}}" target="_blank">{{ $x->Diario}}</a></td>       
                            </tr>
                        @endforeach
                    @endif
                    @if(isset($tranferencia)) 
                        @foreach($tranferencia as $x)
                        <tr class="text-center">
                                <td>    
                                </td>  
                                <td>{{ $x->fecha}}</td>  
                                <td>Transferencia</td>     
                                <td>{{ $x->Numero}}</td>  
                                <td>{{ $x->Valor}}</td> 
                                <td class="text-center"><a href="{{ url("asientoDiario/ver/{$x->Diario}")}}" target="_blank">{{ $x->Diario}}</a></td>       

                            </tr>
                        @endforeach
                    @endif
                    @if(isset($depositos)) 
                        @foreach($depositos as $x)
                        <tr class="text-center">
                                <td>    
                                </td>  
                                <td>{{ $x->fecha}}</td>  
                                <td>Deposito</td>     
                                <td>{{ $x->Numero}}</td>  
                                <td>{{ $x->Valor}}</td> 
                                <td class="text-center"><a href="{{ url("asientoDiario/ver/{$x->Diario}")}}" target="_blank">{{ $x->Diario}}</a></td>       
                            </tr>
                        @endforeach
                    @endif
                    @if(isset($notadebito)) 
                        @foreach($notadebito as $x)
                        <tr class="text-center">
                                <td>    
                                </td>  
                                <td>{{ $x->fecha}}</td>  
                                <td>Nota de Debito</td>     
                                <td>{{ $x->Numero}}</td>  
                                <td>{{ $x->Valor}}</td> 
                                <td class="text-center"><a href="{{ url("asientoDiario/ver/{$x->Diario}")}}" target="_blank">{{ $x->Diario}}</a></td>       
                            </tr>
                        @endforeach
                    @endif
                    @if(isset($notacredito)) 
                        @foreach($notacredito as $x)
                        <tr class="text-center">
                                <td>    
                                </td>  
                                <td>{{ $x->fecha}}</td>  
                                <td>Nota de Credito</td>     
                                <td>{{ $x->Numero}}</td>  
                                <td>{{ $x->Valor}}</td> 
                                <td class="text-center"><a href="{{ url("asientoDiario/ver/{$x->Diario}")}}" target="_blank">{{ $x->Diario}}</a></td>       
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>        
        </div>
    </div>
    <div id="div-gif" class="col-md-12 text-center" style="position: absolute;height: 300px; margin-top: 150px; display: none">
        <img src="{{ url('img/loading.gif') }}" width=90px height=90px style="align-items: center">
    </div>
</form>

<script>
    function girarGif(){
        document.getElementById("div-gif").style.display="inline"
        console.log("girando")
    }
</script>
<script>
      <?php
      if(isset($fecha_todo)){  
        echo('document.getElementById("fecha_todo").checked=true;');
    }
   if(isset($fecha_hasta)){  
        ?>
         document.getElementById("fecha_hasta").value='<?php echo($fecha_hasta); ?>';
         <?php
    }
    if (isset($fecha_desde)) {
        ?>
       document.getElementById("fecha_desde").value='<?php echo($fecha_desde); ?>';
    
    <?php
    }
    if(isset($idcuenta)){  
        ?>
       document.getElementById("cuenta_id").value='<?php echo($idcuenta); ?>';
        <?php
    }
    ?>
    <?php
    if(isset($idbanco)){  
        ?>
       document.getElementById("banco").value='<?php echo($idbanco); ?>';
        <?php
    }
    ?>
    <?php
    if(isset($idtipo)){  
        ?>
       document.getElementById("tipo").value='<?php echo($idtipo); ?>';
        <?php
    }
    ?>
    <?php
    if(isset($idsucursal)){  
        ?>
       document.getElementById("sucursal").value='<?php echo($idsucursal); ?>';
        <?php
    }
    ?>

    function cargarCuenta() {
        $.ajax({
            url: '{{ url("cuentaBanco/searchN") }}'+ '/' +document.getElementById("banco").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("banco").value
            },
            success: function(data) {
                document.getElementById("cuenta_id").innerHTML = "<option value='--TODOS--' label>--TODOS--</option>";
                for (var i = 0; i < data.length; i++) {
                    document.getElementById("cuenta_id").innerHTML += "<option value='" + data[i].cuenta_bancaria_numero + "'>" + data[i].cuenta_bancaria_numero + "</option>";
                }
            },
        });
    }
</script>

@endsection