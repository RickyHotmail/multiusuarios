<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_LC extends Model
{
    use HasFactory;
    protected $table='detalle_lc';
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
        'lc_id',
        'producto_id',
        'bodega_id',
        'centro_consumo_id',
        'movimiento_id',
    ];
    protected $guarded =[
    ];
    public function liquidacionCompra(){
        return $this->belongsTo(Liquidacion_Compra::class, 'lc_id', 'lc_id');
    }
    public function centroConsumo(){
        return $this->belongsTo(Centro_Consumo::class, 'centro_consumo_id', 'centro_consumo_id');
    }
    public function movimiento(){
        return $this->belongsTo(Movimiento_Producto::class, 'movimiento_id', 'movimiento_id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function bodega(){
        return $this->belongsTo(Bodega::class, 'bodega_id', 'bodega_id');
    }
   
}
