@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-secondary ">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar esta Contabilizacion de Rol?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('listarContabilizado.destroy', [$id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Tabla de detalles -->
            <div class="col-sm-12">
                
                <div class="col-sm-12">
                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2">
                            <i class="fas fa-square text-pagado"></i> Pagado
                            </span>
                            <span>
                            <i class="fas fa-square text-acumulado"></i> Acumulado
                            </span>
                        </div>     
                        <div class="tabladetallerol card-body table-responsive p-0" style="height: 350px;">  
                         
                            <table  class="tabladetallerol table table-bordered table-head-fixed text-nowrap">
                                <thead>
                                    <tr>   
                                        <th  class=" text-center-encabesado"> </th>
                                        <th class="ingresos text-center-encabesado" colspan="7">Ingresos</th>                        
                                        <th class=" egresos text-center-encabesado" colspan="13">Egresos </th>
                                        <th class="provisiones text-center-encabesado" colspan="9">Provisiones </th>                       
                                    </tr>  
                                    <tr >
                                        <th  class="text-center-encabesado">Empleado </th>
                                        <th class="text-center-encabesado">Sueldo</th>                        
                                        <th class="text-center-encabesado">Horas Extras </th>
                                        <th class="text-center-encabesado">Vacaciones </th> 
                                        <th class="text-center-encabesado">Viaticos </th>                       
                                        <th class="text-center-encabesado">otros Bonificaciones </th>
                                        <th class="text-center-encabesado">otros Ingresos </th>
                                        <th class="text-center-encabesado">Total Ingresos </th>

                                        <th class="text-center-encabesado">EXT. Salud</th>
                                        <th class="text-center-encabesado">Ley salud</th>
                                        <th class="text-center-encabesado">Comisariato </th>      
                                        <th class="text-center-encabesado">PPQQ </th>
                                        <th class="text-center-encabesado">Prestamos Hipotecarios</th>
                                        <th class="text-center-encabesado">Prestamos </th>
                                        <th class="text-center-encabesado">Multas</th>
                                        <th class="text-center-encabesado">IESS Asum.</th>
                                        <th class="text-center-encabesado">Apr. Perso</th>
                                        <th class="text-center-encabesado">Anticipos</th>
                                        <th class="text-center-encabesado">Imp. Renta</th>
                                        <th class="text-center-encabesado">Otros Egresos</th>
                                        <th class="text-center-encabesado">Total Egresos</th>

                                        <th class="text-center-encabesado">Apr. Patro</th>
                                        <th class="text-center-encabesado">Vacaciones Pagadas</th>
                                        <th class="text-center-encabesado">Dec. Tercero</th>
                                        <th class="text-center-encabesado">Dec. Cuarto</th>
                                        <th class="text-center-encabesado">F. Reser.</th>
                                        <th class="text-center-encabesado">IECE / SETEC</th>
                                       
                                        <th class="text-center-encabesado">Liq. Pagar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if(isset($rol))
                                @if(count($rol)>0)
                                    @foreach($rol as $roles)
                                    <tr >
                                    <td width="150" class="text-center"><input type="hidden" class="form-controltext"   name="rol[]" value="{{$roles->cabecera_id}}" required readonly>
                                        <input type="hidden" class="form-controltext"   name="empleadoid[]" value="{{$roles->empleado_id}}" required readonly> {{$roles->empleado_nombre}}</td>
                                    <td width="150" class="text-center">{{number_format($roles->sueldos, 2)}}</td>
                                    <td width="150" class="text-center">{{number_format($roles->extras , 2)}}</td>
                                    <td width="150" class="text-center">{{number_format($roles->vacaciones , 2)}}</td>
                                    <td width="150" class="text-center">{{number_format($roles->transporte , 2)}}</td>
                                    <td width="150" class="text-center">{{number_format($roles->otrabonifi, 2)}} </td>
                                    <td width="150" class="text-center">{{number_format($roles->otrosingresos, 2)}} </td> 
                                    <td  width="150" class="text-center">{{number_format($roles->ingresos, 2)}}</td>
                                    <td width="150" class="text-center">{{number_format($roles->extsalud, 2)}} </td>
                                    <td width="150" class="text-center">{{number_format($roles->leysal, 2) }}</td>
                                
                                    <td width="150" class="text-center">{{number_format($roles->comisariato, 2)}}</td>
                                    <td width="150" class="text-center">{{number_format($roles->ppqq, 2)}} </td>
                                    <td width="150" class="text-center">{{number_format($roles->hipoteca, 2)}}</td> 
                                    <td width="150" class="text-center">{{number_format($roles->prestamos, 2)}} </td>
                                    <td width="150" class="text-center">{{number_format($roles->multas , 2)}}</td>
                                    <td width="150" class="text-center">{{number_format($roles->asumido , 2)}}</td>
                                    <td width="150" class="text-center">{{number_format($roles->personal, 2) }}</td>
                                    <td width="150" class="text-center">{{number_format($roles->anticipo, 2) }}</td>
                                    <input type="hidden" id="anticipos[]" name="anticipos[]" value="{{number_format($roles->anticipo, 2) }}">
                                    <td width="150" class="text-center">{{number_format($roles->impu_renta, 2) }}</td>

                                    <td width="150" class="text-center">{{number_format($roles->otrosegre, 2)}} </td>
                                    <td width="150" class="text-center">{{number_format($roles->egresos, 2)}}</td>
                                    <td width="150" class="text-center">{{number_format($roles->patronal, 2)}}</td>
                                    <td width="150" class="text-center">{{number_format($roles->vacacionespag, 2)}}</td>
                                    <td width="150" class="text-center" @if(number_format($roles->tercero, 2)>0) style="background:  #70B1F7;"  @else style="background:  #B1E2DD;"  @endif  >@if(number_format($roles->tercero, 2)>0) {{number_format($roles->tercero, 2)}} @else {{number_format($roles->terceroacum, 2)}}  @endif</td>                   
                                    <td width="150" class="text-center" @if(number_format($roles->cuarto, 2)>0) style="background:  #70B1F7;"  @else style="background:  #B1E2DD;"  @endif  >@if(number_format($roles->cuarto, 2)>0) {{number_format($roles->cuarto, 2)}} @else {{number_format($roles->cuartoacum, 2)}}  @endif</td>                   
                                    <td width="150" class="text-center" @if(number_format($roles->fondo_reserva, 2)>0) style="background:  #70B1F7;"  @else style="background:  #B1E2DD;"  @endif  >@if(number_format($roles->fondo_reserva, 2)>0) {{number_format($roles->fondo_reserva, 2)}} @else {{number_format($roles->fondoacumula, 2)}}  @endif</td>
                                    <td width="150" class="text-center">{{number_format($roles->iecesecap, 2)}}</td>
                                 
                                    <td width="150" class="text-center">{{number_format($roles->liquido_pagar, 2)}}</td>

                                    </tr>       
                                    @endforeach
                                @endif
                                @endif
                                </tbody>
                            </table>
                        </div>
                    
                </div>
                <div class="col-sm-12">
                        <div class="tabladetallerol card-body table-responsive p-0" style="height: 350px;">        
                            <table  class="tabladetallerol table table-bordered table-head-fixed text-nowrap">
                                <thead>
                                    <tr >       
                                        <th  class="ingresos text-center-encabesado " colspan="2"> Ingresos</th>
                                        <th class="egresos text-center-encabesado" colspan="2">Egresos</th>                        
                                        <th class="provisiones text-center-encabesado" colspan="2">Provisiones </th>               
                                    </tr>  
                                </thead>
                                <tbody>
                                @if(isset($rol))
                                @if(count($rol)>0)
                                    @for ($i = 1; $i <= count($datos); ++$i)  
                                    <tr >       
                                         <td class="tipo text-center-encabesado"colspan="6"> {{ $datos[$i]['tipo'] }}</td>
                                         <input type="hidden" name="idtipo[]" id="idtipo[]" value="{{ $datos[$i]['idtipo'] }}">
                                         <input type="hidden" name="tipo[]" id="tipo[]" value="{{ $datos[$i]['tipo'] }}">
                                    </tr>
                                    <tr>
                                        <td class="encabesado-izquierda">sueldo </td>
                                        <td>{{ number_format($datos[$i]['sueldos'], 2) }} </td>
                                        <input type="hidden" name="vsueldo[]" id="vsueldo[]" value="{{ $datos[$i]['sueldos'] }}">
                                        <td class="encabesado-izquierda">extsalud </td>
                                        <td>{{ number_format($datos[$i]['extsalud'], 2) }} </td>
                                        <input type="hidden" name="vextsalud[]" id="vextsalud[]" value="{{ $datos[$i]['extsalud'] }}">
                                        <td class="encabesado-izquierda">Apor. Patro. </td>
                                        <td>{{ number_format($datos[$i]['patronal'], 2) }} </td>
                                        <input type="hidden" name="vpatronal[]" id="vpatronal[]" value="{{ $datos[$i]['patronal'] }}">
                                    </tr>
                                    <tr>
                                        <td class="encabesado-izquierda">Horas Extras </td>
                                        <td>{{ number_format($datos[$i]['extras'], 2) }}</td>
                                        <input type="hidden" name="vextras[]" id="vextras[]" value="{{ $datos[$i]['extras'] }}">
                                        <td class="encabesado-izquierda">leysal </td>
                                        <td>{{ number_format($datos[$i]['leysal'] , 2)}} </td>
                                        <input type="hidden" name="vleysal[]" id="vleysal[]" value="{{ $datos[$i]['leysal'] }}">
                                        <td class="encabesado-izquierda">Vacaciones Pag </td>
                                        <td> {{number_format( $datos[$i]['vacacionesacu'] , 2)}}</td>
                                        <input type="hidden" name="vvacacionespag[]" id="vvacacionespag[]" value="{{ $datos[$i]['vacacionesacu'] }}">
                                    </tr>
                                    <tr>
                                        <td class="encabesado-izquierda">Transporte</td>
                                        <td>{{ number_format($datos[$i]['transporte'], 2) }} </td>
                                        <input type="hidden" name="vtransporte[]" id="vtransporte[]" value="{{ $datos[$i]['transporte'] }}">
                                        <td class="encabesado-izquierda"> Prestamos Quirografarios</td>
                                        <td>{{ number_format($datos[$i]['ppqq'], 2) }} </td>
                                        <input type="hidden" name="vppqq[]" id="vppqq[]" value="{{ $datos[$i]['ppqq'] }}">
                                        <td class="encabesado-izquierda">Deci. Tercero </td>
                                        <td>{{ number_format($datos[$i]['tercero'] , 2)}}  </td>
                                        <input type="hidden" name="vtercero[]" id="vtercero[]" value="{{ $datos[$i]['tercero'] }}">
                                    
                                    </tr>
                                    <tr>
                                        <td class="encabesado-izquierda">Otras Bonificaciones </td>
                                        <td>{{ number_format($datos[$i]['otrabonifi'] , 2)}} </td>
                                        <input type="hidden" name="votrabonifi[]" id="votrabonifi[]" value="{{ $datos[$i]['otrabonifi'] }}">
                                        <td class="encabesado-izquierda"> Hipoteca</td>
                                        <td>{{ number_format($datos[$i]['hipoteca'] , 2)}} </td>
                                        <input type="hidden" name="vhipoteca[]" id="vhipoteca[]" value="{{ $datos[$i]['hipoteca'] }}">
                                        <td class="encabesado-izquierda">Deci. Cuarto</td>
                                        <td>{{number_format( $datos[$i]['cuarto'], 2) }} </td>
                                        <input type="hidden" name="vcuarto[]" id="vcuarto[]" value="{{ $datos[$i]['cuarto'] }}">
                                        
                                    </tr>
                                    <tr>
                                        <td class="encabesado-izquierda">Otras Ingresos  </td>
                                        <td>{{ number_format($datos[$i]['otrosingresos'], 2) }} </td>
                                        <input type="hidden" name="votrosingresos[]" id="votrosingresos[]" value="{{ $datos[$i]['otrosingresos'] }}">
                                        <td class="encabesado-izquierda">Comisariato</td>
                                        <td>{{ number_format($datos[$i]['comisariato'] , 2)}} </td>
                                        <input type="hidden" name="vcomisariato[]" id="vcomisariato[]" value="{{ $datos[$i]['comisariato'] }}">
                                        <td class="encabesado-izquierda">F. Reserva</td>
                                        <td>{{ number_format($datos[$i]['fondo_reserva'] , 2)}} </td>
                                        <input type="hidden" name="vfondo_reserva[]" id="vfondo_reserva[]" value="{{ $datos[$i]['fondo_reserva'] }}">
                                        
                                    </tr>                    
                                    <tr>
                                        <td class="encabesado-izquierda"> Vacaciones</td>
                                        <td>{{ number_format($datos[$i]['vacaciones'], 2) }} </td>
                                        <input type="hidden" name="vvacaciones[]" id="vvacaciones[]" value="{{ $datos[$i]['vacaciones'] }}">
                                        <td class="encabesado-izquierda">IESS Asum.</td>
                                        <td>{{ number_format($datos[$i]['asumido'], 2) }} </td>
                                       
                                        <input type="hidden" name="vasumido[]" id="vasumido[]" value="{{ $datos[$i]['asumido'] }}">
                                        <td class="encabesado-izquierda">F. Reserva(Acu)</td>
                                        <td>{{ number_format($datos[$i]['fondo_reservaacu'], 2) }}</td>
                                        <input type="hidden" name="vfondoacumula[]" id="vfondoacumula[]" value="{{ $datos[$i]['fondo_reservaacu'] }}">
                                    </tr>
                                    <tr>
                                    <td class="encabesado-izquierda"> Total Ingresos</td>
                                        <td>{{ number_format($datos[$i]['ingresos'], 2) }} </td>
                                        <input type="hidden" name="vingresos[]" id="vingresos[]" value="{{ $datos[$i]['ingresos'] }}">
                                        <td class="encabesado-izquierda">Apor. Perso.</td>
                                        <td>{{ number_format($datos[$i]['personal'] , 2)}} </td>
                                        <input type="hidden" name="vpersonal[]" id="vpersonal[]" value="{{ $datos[$i]['personal'] }}">
                                        <td class="encabesado-izquierda"> IESCE/SECAP</td>
                                        <td> {{number_format( $datos[$i]['iecesecap'] , 2)}}</td>
                                        <input type="hidden" name="viecesecap[]" id="viecesecap[]" value="{{ $datos[$i]['iecesecap'] }}">
                                    
                                    </tr>
                                    <tr>
                                        <td> </td>
                                        <td></td>
                                        <td class="encabesado-izquierda">Anticipos</td>
                                        <td>{{ number_format($datos[$i]['anticipo'], 2) }} </td>
                                        <input type="hidden" name="vanticipo[]" id="vanticipo[]" value="{{ $datos[$i]['anticipo'] }}">
                                        <td class="encabesado-izquierda">D. Tercero(Acu)</td>
                                        <td>{{ number_format($datos[$i]['terceroacu'], 2) }} </td>
                                        <input type="hidden" name="vterceroacu[]" id="vterceroacu[]" value="{{ $datos[$i]['terceroacu'] }}">
                                    
                                    </tr>
                                    
                                    <tr>
                                        <td> </td>
                                        <td></td>
                                        <td class="encabesado-izquierda">Impu. Renta</td>
                                        <td>{{number_format( $datos[$i]['impu_renta'], 2) }} </td>
                                        <input type="hidden" name="vimpu_renta[]" id="vimpu_renta[]" value="{{ $datos[$i]['impu_renta'] }}">
                                        <td class="encabesado-izquierda">D. Cuarto(Acu)</td>
                                        <td>{{ number_format($datos[$i]['cuartoacu'], 2) }} </td>
                                        <input type="hidden" name="vcuartoacu[]" id="vcuartoacu[]" value="{{ $datos[$i]['cuartoacu'] }}">
                                    
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td> </td>
                                        <td class="encabesado-izquierda">Multas</td>
                                        <td> {{number_format( $datos[$i]['multas'], 2) }}</td>
                                        <input type="hidden" name="vmultas[]" id="vmultas[]" value="{{ $datos[$i]['multas'] }}">
                                        <td class="encabesado-izquierda">Liquido Pagar</td>
                                        <td>{{ number_format($datos[$i]['liquido_pagar'], 2) }} </td>
                                        <input type="hidden" name="vliquido_pagar[]" id="vliquido_pagar[]" value="{{ $datos[$i]['liquido_pagar'] }}">
                                    
                                    </tr>
                                    <tr>
                                        <td> </td>
                                        <td></td>
                                        <td class="encabesado-izquierda">Otros Egresos</td>
                                        <td>{{ number_format($datos[$i]['otrosegre'], 2) }} </td>
                                        <input type="hidden" name="votrosegre[]" id="votrosegre[]" value="{{ $datos[$i]['otrosegre'] }}">
                                        <td></td>
                                        <td> </td>
                                    </tr>
                                    <tr>
                                        <td> </td>
                                        <td></td>
                                        <td class="encabesado-izquierda">Total Egresos</td>
                                        <td>{{ number_format($datos[$i]['egresos'] , 2)}} </td>
                                        <input type="hidden" name="vegresos[]" id="vegresos[]" value="{{ $datos[$i]['egresos'] }}">
                                        <td></td>
                                        <td> </td>
                                    </tr>
                                    @endfor
                                @endif 
                                @endif 
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">



</script>
@endsection