<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto_Movil extends Model
{
    use HasFactory;
    protected $table="producto";
    protected $primaryKey="producto_id";
    public $timestamps=true;
    protected $fillable=[
        'producto_codigo_principal',
        'producto_codigo_auxiliar',
        'producto_tipo',
        'producto_nombre',
        'producto_valor_unitario',
        'producto_grava_iva',
        'producto_grava_ice',
        'producto_grava_irbpnr',
        'producto_atributo1',
        'producto_descripcion1',
        'producto_atributo2',
        'producto_descripcion2',
        'producto_atributo3',
        'producto_descripcion3',
        'producto_estado'
    ];

    protected $guarded=[
    ];
}
