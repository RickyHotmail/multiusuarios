@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Cierre de Mes Contable</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST"  action="{{ url("cierreMes") }} ">
        @csrf
            <div class="form-group row">
                <label class="col-sm-1 col-form-label">Sucursal :</label>
                <div class="col-lg-4 col-md-7">
                    <select class="custom-select select2" id="sucursal_id" name="sucursal_id" required>
                        @foreach($sucursales as $sucursal)
                        <option value="{{$sucursal->sucursal_id}}" @if(isset($sucurslaC)) @if($sucurslaC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1">
                    <button onclick="girarGif()" type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
        <table class="table table-bordered table-hover">
            <thead>
                <tr class="text-center neo-fondo-tabla">  
                    <th></th>
                    <th>Año</th>
                    <th>Enero</th>
                    <th>Febrero</th>
                    <th>Marzo</th>
                    <th>Abril</th>
                    <th>Mayo</th>
                    <th>Junio</th>
                    <th>Julio</th>                                  
                    <th>Agosto</th>
                    <th>Septiembre</th>
                    <th>Octubre</th>
                    <th>Noviembre</th>
                    <th>Diciembre</th>
                </tr>
            </thead>            
            <tbody>
                @if(isset($cierres))
                    @foreach($cierres as $cierre)
                        <tr class="text-center">
                            <td>
                                <a href="{{ url("cierreMes/editar/{$cierre->cierre_id}") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <a href="{{ url("cierreMes/eliminar/{$cierre->cierre_id}") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            </td>
                           <td>{{ $cierre->cierre_ano }}</td> 
                           <td>
                                @if($cierre->cierre_01=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                                @endif
                           </td>     
                           <td>
                                @if($cierre->cierre_02=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                                @endif
                           </td>  
                           <td>
                                @if($cierre->cierre_03=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                                @endif
                           </td>  
                           <td>
                                @if($cierre->cierre_04=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                                @endif
                           </td>      
                           <td>
                                @if($cierre->cierre_05=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                                @endif
                           </td>  
                           <td>
                                @if($cierre->cierre_06=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                                @endif
                           </td>  
                           <td>
                                @if($cierre->cierre_07=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                                @endif
                           </td>  
                           <td>
                                @if($cierre->cierre_08=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                                @endif
                           </td>  
                           <td>
                                @if($cierre->cierre_09=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                                @endif
                           </td>  
                           <td>
                                @if($cierre->cierre_10=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                                @endif
                           </td>  
                           <td>
                                @if($cierre->cierre_11=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                                @endif
                           </td>  
                           <td>
                                @if($cierre->cierre_12=="1")
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
</div>
<div id="div-gif" class="col-md-12 text-center" style="position: absolute;height: 300px; margin-top: 150px; display: none">
    <img src="{{ url('img/loading.gif') }}" width=90px height=90px style="align-items: center">
</div>

<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Cierre de Mes Contable</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("cierreMes/guadar") }} ">
                @csrf
                <div class="modal-body">
                    <div class="card-body">  
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Sucursal :</label>
                            <div class="col-sm-10">
                                <select class="custom-select select2" id="sucursal_id2" name="sucursal_id2" required>
                                    @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                      
                        <div class="form-group row">
                            <label for="idAno" class="col-sm-2 col-form-label">Año :</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="idAno" name="idAno" placeholder="2022" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="id_01" name="id_01">
                                            <label for="id_01" class="custom-control-label">Enero</label>
                                        </div>
                                    </div>        
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="id_02" name="id_02">
                                            <label for="id_02" class="custom-control-label">Febrero</label>
                                        </div>
                                    </div>        
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="id_03" name="id_03">
                                            <label for="id_03" class="custom-control-label">Marzo</label>
                                        </div>
                                    </div>        
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="id_04" name="id_04">
                                            <label for="id_04" class="custom-control-label">Abril</label>
                                        </div>
                                    </div>        
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="id_05" name="id_05">
                                            <label for="id_05" class="custom-control-label">Mayo</label>
                                        </div>
                                    </div>        
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="id_06" name="id_06">
                                            <label for="id_06" class="custom-control-label">Junio</label>
                                        </div>
                                    </div>        
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="id_07" name="id_07">
                                            <label for="id_07" class="custom-control-label">Julio</label>
                                        </div>
                                    </div>        
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="id_08" name="id_08">
                                            <label for="id_08" class="custom-control-label">Agosto</label>
                                        </div>
                                    </div>        
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="id_09" name="id_09">
                                            <label for="id_09" class="custom-control-label">Septiembre</label>
                                        </div>
                                    </div>        
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="id_10" name="id_10">
                                            <label for="id_10" class="custom-control-label">Octubre</label>
                                        </div>
                                    </div>        
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="id_11" name="id_11">
                                            <label for="id_11" class="custom-control-label">Noviembre</label>
                                        </div>
                                    </div>        
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="id_12" name="id_12">
                                            <label for="id_12" class="custom-control-label">Diciembre</label>
                                        </div>
                                    </div>        
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>             
            </form>
        </div>
    </div>
</div>

<script>
    function girarGif(){
        document.getElementById("div-gif").style.display="inline"
        console.log("girando")
    }
</script>
@endsection