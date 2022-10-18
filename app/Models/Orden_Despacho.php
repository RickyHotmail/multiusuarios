<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class Orden_Despacho extends Model
{
    use HasFactory;
    protected $table='orden_despacho';
    protected $primaryKey = 'orden_id';
    public $timestamps=true;
    protected $fillable = [
        'orden_numero',
        'orden_serie',
        'orden_secuencial', 
        'orden_fecha',
        'orden_tipo_pago',
        'orden_dias_plazo', 
        'orden_fecha_pago', 
        'orden_subtotal',
        'orden_tarifa0',
        'orden_tarifa12', 
        'orden_descuento', 
        'orden_iva',  
        'orden_total',  
        'orden_comentario',
        'orden_porcentaje_iva',
        'orden_reserva',
        'orden_estado',
        'bodega_id',
        'cliente_id',
        'gr_id',
        'rango_id',
        'vendedor_id',
        'factura_id',
    ];
    protected $guarded =[
    ];
    public function scopeClientesDistinsc($query){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->join('tipo_identificacion','tipo_identificacion.tipo_identificacion_id','=','cliente.tipo_identificacion_id')->where('tipo_identificacion.empresa_id','=',Auth::user()->empresa_id)->orderBy('cliente.cliente_nombre','asc');
    }
    public function scopeEstadoDistinsc($query){
        return $query->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_estado','asc');
    }
    public function scopeSurcusalDistinsc($query){
        return $query->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');
    }
    public function scopeProductoDistinsc($query){
        return $query->join('detalle_orden','orden_despacho.orden_id','=','detalle_orden.orden_id')->join('producto','producto.producto_id','=','detalle_orden.producto_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('producto_nombre','asc');
    }
    public function scopeTodosOn($query, $desde,$hasta, $estado,$cliente,$sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('orden_fecha', '>=',$desde)
        ->where('orden_fecha', '<=', $hasta)
        ->where('orden_estado', '=', $estado)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)
        ;
    }
    public function scopeFecha($query, $desde,$hasta){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('orden_fecha', '>=',$desde)->where('orden_fecha', '<=', $hasta);
    }
    public function scopeFechaClientesurcursal($query, $desde,$hasta, $cliente, $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('orden_fecha', '>=',$desde)->where('orden_fecha', '<=', $hasta)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeFechaEstadosurcursal($query, $desde,$hasta, $estado, $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('orden_fecha', '>=',$desde)->where('orden_fecha', '<=', $hasta)
        ->where('orden_estado','=',$estado)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeFechaEstadoCliente($query, $desde,$hasta, $cliente, $estado){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('orden_fecha', '>=',$desde)->where('orden_fecha', '<=', $hasta)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('orden_estado','=',$estado);
    }
    public function scopeFechaEstado($query, $desde,$hasta, $estado){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('orden_fecha', '>=',$desde)->where('orden_fecha', '<=', $hasta)
        ->where('orden_estado','=',$estado);
    }
    public function scopeEstadoClientesurcursal($query, $estado, $cliente, $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('orden_estado','=',$estado)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeEstadoCliente($query, $estado, $cliente){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('orden_estado','=',$estado)
        ->where('cliente.cliente_nombre', '=', $cliente);
    }
    public function scopeEstadosurcursal($query, $estado,$sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('orden_estado','=',$estado)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeFechaCliente($query, $desde,$hasta, $cliente){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('orden_fecha', '>=',$desde)->where('orden_fecha', '<=', $hasta)
        ->where('cliente.cliente_nombre', '=', $cliente);
    }
    public function scopeFechasurcursal($query, $desde,$hasta, $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('orden_fecha', '>=',$desde)->where('orden_fecha', '<=', $hasta)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeClientesurcursal($query,$cliente, $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeBurcarEstado($query, $estado){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('orden_estado','=',$estado);
    }
    public function scopeBuscarCliente($query, $cliente){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('cliente.cliente_nombre', '=', $cliente);
    }
    public function scopeBurcarsurcursal($query, $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeBuscarDetalleProductoTodos($query){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeBuscarDetalleProductoDiferentes($query, $desde,$hasta,$estado, $cliente, $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('orden_fecha', '>=',$desde)
        ->where('orden_fecha', '<=', $hasta)
        ->where('orden_estado', '=', $estado)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeBuscarDetalleProductoFecha($query, $desde,$hasta){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('orden_fecha', '>=',$desde)
        ->where('orden_fecha', '<=', $hasta)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeBuscarDetalleProductoCliente($query, $cliente){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeBuscarDetalleProductoEstado($query, $estado){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('orden_estado', '=', $estado)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeBuscarDetalleProductoSucurasal($query, $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('sucursal.sucursal_nombre', '=', $sucursal)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeBuscarDetalleProductoEstadoSucursal($query,$estado,$sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('orden_estado', '=', $estado)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }

    public function scopeBuscarDetalleProductoEstadoCliente($query,$estado, $cliente){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('orden_estado', '=', $estado)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeBuscarDetalleProductoFechaEstado($query, $desde,$hasta,$estado){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('orden_fecha', '>=',$desde)
        ->where('orden_fecha', '<=', $hasta)
        ->where('orden_estado', '=', $estado)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeBuscarDetalleProductoFechaEstadoCliente($query, $desde,$hasta,$estado, $cliente){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('orden_fecha', '>=',$desde)
        ->where('orden_fecha', '<=', $hasta)
        ->where('orden_estado', '=', $estado)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
   
    public function scopeBuscarDetalleProductoFechaEstadoSucursal($query, $desde,$hasta,$estado,  $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('orden_fecha', '>=',$desde)
        ->where('orden_fecha', '<=', $hasta)
        ->where('orden_estado', '=', $estado)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
   
    public function scopeBuscarDetalleProductoFechaClienteSucursal($query, $desde,$hasta, $cliente, $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('orden_fecha', '>=',$desde)
        ->where('orden_fecha', '<=', $hasta)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeBuscarDetalleProductoFechaCliente($query, $desde,$hasta, $cliente){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('orden_fecha', '>=',$desde)
        ->where('orden_fecha', '<=', $hasta)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeBuscarDetalleProductoFechaSucursal($query, $desde,$hasta, $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('orden_fecha', '>=',$desde)
        ->where('orden_fecha', '<=', $hasta)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeBuscarDetalleProductoEstadoClienteSucursal($query, $estado, $cliente, $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('orden_estado', '=', $estado)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeBuscarDetalleProductoClienteSucursal($query, $cliente, $sucursal){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')
        ->join('detalle_orden','detalle_orden.orden_id','=','orden_despacho.orden_id')
        ->join('producto','producto.producto_id','=','detalle_orden.producto_id')
        ->join('tamano_producto','tamano_producto.tamano_id','=','producto.tamano_id')
        ->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal)
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }

    public function scopeOrdenes($query){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('orden_estado','=','1')->orderBy('orden_numero','asc');
    }
    public function scopeOrdenesReserva($query){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
    }
    public function scopeOrdenBusqueda($query){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('orden_numero','asc');
    }
    public function scopeOrden($query, $id){
        return $query->join('vendedor','vendedor.vendedor_id','=','orden_despacho.vendedor_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('orden_id','=',$id);
    }

    public function scopenGuiaOrden($query, $id){
        return $query->join('vendedor','vendedor.vendedor_id','=','orden_despacho.vendedor_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('gr_id','=',$id);
    }

    public function scopeSecuencial($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('orden_despacho.rango_id','=',$id);
    }
   
     ////////////////////  ORDENES CON GUIAS  RELACIONADAS ///////////////////////////////////////
    public function scopeOrdeneGuias($query){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('orden_estado','=','2');
    }
    
    public function scopeGuiasTodos($query){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
    
    }
    public function scopeOrdeneGuiasTodos($query){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('orden_estado','=','2');
    }
    public function scopeClientesOrdeneGuiasDistinsc($query){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->join('tipo_identificacion','tipo_identificacion.tipo_identificacion_id','=','cliente.tipo_identificacion_id')->where('tipo_identificacion.empresa_id','=',Auth::user()->empresa_id)->orderBy('cliente.cliente_nombre','asc');
    }
    public function scopeEstadoOrdeneGuiasDistinsc($query){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('gr_estado','asc');;
    }
    public function scopeSucursalOrdeneGuiasDistinsc($query){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');;
    }
    public function scopeGuiasTodosDiferentes($query,$fechatodo,$fechadesde,$fechahasta,$estado,$cliente,$sucursal){
        $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
        if($fechatodo != 'on'){
            $query->where('guia_remision.gr_fecha', '>=', $fechadesde)->where('guia_remision.gr_fecha', '<=', $fechahasta);
        }  
        if($estado != '--TODOS--'){
            $query->where('guia_remision.gr_estado', '=', $estado);
        } 
        if($sucursal != '0'){
            $query->where('sucursal.sucursal_id', '=', $sucursal);
        }
        if($cliente != '0'){
            $query->where('cliente.cliente_id', '=', $cliente);
        }   
        return $query;
    }
    public function scopeGuiasFecha($query,$fechadesde,$fechahasta){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('guia_remision.gr_fecha', '>=', $fechadesde)
        ->where('guia_remision.gr_fecha', '<=', $fechahasta);
    }
    public function scopeGuiasCliente($query,$cliente){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('cliente_nombre', '=', $cliente);
    }
    public function scopeGuiasEstado($query,$estado){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('guia_remision.gr_estado', '=', $estado);
    }
    public function scopeGuiasSucursal($query,$sucursal){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre', '=', $sucursal);
    }
    
    public function scopeGuiasFechaClientesurcursal($query, $desde,$hasta, $cliente, $sucursal){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('guia_remision.gr_fecha', '>=',$desde)->where('guia_remision.gr_fecha', '<=', $hasta)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeGuiasFechaEstadosurcursal($query, $desde,$hasta, $estado, $sucursal){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_fecha', '>=',$desde)->where('gr_fecha', '<=', $hasta)
        ->where('gr_estado','=',$estado)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeGuiasFechaEstadoCliente($query, $desde,$hasta, $cliente, $estado){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_fecha', '>=',$desde)->where('gr_fecha', '<=', $hasta)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('gr_estado','=',$estado);
    }
    public function scopeGuiasFechaEstado($query, $desde,$hasta, $estado){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_fecha', '>=',$desde)->where('gr_fecha', '<=', $hasta)
        ->where('gr_estado','=',$estado);
    }
    public function scopeGuiasEstadoClientesurcursal($query, $estado, $cliente, $sucursal){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_estado','=',$estado)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeGuiasEstadoCliente($query, $estado, $cliente){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_estado','=',$estado)
        ->where('cliente.cliente_nombre', '=', $cliente);
    }
    public function scopeGuiasEstadosurcursal($query, $estado,$sucursal){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_estado','=',$estado)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeGuiasFechaCliente($query, $desde,$hasta, $cliente){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_fecha', '>=',$desde)->where('gr_fecha', '<=', $hasta)
        ->where('cliente.cliente_nombre', '=', $cliente);
    }
    public function scopeGuiasFechasurcursal($query, $desde,$hasta, $sucursal){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('gr_fecha', '>=',$desde)->where('gr_fecha', '<=', $hasta)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeGuiasClientesurcursal($query,$cliente, $sucursal){
        return $query->join('guia_remision','guia_remision.gr_id','=','orden_despacho.gr_id')->join('transportista','transportista.transportista_id','=','guia_remision.transportista_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('cliente.cliente_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }



    public function scopeOrdenGuia($query, $id){
        return $query->join('vendedor','vendedor.vendedor_id','=','orden_despacho.vendedor_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('gr_id','=',$id);
    }
    public function scopeprueba($query, $id){
        return $query->join('vendedor','vendedor.vendedor_id','=','orden_despacho.vendedor_id')->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('gr_id','=',$id);
    }
    public function scopeFacturaNumero($query, $numeroFactura, $bodega){
        return $query->join('cliente','cliente.cliente_id','=','orden_despacho.cliente_id')->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->join('vendedor','vendedor.vendedor_id','=','orden_despacho.vendedor_id')->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)->where('orden_despacho.bodega_id','=',$bodega)->where('orden_estado','=','1')->where('orden_numero','like','%'.$numeroFactura.'%')->orderBy('orden_numero','asc');
    }
    public function scopeFacturasbyFecha($query, $fechaInicio, $fechaFin){
        return $query->join('bodega','bodega.bodega_id','=','orden_despacho.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('orden_fecha','>=',$fechaInicio)->where('orden_fecha','<=',$fechaFin);
    }
   

////////////////////////////////////////////
    public function detalles(){
        return $this->hasMany(Detalle_OD::class, 'orden_id', 'orden_id');
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }
    public function guia(){
        return $this->belongsTo(Guia_Remision::class, 'gr_id', 'gr_id');
    }
    public function Factura()
    {
        return $this->belongsTo(Factura_Venta::class, 'factura_id', 'factura_id');
    }
    public function bodega(){
        return $this->belongsTo(Bodega::class, 'bodega_id', 'bodega_id');
    }
    public function vendedor(){
        return $this->belongsTo(Vendedor::class, 'vendedor_id', 'vendedor_id');
    }
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
   
}
