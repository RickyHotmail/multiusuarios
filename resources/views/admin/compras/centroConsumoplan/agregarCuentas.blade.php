@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url('plancentroConsumo') }}/{{ $cuentaPadre->centro_consumo_id }}/agregarSubCuenta">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Agregar Centro Consumo</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--
                <button type="button"  onclick='window.location = "{{ url("cuenta") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="cuenta_nivel" class="col-sm-3 col-form-label">Nivel</label>
                <div class="col-sm-9">
                    <input type="hidden"id="cuenta_nivel" name="cuenta_nivel" value="{{$cuentaPadre->centro_consumo_nivel + 1 }}">
                    <label class="form-control">{{$cuentaPadre->centro_consumo_nivel + 1 }}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="cuenta_numero" class="col-sm-3 col-form-label">Numero</label>
                <div class="col-sm-3">
                    <input type="hidden" id="cuenta_padre" name="cuenta_padre" value="{{$cuentaPadre->centro_consumo_numero}}"/>
                    <label class="form-control">{{$cuentaPadre->centro_consumo_numero.'.'}}</label>
                </div>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="cuenta_numero" name="cuenta_numero" placeholder="Ej. 1.1.1.1" value="{{$secuencial}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="centro_consumo_nombre" class="col-sm-3 col-form-label">Nombre</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="centro_consumo_nombre" name="centro_consumo_nombre" placeholder="Nombre" required>
                </div>
            </div>  
            <div class="form-group row">
                <label for="centro_consumo_nombre" class="col-sm-3 col-form-label">Descripcion</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="centro_consumo_descripcion" name="centro_consumo_descripcion" placeholder="Descripcion" >
                </div>
            </div>
            @if(($cuentaPadre->centro_consumo_nivel + 1)>3)
            <div class="form-group row">
                <label for="idSustento" class="col-sm-3 col-form-label">Sustento Tributario</label>
                <div class="col-sm-9">
                    <select class="custom-select select2" id="idSustento" name="idSustento" require>
                        <option value="0">----Seleccione----</option>
                        @foreach($sustentosTributario25 as $sustentoTributario25)
                            <option value="{{$sustentoTributario25->sustento_id}}">{{$sustentoTributario25->sustento_codigo.' - '.$sustentoTributario25->sustento_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>       
            @endif                       
        </div>      
    </div>
</form>
@endsection