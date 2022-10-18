<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Detalle_FV extends Model
{
    use HasFactory;
    protected $table='detalle_fv';
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
        'factura_id',
        'producto_id',
        'movimiento_id'
    ];
    protected $guarded =[
    ];
    public function scopeDetalleFactura($query, $facturaID){
        return $query->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('factura_id','=', $facturaID)->orderBy('detalle_id','asc');
    }
    public function scopeDetalleFacturaTotal($query, $facturaID){
        return $query->join('factura_venta','factura_venta.factura_id','=','detalle_fv.factura_id')->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('detalle_fv.factura_id','=', $facturaID)->orderBy('detalle_id','asc');
    }
    public function scopeDetalleFacturaSuma($query, $facturaID){
        return $query->join('producto','producto.producto_id','=','detalle_fv.producto_id')->where('factura_id','=', $facturaID)->orderBy('detalle_id','asc');
    }
    public function movimiento(){
        return $this->belongsTo(Movimiento_Producto::class, 'movimiento_id', 'movimiento_id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function facturaVenta()
    {
        return $this->belongsTo(Factura_Venta::class, 'factura_id', 'factura_id');
    }
}
