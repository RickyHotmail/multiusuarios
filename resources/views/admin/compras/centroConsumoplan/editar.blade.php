@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('plancentroConsumo.update', [$centroCon->centro_consumo_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Centro Consumo</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--     
                <button type="button" onclick='window.location = "{{ url("centroConsumo") }}";'  class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                 --> 
                 <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="cuenta_nivel" class="col-sm-2 col-form-label">Nivel</label>
                <div class="col-sm-10">
                    <input type="hidden"id="cuenta_nivel" name="cuenta_nivel" value="{{$centroCon->centro_consumo_nivel}}">
                    <label class="form-control">{{$centroCon->centro_consumo_nivel}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="cuenta_numero" class="col-sm-2 col-form-label">Numero</label>
                <div class="col-sm-4">
                    <input type="hidden" id="cuenta_padre" name="cuenta_padre" value="{{$centroCon->cuentapadre->centro_consumo_numero}}"/>
                    <label class="form-control">{{$centroCon->cuentapadre->centro_consumo_numero.'.'}}</label>
                </div>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="cuenta_numero" name="cuenta_numero" placeholder="Ej. 1.1.1.1" value="{{$centroCon->centro_consumo_secuencial}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="centro_consumo_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="centro_consumo_nombre" name="centro_consumo_nombre" placeholder="Consumo" value="{{$centroCon->centro_consumo_nombre}}" required>
                </div>
            </div>
                         
            <div class="form-group row">
                <label for="centro_consumo_descripcion" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="centro_consumo_descripcion" name="centro_consumo_descripcion" placeholder="Ingrese aqui una descripcion" value="{{$centroCon->centro_consumo_descripcion}}" >
                </div>
            </div>   
            @if(($centroCon->centro_consumo_nivel)>3)
            <div class="form-group row">
                <label for="idSustento" class="col-sm-3 col-form-label">Sustento Tributario</label>
                <div class="col-sm-9">
                    <select class="custom-select select2" id="idSustento" name="idSustento" require>
                        <option value="">----Seleccione----</option>
                        @foreach($sustentosTributario25 as $sustentoTributario25)
                            <option value="{{$sustentoTributario25->sustento_id}}" @if($sustentoTributario25->sustento_id==$centroCon->sustento_id) checked @endif>{{$sustentoTributario25->sustento_codigo.' - '.$sustentoTributario25->sustento_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>       
            @endif                                                    
            <div class="form-group row">
                <label for="centro_consumo_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($centroCon->centro_consumo_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="centro_consumo_estado" name="centro_consumo_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="centro_consumo_estado" name="centro_consumo_estado">
                        @endif
                        <label class="custom-control-label" for="centro_consumo_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection