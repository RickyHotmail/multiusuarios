<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class Detalle_TC extends Model
{
    use HasFactory;
    protected $table='detalle_tc';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_cantidad',
        'detalle_precio_unitario',         
        'detalle_descuento',       
        'detalle_iva',    
        'detalle_total',
        'detalle_descripcion',
        'detalle_estado',
        'transaccion_id',
        'producto_id',
        'bodega_id',
        'centro_consumo_id',
        'movimiento_id',
    ];
    protected $guarded =[
    ];
    public function scopeReporteDetallestc($query){
        return $query->join('transaccion_compra','transaccion_compra.transaccion_id','=','detalle_tc.transaccion_id')->join('centro_consumo','centro_consumo.centro_consumo_id','=','detalle_tc.centro_consumo_id')->where('centro_consumo.empresa_id','=',Auth::user()->empresa_id)->where('detalle_tc.detalle_estado','=','1')->orderBy('transaccion_compra.transaccion_fecha','asc');
    }
    public function scopeDetalleFactura($query, $facturaID){
        return $query->join('producto','producto.producto_id','=','detalle_tc.producto_id')->where('transaccion_id','=', $facturaID)->orderBy('detalle_id','asc');
    }
    public function scopeDetalleFacturaxProducto($query, $productoID, $fechaDesde, $fechaHasta){
        return $query->join('transaccion_compra','transaccion_compra.transaccion_id','=','detalle_tc.transaccion_id')->where('producto_id','=', $productoID)->where('transaccion_compra.transaccion_fecha','>=', $fechaDesde)->where('transaccion_compra.transaccion_fecha','<=', $fechaHasta)->orderBy('transaccion_compra.transaccion_fecha','desc');
    }
    public function transaccionCompra(){
        return $this->belongsTo(Transaccion_Compra::class, 'transaccion_id', 'transaccion_id');
    }
    public function centroConsumo(){
        return $this->belongsTo(Centro_Consumo::class, 'centro_consumo_id', 'centro_consumo_id');
    }
    public function movimiento(){
        return $this->belongsTo(Movimiento_Producto::class, 'movimiento_id', 'movimiento_id');
    }
    public function bodega(){
        return $this->belongsTo(Bodega::class, 'bodega_id', 'bodega_id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
}
