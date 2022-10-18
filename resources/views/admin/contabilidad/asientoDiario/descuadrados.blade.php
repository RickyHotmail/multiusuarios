@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Asientos Descuadrados</h3>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("asientoDiario/descuadrados") }} ">
        @csrf
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label">Desde:</label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                </div>
                <label for="fecha_desde" class="col-sm-1 col-form-label">Hasta:</label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                </div>
                <label class="col-sm-1 col-form-label">Sucursal :</label>
                <div class="col-lg-4 col-md-7">
                    <select class="custom-select select2" id="sucursal_id" name="sucursal_id" required>
                        <option value="0" @if(isset($sucurslaC)) @if($sucurslaC == 0) selected @endif @endif>Todas</option>
                        @foreach($sucursales as $sucursal)
                        <option value="{{$sucursal->sucursal_id}}" @if(isset($sucurslaC)) @if($sucurslaC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1">
                    <button onclick="girarGif()" type="submit" type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">  
                    <th></th>
                    <th>Fecha</th>
                    <th>Diario</th>  
                    <th>Debe</th>  
                    <th>Haber</th>  
                    <th>Diferencia</th> 
                    <th>Beneficiario</th>                  
                    <th>Tipo Documento</th>          
                    <th>Numero Documento</th>    
                    <th>Referencia</th>          
                    <th>Comentario</th>                                               
                </tr>
            </thead>            
            <tbody>
                @if(isset($diarios))
                    @foreach($diarios as $diario)
                        @if(number_format($diario->debe-$diario->haber,2) != 0)
                        <tr class="text-center">
                            <td>
                                <a href="{{ url("asientoDiario/editarD/{$diario->diario_id}") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <a href="{{ url("asientoDiario/ver/{$diario->diario_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>
                            <td>{{ $diario->diario_fecha }}</td>
                            <td>{{ $diario->diario_codigo }}</td> 
                            <td>{{ number_format($diario->debe,2) }}</td>
                            <td>{{ number_format($diario->haber,2) }}</td>
                            <td>{{ number_format($diario->debe-$diario->haber,2) }}</td>
                            <td>{{ $diario->diario_tipo_documento }}</td> 
                            <td>{{ $diario->diario_numero_documento }}</td> 
                            <td>{{ $diario->diario_beneficiario }}</td>           
                            <td>{{ $diario->diario_referencia }}</td>      
                            <td>{{ $diario->diario_comentario }}</td>                  
                        </tr>
                        @endif
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
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

    function setTipo(t){
        tipo=t
    }

    setTimeout(function(){
        console.log("registro de la funcion")
        $("#idForm").submit(function(e) {
            if(tipo=="")  return
            var form = $(this);
            form.append("excel", "descargar excel");
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