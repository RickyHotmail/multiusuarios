@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('cierre.edit', [$cierre->cierre_id]) }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Cierre de Mes Contable</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--
                <button type="button" onclick='window.location = "{{ url("cierreMes") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Sucursal :</label>
                <div class="col-sm-10">
                    <label class="form-control">{{ $cierre->sucursal->sucursal_nombre }}</label>
                </div>
            </div>                      
            <div class="form-group row">
                <label for="idAno" class="col-sm-2 col-form-label">Año :</label>
                <div class="col-sm-10">
                    <label class="form-control">{{ $cierre->cierre_ano }}</label>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-4">
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_01" name="id_01" @if($cierre->cierre_01 == '1') checked @endif>
                                <label for="id_01" class="custom-control-label">Enero</label>
                            </div>
                        </div>        
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_02" name="id_02" @if($cierre->cierre_02 == '1') checked @endif> 
                                <label for="id_02" class="custom-control-label">Febrero</label>
                            </div>
                        </div>        
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_03" name="id_03" @if($cierre->cierre_03 == '1') checked @endif>
                                <label for="id_03" class="custom-control-label">Marzo</label>
                            </div>
                        </div>        
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_04" name="id_04" @if($cierre->cierre_04 == '1') checked @endif>
                                <label for="id_04" class="custom-control-label">Abril</label>
                            </div>
                        </div>        
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_05" name="id_05" @if($cierre->cierre_05 == '1') checked @endif>
                                <label for="id_05" class="custom-control-label">Mayo</label>
                            </div>
                        </div>        
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_06" name="id_06" @if($cierre->cierre_06 == '1') checked @endif>
                                <label for="id_06" class="custom-control-label">Junio</label>
                            </div>
                        </div>        
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_07" name="id_07" @if($cierre->cierre_07 == '1') checked @endif>
                                <label for="id_07" class="custom-control-label">Julio</label>
                            </div>
                        </div>        
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_08" name="id_08" @if($cierre->cierre_08 == '1') checked @endif>
                                <label for="id_08" class="custom-control-label">Agosto</label>
                            </div>
                        </div>        
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_09" name="id_09" @if($cierre->cierre_09 == '1') checked @endif>
                                <label for="id_09" class="custom-control-label">Septiembre</label>
                            </div>
                        </div>        
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_10" name="id_10" @if($cierre->cierre_10 == '1') checked @endif>
                                <label for="id_10" class="custom-control-label">Octubre</label>
                            </div>
                        </div>        
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_11" name="id_11" @if($cierre->cierre_11 == '1') checked @endif>
                                <label for="id_11" class="custom-control-label">Noviembre</label>
                            </div>
                        </div>        
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="id_12" name="id_12" @if($cierre->cierre_12 == '1') checked @endif>
                                <label for="id_12" class="custom-control-label">Diciembre</label>
                            </div>
                        </div>        
                    </div>
                </div>
            </div>
        </div>            
    </div>
</form>
@endsection