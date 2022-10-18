@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Siembras</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Codigo</th>
                    <th>Numero Larvas</th>
                    <th>Codigo Entregas</th>
                    <th>Fecha Inicio Costo</th>
                    <th>Inicio Siembra</th>
                    <th>Longitud</th>
                    <th>Peso</th>
                    <th>Densidad</th>
                                 
                </tr>
            </thead>            
            <tbody>
                @if(isset($siembras))
                    @foreach($siembras as $siembra)
                    <tr class="text-center">
                        <td>
                            <a href="{{ url("siembra/{$siembra->siembra_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            <a href="{{ url("siembra/{$siembra->siembra_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a href="{{ url("siembra/{$siembra->siembra_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                        <td>{{ $siembra->siembra_codigo}}</td>  
                        <td>{{ $siembra->siembra_larvas}}</td>   
                        <td>{{ $siembra->siembra_entregas}}</td>  
                        <td>{{ $siembra->siembra_fecha}}</td>
                        <td>{{ $siembra->siembra_fecha_costo}}</td>  
                        <td>{{ $siembra->siembra_longitud}}</td>
                        <td>{{ $siembra->siembra_peso}}</td>    
                        <td>{{ $siembra->siembra_densidad}}</td>    
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nueva Siembra</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("siembra") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idCodigo" class="col-sm-3 col-form-label">Piscinas</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idPiscina" name="idPiscina" onchange="codigo();" required>
                                        <option value="" label>--Seleccione una opcion--</option>
                                        @foreach($piscinas as $piscina)
                                        <option value="{{$piscina->piscina_id}}">{{$piscina->piscina_nombre}}</option>
                                        @endforeach
                                </select>
                                <input type="hidden" class="form-control" id="idArea" name="idArea" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idCodigo" class="col-sm-3 col-form-label">Codigo Siembra</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCodigo" name="idCodigo" placeholder="Codigo" readonly required>
                                <input type="hidden" class="form-control" id="idSecuencial" name="idSecuencial" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idCodigo" class="col-sm-3 col-form-label">Numero Larvas</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="idLarvas" name="idLarvas" step="any" placeholder="Numero Larvas" value="0"  onkeyup="calculo();" onclick="calculo();" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Laboratorio</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idLaboratorio" name="idLaboratorio" onchange="extraer();" required>
                                        <option value="" label>--Seleccione una opcion--</option>
                                        @foreach($laboratorios as $laboratorio)
                                        <option value="{{$laboratorio->laboratorio_id}}">{{$laboratorio->laboratorio_nombre}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                    
                        <div class="form-group row">
                            <label for="idTipo" class="col-sm-3 col-form-label">Origen Nauplio</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idNauplio" name="idNauplio"  required>
                                    <option value="" label>--Seleccione una opcion--</option>        
                                </select>
                            </div>
                        </div>                                          
                        <div class="form-group row">
                            <label for="idLargo" class="col-sm-3 col-form-label">Codigo Entregas</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" step="any" id="idEntregas" name="idEntregas" placeholder="Codigo Entregas"  required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idAncho" class="col-sm-3 col-form-label">Fecha</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="Fecha" name="Fecha" placeholder="Fecha" value="<?php echo(date("Y")."-".date("m")."-".date("d")); ?>"  required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idAltura" class="col-sm-3 col-form-label">Fecha Inicio Costo</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control"id="FechaCosto" name="FechaCosto" placeholder="Fecha Inicio Costo" value="<?php echo(date("Y")."-".date("m")."-".date("d")); ?>"  required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idArea" class="col-sm-3 col-form-label">Inicio Siembra</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idInicio" name="idInicio" placeholder="Inicio Siembra" value="<?php echo(date("Y")."-".date("m")."-".date("d")); ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idArea" class="col-sm-3 col-form-label">Longitud Larva cms</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" step="any" id="idLongitud" name="idLongitud" placeholder="Longitud Larva cms" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idDeclinacion" class="col-sm-3 col-form-label">Peso Lb</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" step="any" id="idPeso" name="idPeso" placeholder="Peso Lb" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idEntradas" class="col-sm-3 col-form-label">Densidad</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" step="any" id="idDensidad" name="idDensidad" placeholder="Densidad" required>
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label for="idSalidas" class="col-sm-3 col-form-label">Sistemas Cultivo</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idCultivo" name="idCultivo" onchange="codigo();" required>
                                    <option value="" label>--Seleccione una opcion--</option>    
                                    <option value="MONOFASICO" label>MONOFASICO</option>
                                </select>
                            </div>
                        </div> 
                        
                        <div class="form-group row">
                            <label for="idDeclinacion" class="col-sm-3 col-form-label">Precio Unitario Larva</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" step="any" id="idPrecio" name="idPrecio" placeholder="Precio Unitario Larva" required>
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
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script>
    function calculo(){
        document.getElementById("idDensidad").value=round(document.getElementById("idLarvas").value/document.getElementById("idArea").value);
    }
    function codigo(){
        var codigo='';
        if(document.getElementById("idCultivo").value=='MONOFASICO'){
            codigo='.M';
        }
        if(document.getElementById("idCultivo").value=='BIFASICO'){
            codigo='.B';
        }
        if(document.getElementById("idCultivo").value=='TRIFASICO'){
            codigo='.T';
        }
        $.ajax({
        url: '{{ url("codigosiembra") }}'+'/'+document.getElementById("idPiscina").value,
        dataType: "json",
        type: "GET",
        data: { 
         
        },
        success: function(data){           
            document.getElementById("idCodigo").value=data[0]+codigo;
            document.getElementById("idSecuencial").value=data[1];
        },
    });
    extraerpiscina();
    }
    function extraer(){
        document.getElementById("idNauplio").innerHTML = "";
        document.getElementById("idNauplio").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";   
    $.ajax({
        url: '{{ url("nauplio/search") }}'+'/'+document.getElementById("idLaboratorio").value,
        dataType: "json",
        type: "GET",
        data: {
           
        },
        success: function(data){
            for (var i=0; i<data.length; i++) {
                document.getElementById("idNauplio").innerHTML += "<option value='"+data[i].nauplio_id+"'>"+data[i].nauplio_nombre+"</option>";
              
            }  
               
        },
    });
    }
    function extraerpiscina(){
        
    $.ajax({
        url: '{{ url("piscina/search") }}'+'/'+document.getElementById("idPiscina").value,
        dataType: "json",
        type: "GET",
        data: {
           
        },
        success: function(data){
            
                document.getElementById("idArea").value=data["piscina_espejo_agua"];
              
               
        },
    });
    calculo();
    }
    function round(num) {
        var m = Number((Math.abs(num) * 100).toPrecision(15));
         m =Math.round(m) / 100 * Math.sign(num);
         return (m).toFixed(2);
    }  
</script>
@endsection