@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Verificar Compras</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("verificadorComprasSri") }}">
            @csrf
            <div class="form-group row">
                <label for="idDescripcion" class="col-sm-1 col-form-label"><center>Proveedor</center></label>
                <div class="col-sm-4">
                    <select class="form-control select2" id="idProveedor" name="idProveedor" data-live-search="true">
                        <option id="todos" name="todos" value="--TODOS--" label>--TODOS--</option>  
                        @foreach($proveedores as $proveedor)
                            <option id="{{$proveedor->proveedor_id}}" name="{{$proveedor->proveedor_id}}" value="{{$proveedor->proveedor_id}}"  @if(isset($nombre_cliente)) @if($proveedor->proveedor_id==$nombre_cliente) selected @endif @endif>                                
                                {{$proveedor->proveedor_nombre}}
                            </option>
                        @endforeach
                    </select>  
                </div>
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <!--div class="col-sm-1">
                    <div class="icheck-secondary">
                        <input type="checkbox" id="fecha_todo" name="fecha_todo" >
                        <label for="fecha_todo" style="border-right: 10px;"><center>Todo</center></label>
                    </div>  
                </div-->
            </div>   
            <div class="form-group row">  
                <label for="sucursal" class="col-sm-1 col-form-label"><center>Sucursal</center></label>
                <div class="col-sm-4">
                    <select class="form-control select2" id="sucursal" name="sucursal" data-live-search="true">
                        <option value="--TODOS--" label>--TODOS--</option>  
                        @foreach($sucursales as $sucursal)
                            <option id="{{$sucursal->sucursal_nombre}}" name="{{$sucursal->sucursal_nombre}}" value="{{$sucursal->sucursal_nombre}}" @if(isset($idsucursal)) @if($sucursal->sucursal_nombre==$idsucursal) selected @endif @endif>                                
                                {{$sucursal->sucursal_nombre}}
                            </option>
                        @endforeach
                    </select>  
                </div>
                <div class="col-sm-1">
                    <center><button onclick="girarGif()" type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>            
        </form>
        <hr>

        
        <div class="card-body table-responsive p-0" style="height: 540px;">
            <table id="example4" class="table table-head-fixed text-nowrap">     
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th>Estado</th>
                        <th>Documento</th>
                        <th>Fecha</th>
                        <th>Numero</th>
                        <th>Proveedor</th>
                        <th>Forma de Pago</th>
                        <th># Autorizacion</th>
                        <!--th>Subtotal</th>
                        <th>Tarifa 0%</th>                  
                        <th>Tarifa 12%</th>
                        <th>Descuento</th>
                        <th>Iva</th>
                        <th>Total</th-->
                    </tr>
                </thead>
                <?php $cont = $sub_total = $tarifa0 = $tarifa12 = $desc = $iva = $total = 0.00;?> 
                @if(isset($transaccionCompras))
                    @foreach($transaccionCompras as $y)
                        <?php $cont = $cont + 1; $sub_total = $sub_total + $y->transaccion_subtotal; $tarifa0 = $tarifa0 + $y->transaccion_tarifa0; $tarifa12 = $tarifa12 + $y->transaccion_tarifa12; 
                        $desc = $desc + $y->transaccion_descuento; $iva = $iva + $y->transaccion_iva; $total = $total + $y->transaccion_total;?>
                    @endforeach
                @endif  
                <tbody> 
                    @if(isset($transaccionCompras))
                        <tr class="text-center">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <!--td> <?php echo '$' . number_format($sub_total, 2) ?> </td>
                            <td> <?php echo '$' . number_format($tarifa0, 2) ?> </td>
                            <td> <?php echo '$' . number_format($tarifa12, 2) ?> </td>
                            <td> <?php echo '$' . number_format($desc, 2) ?> </td>
                            <td> <?php echo '$' . number_format($iva, 2) ?> </td>
                            <td> <?php echo '$' . number_format($total, 2) ?> </td-->
                        </tr>   
                        @foreach($transaccionCompras as $transaccionCompra)
                        @if(strtoupper($transaccionCompra->tipoComprobante->tipo_comprobante_nombre)!="FACTURA" || (strtoupper($transaccionCompra->tipoComprobante->tipo_comprobante_nombre)=="FACTURA" && strlen($transaccionCompra->transaccion_autorizacion)==49))
                        <tr>
                            <td>
                                <img src="{{ url('img/loading.gif') }}" width="25px">
                            </td>
                            <td class="text-center">{{ $transaccionCompra->tipoComprobante->tipo_comprobante_nombre}}</td>
                            <td class="text-center">{{ $transaccionCompra->transaccion_fecha}}</td>
                            <td class="text-center">{{ $transaccionCompra->transaccion_numero}}</td>
                            <td class="text-center">{{ $transaccionCompra->proveedor->proveedor_nombre}}</td>
                            <td class="text-center">{{ $transaccionCompra->transaccion_tipo_pago}}</td>
                            <td class="text-center">{{ $transaccionCompra->transaccion_autorizacion}}</td>
                            
                            <!--td class="text-rigth">${{ number_format($transaccionCompra->transaccion_subtotal,2)}}</td>
                            <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_tarifa0,2)}}</td>
                            <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_tarifa12,2)}}</td>
                            <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_descuento,2)}}</td>
                            <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_iva,2)}}</td>
                            <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_total,2)}}</td-->
                        </tr>
                        @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.card-body -->
</div>

<div id="div-gif" class="col-md-12 text-center" style="position: absolute;height: 300px; margin-top: 150px; display: none">
    <img src="{{ url('img/loading.gif') }}" width=90px height=90px style="align-items: center">
</div>
<script>
    function girarGif(){
        document.getElementById("div-gif").style.display="inline"
        console.log("girando")
    }

    function docReady(fn) {
        // see if DOM is already available
        if (document.readyState === "complete" || document.readyState === "interactive") {
            // call on next available tick
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }

        
    }    

    docReady(function() {
        console.log("cargando")

        for (var i = 2, row; row = tabla1.rows[i]; i++) {
            ultima=i
            verificarCompras(i)
        }
    });
</script>

<script>
     <?php
    if(isset($fecha_todo)){  
        echo('document.getElementById("fecha_todo").checked=true;');
    }
    if(isset($fecF)){  
        ?>
         document.getElementById("fecha_hasta").value='<?php echo($fecF); ?>';
         <?php
    }
    if(isset($fecI)){  
        ?>
         document.getElementById("fecha_desde").value='<?php echo($fecI); ?>';
         <?php
    }
    ?>
</script>



<script>
    var tabla1 = document.getElementById("example4");
    var pagina=0
    var ultima= false

    document.addEventListener('click', function(f){
        pagina= parseInt(f.target.text)
        console.log("pagina "+f.target.text)
    })


    function verificarCompras(i){
        $.ajax({
        url: '{{ url("verificarEstadoCompra/") }}',
        dataType: "json",
        async:true,
        type: "POST",
        data: {
            "_token": "{{ csrf_token() }}",
            clave:tabla1.rows[i].cells[6].innerText 
        },
        success: function(data){
            console.log(data.estado)

            if(data.estado=="SI")
                tabla1.rows[i].cells[0].innerHTML ="<i class='fa fa-check' aria-hidden='true' style='color: green'></i>"
            else
                tabla1.rows[i].cells[0].innerHTML ="<i class='fa fa-minus-square' aria-hidden='true' style='color: red'></i>"
        },
    });
    }

    setTimeout(function(){
        $(document).on("click", ".paginate_button", function(e) {
            setTimeout(function(){
                iniciar=(pagina==1? 2: 1)
                console.log("iniciar "+iniciar)
                
                //document.getElementById("example1_paginate").style.display="none"

                for (var i = iniciar, row; row = tabla1.rows[i]; i++) {
                    tabla1.rows[i].cells[0].innerHTML ="<img src='{{ url('img/loading.gif') }}' width='35px'>"
                }


                for (var i = iniciar, row; row = tabla1.rows[i]; i++) {
                    ultima=i
                    verificarCompras(i)
                }
            }, 500)
        });
    }, 3000)
</script>
@endsection
