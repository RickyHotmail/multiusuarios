<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Orden_Recepcion extends Model
{
    use HasFactory;
    protected $table='orden_recepcion';
    protected $primaryKey = 'ordenr_id';
    public $timestamps=true;
    protected $fillable = [
        'ordenr_numero',
        'ordenr_serie',
        'ordenr_secuencial', 
        'ordenr_guia', 
        'ordenr_fecha',
        'ordenr_observacion',
        'ordenr_estado',
        'bodega_id',
        'proveedor_id',
        'rango_id',
        'transaccion_id',
        'diario_id'
    ];
    protected $guarded =[
    ];
    public function scopeOrdenes($query){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('ordenr_estado','=','1')->orderBy('ordenr_numero','asc');
    }
    public function scopeOrden($query, $id){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('ordenr_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('orden_recepcion.rango_id','=',$id);
    }
    public function scopeProveedorDistinsc($query){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('proveedor.proveedor_nombre','asc');
    }
    public function scopeSucursalDistinsc($query){
        return $query->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');
    }
    public function scopeProductoDistinsc($query){
        return $query->join('detalle_or','orden_recepcion.orden_id','=','detalle_or.ordenr_id')->join('producto','producto.producto_id','=','detalle_or.producto_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('producto_nombre','asc');
    }
    public function scopeEstadoDistinsc($query){
        return $query->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('ordenr_estado','asc');
    }
    public function scopeOrdenBusqueda($query){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('ordenr_numero','asc');
    }
    public function scopeTodosDiferentes($query, $desde,$hasta, $estado,$proveedor,$sucursal){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('ordenr_fecha', '>=',$desde)
        ->where('ordenr_fecha', '<=', $hasta)
        ->where('ordenr_estado','=',$estado)
        ->where('proveedor_nombre','=',$proveedor)
        ->where('sucursal_nombre','=',$sucursal);
                  
    }
    public function scopeFiltrar($query, $desde,$hasta, $estado,$proveedor,$sucursal){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('ordenr_fecha', '>=',$desde)
        ->where('ordenr_fecha', '<=', $hasta)
        ->where('ordenr_estado','=',$estado)
        ->where('proveedor_nombre','=',$proveedor)
        ->where('sucursal_nombre','=',$sucursal);
                  
    }
    public function scopeFiltrarbusqueda($query,$fechatodo, $desde,$hasta, $estado,$proveedor,$sucursal){
         $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
        if($fechatodo != 'on'){
            $query->where('orden_recepcion.ordenr_fecha', '>=', $desde)->where('orden_recepcion.ordenr_fecha', '<=', $hasta);
        }  
        if($estado != '0'){
            $query->where('orden_recepcion.ordenr_estado', '=', $estado);
        } 
        if($sucursal != '0'){
            $query->where('sucursal.sucursal_id', '=', $sucursal);
        }
        if($proveedor != '0'){
            $query->where('proveedor.proveedor_id', '=', $proveedor);
        }   
        return $query;
                  
    }
    public function scopeFecha($query, $desde,$hasta){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('ordenr_fecha', '>=',$desde)->where('ordenr_fecha', '<=', $hasta);
    }
    public function scopeBurcarEstado($query, $estado){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('ordenr_estado','=',$estado);
    }
    public function scopeBuscarProveedor($query, $proveedor){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('proveedor_nombre','=',$proveedor);
    }
    public function scopeTodosDiferentesNombreFecha($query, $desde,$hasta, $proveedor){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('ordenr_fecha', '>=',$desde)
        ->where('ordenr_fecha', '<=', $hasta)
        ->where('proveedor_nombre','=',$proveedor);
    }
    public function scopeTodosDiferentesEstadoFecha($query, $desde,$hasta, $estado){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('ordenr_fecha', '>=',$desde)
        ->where('ordenr_fecha', '<=', $hasta)
        ->where('ordenr_estado','=',$estado);
    }
    public function scopeTodosDiferentesNombreEstado($query, $estado,$proveedor){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('ordenr_estado','=',$estado)
        ->where('proveedor_nombre','=',$proveedor);
    }


    public function scopeTodosDiferentesFechaClientesurcursal($query, $desde,$hasta, $cliente, $sucursal){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('ordenr_fecha', '>=',$desde)->where('ordenr_fecha', '<=', $hasta)
        ->where('proveedor_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeTodosDiferentesFechaEstadosurcursal($query, $desde,$hasta, $estado, $sucursal){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
         ->where('ordenr_fecha', '>=',$desde)->where('ordenr_fecha', '<=', $hasta)
        ->where('ordenr_estado','=',$estado)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeTodosDiferentesFechaEstadoCliente($query, $desde,$hasta, $cliente, $estado){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('ordenr_fecha', '>=',$desde)->where('ordenr_fecha', '<=', $hasta)
        ->where('proveedor_nombre', '=', $cliente)
        ->where('ordenr_estado','=',$estado);
    }
    
    public function scopeTodosDiferentesEstadoClientesurcursal($query, $estado, $cliente, $sucursal){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('ordenr_estado','=',$estado)
        ->where('proveedor_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    
   
    
    public function scopeTodosDiferentesFechasurcursal($query, $desde,$hasta, $sucursal){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('ordenr_fecha', '>=',$desde)->where('ordenr_fecha', '<=', $hasta)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }
    public function scopeTodosDiferentesClientesurcursal($query,$cliente, $sucursal){
        return $query->join('proveedor','proveedor.proveedor_id','=','orden_recepcion.proveedor_id')->join('bodega','bodega.bodega_id','=','orden_recepcion.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
         ->where('proveedor_nombre', '=', $cliente)
        ->where('sucursal.sucursal_nombre', '=', $sucursal);
    }

    public function detalles(){
        return $this->hasMany(Detalle_OR::class, 'ordenr_id', 'ordenr_id');
    }
    public function proveedor(){
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function bodega(){
        return $this->belongsTo(Bodega::class, 'bodega_id', 'bodega_id');
    }
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
    public function transaccioncompra(){
        return $this->belongsTo(Transaccion_Compra::class, 'transaccion_id', 'transaccion_id');
    }
}
