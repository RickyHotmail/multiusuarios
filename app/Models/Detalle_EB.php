<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_EB extends Model
{
    use HasFactory;

    protected $table='detalle_egreso_bodega';
    protected $primaryKey = 'detalle_egreso_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_egreso_cantidad',
        'detalle_egreso_precio_unitario',       
        'detalle_egreso_total',
        'detalle_egreso_descripcion',
        'detalle_egreso_estado',
        'cabecera_egreso_id',
        'movimiento_id',
        'producto_id',
        'centro_consumo_id',

    ];
    protected $guarded =[
    ];
    public function scopeDetalleFactura($query, $ordenID){
        return $query->join('producto','producto.producto_id','=','detalle_egreso_bodega.producto_id')->where('cabecera_egreso_id','=', $ordenID)->orderBy('detalle_id','asc');
    }
    public function scopeDetalleDiario($query, $ordenID){
        return $query->join('producto','producto.producto_id','=','detalle_egreso_bodega.producto_id')->join('cabecera_egreso_bodega','cabecera_egreso_bodega.cabecera_egreso_id','=','detalle_egreso_bodega.cabecera_egreso_id')->join('diario','diario.diario_id','=','cabecera_egreso_bodega.diario_id')->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')->where('detalle_diario.detalle_haber','=', 0)->where('detalle_egreso_bodega.cabecera_egreso_id','=', $ordenID)->orderBy('detalle_egreso_id','asc');
    }
    public function egresobodega(){
        return $this->belongsTo(Egreso_Bodega::class, 'cabecera_egreso_id', 'cabecera_egreso_id');
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
