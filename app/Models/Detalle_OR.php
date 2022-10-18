<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_OR extends Model
{
    use HasFactory;
    protected $table='detalle_or';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_cantidad',
        'detalle_estado',
        'ordenr_id',
        'producto_id',
        'movimiento_id',
    ];
    protected $guarded =[
    ];
    
    public function scopeDetalleFactura($query, $ordenID){
        return $query->join('producto','producto.producto_id','=','detalle_or.producto_id')->where('ordenr_id','=', $ordenID)->orderBy('detalle_id','asc');
    }
    public function ordenDespacho(){
        return $this->belongsTo(Orden_Recepcion::class, 'ordenr_id', 'ordenr_id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function movimiento(){
        return $this->belongsTo(Movimiento_Producto::class, 'movimiento_id', 'movimiento_id');
    }
}
