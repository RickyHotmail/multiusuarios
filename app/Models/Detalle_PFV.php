<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_PFV extends Model
{
    use HasFactory;
    protected $table='detalle_pfv';
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
        'gr_id',
        'prefactura_id',
        'producto_id',
    ];
    protected $guarded =[
    ];
    public function scopeDetalleFactura($query, $facturaID){
        return $query->join('producto','producto.producto_id','=','detalle_pfv.producto_id')->where('prefactura_id','=', $facturaID)->orderBy('detalle_id','asc');
    }
    public function scopeDetalleFacturaTotal($query, $facturaID){
        return $query->join('prefactura_venta','prefactura_venta.prefactura_id','=','detalle_pfv.prefactura_id')->join('producto','producto.producto_id','=','detalle_pfv.producto_id')->where('detalle_pfv.prefactura_id','=', $facturaID)->orderBy('detalle_id','asc');
    }
    public function scopeDetalleFacturaSuma($query, $facturaID){
        return $query->join('producto','producto.producto_id','=','detalle_pfv.producto_id')->where('prefactura_id','=', $facturaID)->orderBy('detalle_id','asc');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function prefacturaVenta()
    {
        return $this->belongsTo(Prefactura_Venta::class, 'prefactura_id', 'prefactura_id');
    }
    public function guia()
    {
        return $this->belongsTo(Guia_Remision::class, 'gr_id', 'gr_id');
    }
}
