<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class Detalle_OD extends Model
{
    use HasFactory;
    protected $table='detalle_orden';
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
        'orden_id',
        'producto_id',
        'movimiento_id',
        'movimientop_id',
    ];
    protected $guarded =[
    ];
    
    public function scopeDetalleFactura($query, $ordenID){
        return $query->join('producto','producto.producto_id','=','detalle_orden.producto_id')->where('orden_id','=', $ordenID)->orderBy('detalle_id','asc');
    }
    public function ordenDespacho(){
        return $this->belongsTo(Orden_Despacho::class, 'orden_id', 'orden_id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function movimiento(){
        return $this->belongsTo(Movimiento_Producto::class, 'movimiento_id', 'movimiento_id');
    }
    public function movimientop(){
        return $this->belongsTo(Movimiento_Prestamo_Producto::class, 'movimientop_id', 'movimiento_id');
    }
}
