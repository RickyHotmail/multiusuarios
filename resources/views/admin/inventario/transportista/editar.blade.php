@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('transportista.update', [$transportista->transportista_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Transportista</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--   
                <button type="button" onclick='window.location = "{{ url("transportista") }}";'  class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="transportista_cedula" class="col-sm-2 col-form-label">Cedula</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="transportista_cedula" name="transportista_cedula" placeholder="0875465726" value="{{$transportista->transportista_cedula}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="transportista_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="transportista_nombre" name="transportista_nombre" placeholder="Nombre" value="{{$transportista->transportista_nombre}}" required>
                </div>
            </div>                
            <div class="form-group row">
                <label for="transportista_placa" class="col-sm-2 col-form-label">Placa</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="transportista_placa" name="transportista_placa" placeholder="Ej. AAA-0000" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="{{$transportista->transportista_placa}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="transportista_embarcacion" class="col-sm-2 col-form-label">Embarcacion</label>
                <div class="col-sm-10">
                    <input type="tel" class="form-control" id="transportista_embarcacion" name="transportista_embarcacion" placeholder="Embarcacion" value="{{$transportista->transportista_embarcacion}}" required>
                </div>
            </div>                                                                            
            <div class="form-group row">
                <label for="transportista_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($transportista->transportista_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="transportista_estado" name="transportista_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="transportista_estado" name="transportista_estado">
                        @endif
                        <label class="custom-control-label" for="transportista_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>             
    </div>
</form>
@endsection