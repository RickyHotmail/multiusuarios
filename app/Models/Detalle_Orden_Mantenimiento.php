<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_Orden_Mantenimiento extends Model
{
    use HasFactory;
    protected $table="detalle_orden_mantenimiento";
    protected $primaryKey="detalle_orden_id";
    public $timestamps=true;
    protected $fillable=[
        'detalle_orden_cantidad',
        'producto_id',
        'detalle_orden_estado',
        'orden_id'
    ];

    protected $guarded=[
    ];

    public function producto(){
        return $this->hasOne(Producto::class, 'producto_id', 'producto_id');
    }

    public function scopeDetallesOrden($query, $id){
        return $query->join('orden_mantenimiento', 'orden_mantenimiento.orden_id', '=', 'detalle_mantenimiento.orden_id'
            )->join('sucursal', 'sucursal.sucursal_id', '=', 'orden_mantenimiento.sucursal_id'
            //)->where('sucursal.empresa_id', '=', Auth::user()->empresa_id
            )->where('sucursal.empresa_id', '=', 1
            )->where('orden_mantenimiento.orden_id','=', $id);
    }
}
