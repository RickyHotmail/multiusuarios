@foreach($gruposPermiso as $grupo)
    <li class="nav-item ">
        <a class="nav-link">
            <i class="nav-icon {{$grupo->grupo_icono}}"></i>
            <p>
            {{$grupo->grupo_nombre}}
            <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        @foreach($tipoPermiso as $tipo)
        @if($grupo->grupo_id == $tipo->grupo_id)
        <ul class="nav nav-treeview">
            <li class="nav-item item-nivel1">
            <a  class="nav-link">
                <i class="fas fa-circle nav-icon"></i>
                <p>
                {{$tipo->tipo_nombre}}
                <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @foreach($permisosAdmin as $permiso)
                    <?php $ruta =  str_replace('D/E', '', $permiso->permiso_ruta);?>
                    @if($tipo->tipo_id == $permiso->tipo_id)
                        <li class="nav-item item-nivel2">
                            @if(str_contains($permiso->permiso_ruta, 'D/E'))
                            <a class="nav-link">
                                <i class="{{ $permiso->permiso_icono }} nav-icon"></i>
                                <p>{{ $permiso->permiso_nombre }}<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview item-nivel3">
                                @foreach(Auth::user()->puntosEmision  as $p)
                                    @if(isset($p->rangos))
                                        @if(count($p->rangos) > 0)
                                            @foreach($p->rangos as $rango)
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'factura' and $rango->tipoComprobante->tipo_comprobante_codigo == '01')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'facturacionsinOrden' and $rango->tipoComprobante->tipo_comprobante_codigo == '01')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'transaccionCompra' and $rango->tipoComprobante->tipo_comprobante_codigo == '07')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'transaccionCompraC' and $rango->tipoComprobante->tipo_comprobante_codigo == '07')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'transaccionCActivoFijo' and $rango->tipoComprobante->tipo_comprobante_codigo == '07')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'liquidacionCompra' and $rango->tipoComprobante->tipo_comprobante_codigo == '03')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'notaCredito' and $rango->tipoComprobante->tipo_comprobante_codigo == '04')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'notaCreditonuevo' and $rango->tipoComprobante->tipo_comprobante_codigo == '04')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'notaDebito' and $rango->tipoComprobante->tipo_comprobante_codigo == '05')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'guiaRemision' and $rango->tipoComprobante->tipo_comprobante_codigo == '06')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'guiaRemisionCM' and $rango->tipoComprobante->tipo_comprobante_codigo == '06')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'proforma' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS1')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'ordenDespacho' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS2')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'ordenDespachoCM' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS2')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'egresoBodega' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS3')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'egresoBodegaC' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS3')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'directo' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS3')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'indirecto' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS3')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'ingresoBodega' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS4')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'ingresoBodegaC' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS4')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'faltanteCaja' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS5')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'sobranteCaja' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS6')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'notaentrega' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS7')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'egresoCaja' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS8')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'ingresoCaja' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS9')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'egresoBanco' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS10')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'ingresoBanco' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS11')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'notaCreditoBanco' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS12')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'notaDebitoBanco' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS13')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'analisisLaboratorio' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS14')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'anticipoCliente' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS15')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'anticipoProveedor' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS16')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'anticipoEmpleado' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS17')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'ordenRecepcion' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS18')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'pquincena' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS19')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'quincenaConsolidada' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS19')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'vacacion' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS20')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'controldiario' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS21')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'rolConsolidado' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS22')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'rolindividual' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS22')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'roloperativo' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS22')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'roloperativoCM' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS22')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'rolConsolidadoCM' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS22')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'rolindividualCM' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS22')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'operativorol' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS22')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'individualrol' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS22')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'decimoC' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS23')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'individualdecimoCuarto' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS23')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'beneficiosSociales' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS24')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'beneficioSocial' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS24')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                            @if(str_replace('D/E', '', $permiso->permiso_ruta) == 'ingresoPrestamo' and $rango->tipoComprobante->tipo_comprobante_codigo == 'DS25')
                                            <li class="nav-item">
                                                <!--<a href="{{ url("{$ruta}/new/{$p->punto_id}") }}" class="nav-link">-->
                                                <a href="{{str_replace('D/E', '', $permiso->permiso_ruta)}}/new/{{ $p->punto_id}}" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>{{ $rango->rango_descripcion.' - '.$p->sucursal->sucursal_codigo.$p->punto_serie }}</p>
                                                </a>
                                            </li>
                                            @endif
                                        
                                            @endforeach
                                        @endif
                                    @endif
                                @endforeach
                            </ul>
                            @else   
                            <!--<a href="{{ url("{$permiso->permiso_ruta}") }}" class="nav-link">-->
                            <a href="{{$permiso->permiso_ruta}}" class="nav-link">
                            <i class="{{ $permiso->permiso_icono }} nav-icon"></i>
                                <p>{{ $permiso->permiso_nombre }}</p>
                            </a>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>    
            </li>
        </ul>
        @endif
        @endforeach
       
    </li>
@endforeach