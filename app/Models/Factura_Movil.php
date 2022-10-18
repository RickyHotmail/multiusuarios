<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura_Movil extends Model
{
    use HasFactory;
    protected $table='factura_movil';
    protected $primaryKey='factura_id';
    public $timestamps=true;
    protected $fillable=[
        'factura_secuencia',
        'factura_serie',
        'factura_fecha',
        'factura_lugar',
        'factura_comentario',
        'factura_total_0',
        'factura_total_iva',
        'factura_descuento',
        'factura_ice',
        'factura_tarifa_especial',
        'factura_irbpnr',
        'factura_total',
        'factura_emision',
        'factura_ambiente',
        'factura_autorizacion',
        'factura_xml_respuesta_sri',
        'factura_xml_fecha',
        'factura_estado',
        'emisor_id',
        'cliente_id'
    ];

    protected $guarded=[
    ];
}
