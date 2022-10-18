<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_Factura_Movil extends Model
{
    use HasFactory;
    protected $table="detalle_factura_movil";
    protected $primaryKey="detallev_id";
    public $timestamps=true;


    protected $fillable=[
        'detallev_cantidad',
        'detallev_precio_unitario',
        'detallev_subsidio',
        'detallev_descuento',
        'detallev_ice',
        'detallev_irbpnr',
        'detallev_tarifa_especial',
        'factura_id',
        'producto_id'
    ];
    protected $guarded=[
    ];
}
