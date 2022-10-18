<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalle_Mantenimiento extends Model
{
    use HasFactory;
    protected $table="detalle_mantenimiento";
    protected $primaryKey="detalle_id";
    public $timestamps=true;
    protected $fillable=[
        'detalle_fecha_inicio',
        'detalle_fecha_fin',
        'detalle_descripcion',
        'detalle_estado',
        'orden_id'
    ];

    protected $guarded=[
    ];

    public function scopeDetalles($query, $id){
        return $query->join('orden_mantenimiento', 'orden_mantenimiento.orden_id', '=', 'detalle_mantenimiento.orden_id'
            )->join('sucursal', 'sucursal.sucursal_id', '=', 'orden_mantenimiento.sucursal_id'
            )->where('sucursal.empresa_id', '=', Auth::user()->empresa_id
            )->where('orden_mantenimiento.orden_id','=', $id);
    }
}
