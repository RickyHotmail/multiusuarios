@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
    <h3 class="card-title">Rol Pago Consolidado</h3>
    </div>
    <form class="form-horizontal" method="POST" action="{{ url("rolConsolidado") }} ">  
    @csrf
    <div class="card-body">
        <div class="form-group row">
                <input type="hidden" id="rango" name="rango"  value="{{$rangoDocumento->rango_id}}">
                <input type="hidden" id="punto" name="punto"  value="{{$rangoDocumento->punto_id}}">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <div>            
                <button type="submit" id="extraer" name="extraer" class="btn btn-success float-right"><i class="fa fa-search"></i><span> Buscar Rol</span></button>                   
                </div>
                <label class="col-sm-1 col-form-label">Consumo</label>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" >  
                        <select class="custom-select select2" id="consumo" name="consumo" >               
                            @foreach($consumo as $consumos)
                                <option id="{{$consumos->centro_consumo_nombre}}" name="{{$consumos->centro_consumo_nombre}}" value="{{$consumos->centro_consumo_id}}">{{$consumos->centro_consumo_nombre}}</option>
                            @endforeach
                        </select>    
                </div>
                <label class="col-sm-1 col-form-label">Categoria</label>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" >  
                    
                            <select class="custom-select select2" id="categoria" name="categoria" >               
                                @foreach($categoria as $categorias)
                                    <option id="{{$categorias->categoria_nombre}}" name="{{$categorias->categoria_nombre}}" value="{{$categorias->categoria_id}}">{{$categorias->categoria_nombre}}</option>
                                @endforeach
                            </select>      
                </div>
                                   
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example5" class="table table-bordered table-hover table-responsive sin-salto">
                    <thead>
                    <tr class="letra-blanca fondo-azul-claro" >
                        <th class="text-center-encabesado" colspan="5"></th>        
                                      
                        <th class="text-center-encabesado"  colspan="9">Ingresos</th>
                        <th class="text-center-encabesado" colspan="15">Egresos</th>
                        <th class="text-center-encabesado" colspan="1"></th>           
                        </tr>
                        <tr class="letra-blanca fondo-azul-claro">
                            <th><div class="icheck-primary d-inline"><input type="checkbox" id="checkboxPrimary1" value="select" onClick="SELECTITEMS()" checked>
                                <label for="checkboxPrimary1"></div> </th>
                            <th  class="text-center-encabesado">Cédula </th>
                            <th class="text-center-encabesado">Nombre </th>
                        
                            <th class="text-center-encabesado">Dias trabajados</th>

                            <th class="text-center-encabesado">Sueldo </th>
                            
                            <th class="text-center-encabesado">Horas Extras </th>
                            <th class="text-center-encabesado">Horas Suplementarias </th>
                            <th class="text-center-encabesado">Viaticos </th>
                        
                            <th class="text-center-encabesado">otros Bonif. </th>
                            <th class="text-center-encabesado">Fondos Reserva </th>
                            <th class="text-center-encabesado">otros Ingresos </th>
                            <th class="text-center-encabesado">Decimo Tercero </th>
                            <th class="text-center-encabesado">Decimo Cuarto </th>
                            
                            <th class="text-center-encabesado">Total Ingr. </th>
                            <th class="text-center-encabesado">Quincena</th>
                            <th class="text-center-encabesado">Vac. Anticip.</th>
                            
                            <th class="text-center-encabesado">9.45 % IESS </th>
                            <th class="text-center-encabesado">IESS Asumido</th>
                            <th class="text-center-encabesado">EXT. Salud</th>
                            <th class="text-center-encabesado">Alimentación </th>
                            <th class="text-center-encabesado">PPQQ </th>
                            <th class="text-center-encabesado">Prest. Hipot.s</th>
                            <th class="text-center-encabesado">Anticipos </th>
                            <th class="text-center-encabesado">Prestamos </th>
                            <th class="text-center-encabesado">Multas</th>
                            <th class="text-center-encabesado">Impu. Rent. </th>
                            <th class="text-center-encabesado">Otros Egre.</th>
                            <th class="text-center-encabesado">ley salud</th>
                            <th class="text-center-encabesado">Total Egresos </th>
                            <th class="text-center-encabesado">Liquido a Recibir </th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)  
                        <tr>
                            
                            <td> 
                                    <input class="invisible" name="check[]" value="{{ $datos[$i]['IDE'] }}" />
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" id="item{{$datos[$i]['count']}}"  name="contador[]"  value="{{ $datos[$i]['count'] }}" checked> 
                                        <label for="item{{$datos[$i]['count']}}">
                                        </label>
                                    </div>
                                </td> 
                            <td width="150">{{ $datos[$i]['Dcedula'] }}</td>
                            <td width="150">{{ $datos[$i]['Dnombre'] }}</td>
                            <td width="20"> 
                                <input type="number" class="form-controltext"   name="Tdias[]" value="{{ $datos[$i]['dias'] }}" onclick="recalcular('{{ $datos[$i]['ID'] }}');" onkeyup="recalcular('{{ $datos[$i]['ID'] }}');">
                            
                            </td>
                            <td width="150"><input type="number" class="form-controltext"  name="TCSueldo[]"  value="{{ $datos[$i]['DCSueldo'] }}" required readonly><input class="invisible" name="Tsueldo[]" value="{{ $datos[$i]['Dsueldo'] }}" /></td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Textras[]" value="0"  @if($datos[$i]['SHorasExtras'] == '1') readonly  @endif onclick="recalcularfondo('{{ $datos[$i]['ID'] }}');" onkeyup="recalcularfondo('{{ $datos[$i]['ID'] }}');"></td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Thoras_suplementarias[]" value="0.00" onclick="recalcularfondo('{{ $datos[$i]['ID'] }}');" onkeyup="recalcularfondo('{{ $datos[$i]['ID'] }}');"></td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Ttransporte[]" value="0.00" onclick="recalcularfondo('{{ $datos[$i]['ID'] }}');" onkeyup="recalcularfondo('{{ $datos[$i]['ID'] }}');"></td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Totrosbon[]" value="0.00" onclick="recalculartotalIngreso('{{ $datos[$i]['ID'] }}');" onkeyup="recalculartotalIngreso('{{ $datos[$i]['ID'] }}');">
                            </td>
                            <td  width="150"><input type="number" class="form-controltext"  name="Tfondo[]"  value="{{ $datos[$i]['Dfondo'] }}" required readonly> 
                            <input class="invisible" name="Tfondoacumu[]"  value="{{ $datos[$i]['Dfondoacumula'] }}" required readonly>
                            <input class="invisible" name="Tfondoreser[]"  value="{{ $datos[$i]['fondoreser'] }}" required readonly>
                            <input class="invisible" name="Tfondofecha[]"  value="{{ $datos[$i]['fondofecha'] }}" required readonly>
                            <input class="invisible" name="Tporcefondo[]"  value="{{ $datos[$i]['porcefondo'] }}" required readonly>
                            </td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Totrosin[]" value="0.00" onclick="recalculartotalIngreso('{{ $datos[$i]['ID'] }}');" onkeyup="recalculartotalIngreso('{{ $datos[$i]['ID'] }}');" ></td>
                            <td width="150"> <input type="number" class="form-controltext"   name="TCtercero[]" value="{{ $datos[$i]['Dtercero'] }}" required readonly>
                            <input class="invisible" name="Ttercero[]"  value="{{ $datos[$i]['tercero'] }}" required readonly>
                            <input class="invisible" name="TTerceroacu[]"  value="{{ $datos[$i]['Dterceroacu'] }}" required readonly>
                            </td>
                            <td width="150"> <input type="number" class="form-controltext"   name="TCcuarto[]" value="{{ $datos[$i]['Dcuarto'] }}"required readonly >
                            <input class="invisible" name="Tcuarto[]"  value="{{ $datos[$i]['cuarto'] }}" required readonly>
                            <input class="invisible" name="TCuartoacu[]"  value="{{ $datos[$i]['Dcuartoacu'] }}" required readonly>
                            <input class="invisible" name="Tsueldobasico[]"  value="{{ $datos[$i]['sueldobasico'] }}" required readonly>
                            </td>
                            <td  width="150"><input type="number" class="form-controltext"  name="Tingresos[]"  value="{{ $datos[$i]['DTingresos'] }}" required readonly> 
                            <input class="invisible" name="Ttotalingresos[]"  value="{{ $datos[$i]['DTotalingresos'] }}" required readonly>
                            <input class="invisible" name="Tdiastraba[]"  value="{{ $datos[$i]['diastraba'] }}" required readonly>
                            </td>
                            <td  width="150">
                            <input type="number" class="form-controltext"  name="Tquincena[]" value="{{ $datos[$i]['quince'] }}"  required readonly>           
                            </td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Tvacaciones[]" value="{{ $datos[$i]['Dvacaciones'] }}" required readonly>
                                <input class="invisible" type="number" class="form-controltext"   name="Tporcentaje[]" value="100.00" >
                            </td>
                            
                            <td width="150"> <input type="text" class="form-controltext"   name="TIess[]" value="{{ $datos[$i]['Iess'] }}" required readonly>
                            <input class="invisible" name="TIesspersonal[]"  value="{{ $datos[$i]['Iesspersonal'] }}" required readonly>
                            <input class="invisible" name="TIesspatronal[]"  value="{{ $datos[$i]['Iesspatronal'] }}" required readonly>
                            <input class="invisible" name="TIECESECAP[]"  value="{{ $datos[$i]['IECESECAP'] }}" required readonly>
                            <input class="invisible" name="T%iess[]"  value="{{ $datos[$i]['%iess'] }}" required readonly>
                            
                            </td>
                            <td width="150"> <input type="number" class="form-controltext"   name="TIessasu[]" value="{{ $datos[$i]['Iessasu'] }}" required readonly>
                            <input class="invisible" name="T%iessasu_check[]"  value="{{ $datos[$i]['%iessasu_check'] }}" required readonly>
                            </td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Tsalud[]" value="0.00" onclick="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" onkeyup="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');"></td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Talimentacion[]" value="{{ $datos[$i]['Dalimentacion'] }}" onclick="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" onkeyup="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" required readonly></td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Tppqq[]" value="0.00" onclick="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" onkeyup="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');"></td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Thipotecarios[]" value="0.00" onclick="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" onkeyup="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');"></td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Tanticipos[]" value="{{ $datos[$i]['anticipos'] }}" onclick="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" onkeyup="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" required readonly> </td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Tprestamos[]" value="0.00" onclick="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" onkeyup="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');"></td>
                            
                            <td width="150"> <input type="number" class="form-controltext"   name="Tmultas[]" value="0.00" onclick="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" onkeyup="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" ></td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Timpurenta[]" value="{{ $datos[$i]['impurenta'] }}" onclick="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');"  onkeyup="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" >
                            <input class="invisible" name="Timpuesto[]"  value="{impuesto}" required readonly>
                            </td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Totrosegre[]" value="0.00" onclick="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" onkeyup="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" ></td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Tleysol[]" value="0.00" onclick="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" onkeyup="recalculartotalEgresos('{{ $datos[$i]['ID'] }}');" ></td>
                            
                            <td width="150"> <input type="number" class="form-controltext"   name="Ttotalegre[]" value="{{ $datos[$i]['totalegre'] }}" required readonly>
                                <input class="invisible" name="Ttotalegresos[]"  value="0.00" required readonly> 
                            </td>
                            <td width="150"> <input type="number" class="form-controltext"   name="Ttotal[]" value="{{ $datos[$i]['total'] }}" required readonly>
                            </td>
                           
                        </tr>
                        @endfor
                    @endif   
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
    <div style="display: none ">  
        <table>
        <tbody >
        @if(isset($quincenas))
            @for ($i = 1; $i <= count($quincenas); ++$i)
            <tr>
            <td width="150"> <input type="number" class="form-controltext"   name="quincenaid[]" value="{{ $quincenas[$i]['id'] }}" ></td>
            </tr>
            @endfor
        @endif  
        @if(isset($anticipos))
            @for ($i = 1; $i <= count($anticipos); ++$i)
            <tr>
                <td width="150"> <input type="number" class="form-controltext"   name="anticiposid[]" value="{{ $anticipos[$i]['id'] }}" ></td>
            </tr>    
            @endfor
        @endif
        @if(isset($vacaciones))
            @for ($i = 1; $i <= count($vacaciones); ++$i)
            <tr>
                <td width="150"> <input type="number" class="form-controltext"   name="vacacionesid[]" value="{{ $vacaciones[$i]['id'] }}" ></td>
            </tr>    
            @endfor
        @endif
        @if(isset($alimentar))
            @for ($i = 1; $i <= count($alimentar); ++$i)
            <tr>
                <td width="150"> <input type="number" class="form-controltext"   name="alimentacionid[]" value="{{ $alimentar[$i]['id'] }}" ></td>
            </tr>    
            @endfor
        @endif
        </tbody>
        </table>
    </div> 
    <div class="card-header">   
            <div class="card-body">
                <div class="float-right">
                    <button type="submit" id="enviar" name="enviar" class="btn btn-success btn-sm ">Generar Rol</button>
                    <a href="quincenaConsolidada" class="btn btn-danger btn-sm">Cancelar</a>
                </div>
            </div>
           
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Forma de Pago</h5>   
            <div class="card-body">
                <div class="form-group row">
                    <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Pago</label>
                    <div class="col-sm-10">
                        
                        <select class="custom-select" id="idTipo" name="idTipo"  onchange="cajaActivar();" >
                        <option value='' label>--Seleccione una opcion--</option>
                            <option value="Cheque">Cheque</option> 
                            <option value="Transferencia">Transferencia</option>                                   
                        </select>
                    </div>
                </div>
                
                <div class="form-group row">
                        <label for="banco_id" class="col-sm-2 col-form-label">Banco</label>
                        <div class="col-sm-10">
                            <select class="custom-select" id="banco_id" name="banco_id" onchange="cargarCuenta();" >
                                <option value="" label>--Seleccione una opcion--</option>
                            
                            </select>
                        </div>
                </div>
                <div class="form-group row">
                        <label for="cuenta_id" class="col-sm-2 col-form-label"># de Cuenta</label>
                        <div class="col-sm-10">
                            
                            <select class="custom-select" id="cuenta_id" name="cuenta_id"  onchange="cargarContable();" >
                                
                            </select>
                            <input type="hidden" class="form-control" id="ncuenta" name="ncuenta" >
                        </div>
                </div> 
                <div class="form-group row">
                        <label for="idCuentaContable" class="col-sm-2 col-form-label">Cuenta Contable</label>
                        <div class="col-sm-10">
                            <select class="custom-select" id="idCuentaContable"  name="idCuentaContable" disabled >
                                <option value="--Seleccione una opcion--" label>--Seleccione una opcion--</option>                                   
                            </select>
                            <input type="hidden" class="form-control" id="ncuenta_contable" name="ncuenta_contable" value="">
                        </div>
                </div>             
            </div>  
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos del Cheque</h5>
            <div class="card-body">
                <div class="form-group row">
                            <label for="idFechaCheque" class="col-sm-2 col-form-label">Fecha</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="idFechaCheque" name="idFechaCheque" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                            </div>
                </div>                
                <div class="form-group row">
                    <label for="idNcheque" class="col-sm-2 col-form-label">Inicio # de Cheque</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="idNcheque" name="idNcheque" >
                    </div>
                </div>     
            </div> 
    </div>
   
    </form>
    <!-- /.card-body -->
</div>

@endsection

<script type="text/javascript">


function cajaActivar(){
   
    if(document.getElementById("idTipo").value=="Efectivo"){
        document.getElementById("banco_id").disabled = true;
        document.getElementById("cuenta_id").disabled = true;

        document.getElementById("idFechaCheque").disabled = true;
        document.getElementById("idNcheque").disabled = true;
      

        document.getElementById("idCuentaContable").disabled = false;
        document.getElementById("banco_id").value="";
        cargarCuentaCaja();
    }
    if(document.getElementById("idTipo").value=="Cheque"){

        document.getElementById("banco_id").disabled = false;
        document.getElementById("cuenta_id").disabled = false;
        document.getElementById("idFechaCheque").disabled = false;
        document.getElementById("idNcheque").disabled = false;
       
        document.getElementById("idCuentaContable").disabled = false;
        document.getElementById("idCuentaContable").innerHTML = "";         
        document.getElementById("banco_id").innerHTML = "";
        cargarbanco();
    }
    if(document.getElementById("idTipo").value=="Transferencia"){
        document.getElementById("banco_id").disabled = false;
        document.getElementById("cuenta_id").disabled = false;
        document.getElementById("idFechaCheque").disabled = true;
        document.getElementById("idNcheque").disabled = true;
        document.getElementById("idCuentaContable").disabled = false;
        document.getElementById("idCuentaContable").innerHTML = "";         
        document.getElementById("banco_id").innerHTML = "";
        cargarbanco();
    }
    
    document.getElementById("cuenta_id").innerHTML = "";
}
function cargarbanco(){
    $.ajax({
        url: '{{ url("bancos/searchN") }}',
        dataType: "json",
        type: "GET",
        data: {
           
        },
        success: function(data){
            
            document.getElementById("cuenta_id").innerHTML = "";
            document.getElementById("idCuentaContable").innerHTML="";
            document.getElementById("banco_id").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            for (var i=0; i<data.length; i++) {
                document.getElementById("banco_id").innerHTML += "<option value='"+data[i].banco_id+"'>"+data[i].banco_lista_nombre+"</option>";
            }           
        },
    });
}
function cargarCuenta(){
    $.ajax({
        url: '{{ url("cuentaBancaria/searchN") }}'+ '/' + document.getElementById("banco_id").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("banco_id").value
        },
        success: function(data){

            document.getElementById("cuenta_id").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            document.getElementById("idCuentaContable").innerHTML="";
            for (var i=0; i<data.length; i++) {
                document.getElementById("cuenta_id").innerHTML += "<option value='"+data[i].cuenta_bancaria_id+"'>"+data[i].cuenta_bancaria_numero+"</option>";
            }           
        },
    });
}

function cargarContable(){
    document.getElementById("idCuentaContable").disabled = false;
    $.ajax({
        url: '{{ url("cuentaContable/searchN") }}'+ '/' + document.getElementById("cuenta_id").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("cuenta_id").value
        },
        success: function(data){
            
            for (var i=0; i<data.length; i++) {
                document.getElementById("idCuentaContable").innerHTML += "<option value='"+data[i].cuenta_id+"'>"+data[i].cuenta_numero+"-"+data[i].cuenta_nombre+"</option>";
                var combo = document.getElementById("cuenta_id");
                document.getElementById("ncuenta").value= combo.options[combo.selectedIndex].text;
                document.getElementById("ncuenta_contable").value= data[i].cuenta_id;
                
               
            }           
        },
    });
}
function cargarCuentaCaja(){    
    $.ajax({
        url: '{{ url("cuentasCaja/searchN") }}',
        dataType: "json",
        type: "GET",
        data: {
        
        },
        success: function(data){                  
            for (var i=0; i<data.length; i++) {
                document.getElementById("idCuentaContable").innerHTML += "<option value='"+data[i].cuenta_id+"'>"+data[i].cuenta_numero+"-"+data[i].cuenta_nombre+"</option>";
            }

        },
    });
    cargarCuentaParametrizada();
}
function cargarCuentaParametrizada(){    
    $.ajax({
        url: '{{ url("cuentaParametrizadaCaja/searchN/CAJA") }}',
        dataType: "json",
        type: "GET",
        data: {
            buscar: "CAJA"
        },
        success: function(data){

            for (var i=0; i<data.length; i++) {
                $("#idCuentaContable > option[value="+ data[i].cuenta_id +"]").attr("selected",true);
                $("#idCuentaContable").select2().val(data[i].cuenta_id).trigger("change");    
            }                  
        },
    });
}




function cargarmetodo() {
    <?php
    if(isset($fecha_hasta)){  
        ?>
         document.getElementById("fecha_hasta").value='<?php echo($fecha_hasta); ?>';
         <?php
    }
    if(isset($fecha_desde)){  
        ?>
         document.getElementById("fecha_desde").value='<?php echo($fecha_desde); ?>';
         <?php
    }
    ?>
}
function recalcular(id) {   
    
   $("input[name='TCSueldo[]']")[id].value=Number((Number($("input[name='Tsueldo[]']")[id].value)/30)*Number($("input[name='Tdias[]']")[id].value)).toFixed(2); 
    recalcularfondo(id);   
}
function recalcularfondo(id) {   
    if($("input[name='Tfondofecha[]']")[id].value == 1){
        $("input[name='Tfondo[]']")[id].value=Number(((Number($("input[name='TCSueldo[]']")[id].value)
        
        +Number($("input[name='Textras[]']")[id].value)
        +Number($("input[name='Thoras_suplementarias[]']")[id].value)
        +Number($("input[name='Ttransporte[]']")[id].value))*Number($("input[name='Tporcefondo[]']")[id].value))/100).toFixed(2);
        
    } 
    if($("input[name='T%iessasu_check[]']")[id].value == 1){
        $("input[name='TIessasu[]']")[id].value = iees(id);
        $("input[name='TIess[]']")[id].value = 0;
    }
    else{
        $("input[name='TIess[]']")[id].value = iees(id);
        $("input[name='TIessasu[]']")[id].value = 0;

    }
    recalcularcuarto(id);
    recalculartecero(id);
    recalculartotalIngreso(id);
    recalculartotalEgresos(id);
  
}
function recalculartotalIngreso(id) {
   
   $("input[name='Tingresos[]']")[id].value=Number(Number($("input[name='TCSueldo[]']")[id].value)
       
       +Number($("input[name='Textras[]']")[id].value)
       +Number($("input[name='Thoras_suplementarias[]']")[id].value)
       +Number($("input[name='Ttransporte[]']")[id].value)
       +Number($("input[name='Totrosbon[]']")[id].value)
       +Number($("input[name='TCcuarto[]']")[id].value)
       +Number($("input[name='TCtercero[]']")[id].value)
       +Number($("input[name='Tfondo[]']")[id].value)
       +Number($("input[name='Totrosin[]']")[id].value)).toFixed(2);
    $("input[name='Ttotalingresos[]']")[id].value=Number(Number($("input[name='TCSueldo[]']")[id].value)
    +Number($("input[name='Textras[]']")[id].value)
    +Number($("input[name='Thoras_suplementarias[]']")[id].value)
    +Number($("input[name='Ttransporte[]']")[id].value)
    +Number($("input[name='Totrosbon[]']")[id].value)
    +Number($("input[name='Totrosin[]']")[id].value)).toFixed(2);
       recalculartotalliquidacion(id);
}
function recalculartotalEgresos(id) {


 $("input[name='Ttotalegre[]']")[id].value=Number(Number($("input[name='TIess[]']")[id].value)
     +Number($("input[name='TIessasu[]']")[id].value)
     +Number($("input[name='Tquincena[]']")[id].value)
     +Number($("input[name='Tvacaciones[]']")[id].value)
     +Number($("input[name='Tsalud[]']")[id].value)
     +Number($("input[name='Talimentacion[]']")[id].value)
     +Number($("input[name='Tppqq[]']")[id].value)
     +Number($("input[name='Thipotecarios[]']")[id].value)
     +Number($("input[name='Tprestamos[]']")[id].value)
     +Number($("input[name='Tanticipos[]']")[id].value)
     +Number($("input[name='Tmultas[]']")[id].value)
     +Number($("input[name='Timpurenta[]']")[id].value)
     +Number($("input[name='Tleysol[]']")[id].value)
     +Number($("input[name='Totrosegre[]']")[id].value)).toFixed(2);

     $("input[name='Ttotalegresos[]']")[id].value=Number(
     +Number($("input[name='Tsalud[]']")[id].value)
     +Number($("input[name='Talimentacion[]']")[id].value)
     +Number($("input[name='Tppqq[]']")[id].value)
     +Number($("input[name='Thipotecarios[]']")[id].value)
     +Number($("input[name='Tprestamos[]']")[id].value)
     +Number($("input[name='Tmultas[]']")[id].value)
     +Number($("input[name='Tleysol[]']")[id].value)
     +Number($("input[name='Totrosegre[]']")[id].value)).toFixed(2);
    
     recalculartotalliquidacion(id);
}
function iees(id){
    return Number(((Number($("input[name='TCSueldo[]']")[id].value)
       
        +Number($("input[name='Textras[]']")[id].value)
        +Number($("input[name='Thoras_suplementarias[]']")[id].value)
        +Number($("input[name='Ttransporte[]']")[id].value))*Number($("input[name='T%iess[]']")[id].value))/100).toFixed(2);   
}

function recalculartecero(id) {
    if($("input[name='Ttercero[]']")[id].value == 1){
        $("input[name='TCtercero[]']")[id].value=Number((Number($("input[name='TCSueldo[]']")[id].value)
      
        +Number($("input[name='Textras[]']")[id].value)
        +Number($("input[name='Thoras_suplementarias[]']")[id].value)
        +Number($("input[name='Totrosbon[]']")[id].value))/12).toFixed(2);
       
    }
}
function recalcularcuarto(id) {
    if($("input[name='Tcuarto[]']")[id].value == 1){
        
        $("input[name='TCcuarto[]']")[id].value=Number((Number($("input[name='Tsueldobasico[]']")[id].value)/360)*Number($("input[name='Tdias[]']")[id].value)).toFixed(2);
      
      

    }
}
function recalculartotalliquidacion(id) {
    $("input[name='Ttotal[]']")[id].value=Number( Number($("input[name='Tingresos[]']")[id].value)
        -Number($("input[name='Ttotalegre[]']")[id].value)).toFixed(2);

}


</script>
