<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_IB extends Model
{
    use HasFactory;
    protected $table='detalle_ingreso_bodega';
    protected $primaryKey = 'detalle_ingreso_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_ingreso_cantidad',
        'detalle_ingreso_precio_unitario',       
        'detalle_ingreso_total',
        'detalle_ingreso_descripcion',
        'detalle_ingreso_estado',
        'cabecera_ingreso_id',
        'movimiento_id',
        'producto_id',
        'centro_consumo_id',
    ];
    protected $guarded =[
    ];
    public function scopeDetalleFactura($query, $ordenID){
        return $query->join('producto','producto.producto_id','=','detalle_ingreso_bodega.producto_id')->where('cabecera_ingreso_id','=', $ordenID)->orderBy('detalle_id','asc');
    }
    public function Ingresobodega(){
        return $this->belongsTo(ingreso_Bodega::class, 'cabecera_ingreso_id', 'cabecera_ingreso_id');
    }
    public function movimiento(){
        return $this->belongsTo(Movimiento_Producto::class, 'movimiento_id', 'movimiento_id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function centroconsumo(){
        return $this->belongsTo(Centro_Consumo::class, 'centro_consumo_id', 'centro_consumo_id');
    }
}
