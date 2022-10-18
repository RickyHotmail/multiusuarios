<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emisor_Movil extends Model
{
    use HasFactory;
    protected $table="emisor_movil";
    protected $primaryKey="emisor_id";
    public $timestamps=true;


    protected $fillable=[
        'emisor_ruc',
        'emisor_razon_social',
        'emisor_nombre_comercial',
        'emisor_direccion_matriz',
        'emisor_direccion_establecimiento',
        'emisor_codigo_establecimiento',
        'emisor_codigo_punto_emision',
        'emisor_contribuyente_especial_resolucion',
        'emisor_lleva_contabilidad',
        'emisor_contribuyente_rimpe',
        'emisor_agente_retencion',
        'emisor_logo',
        'emisor_tiempo_espera',
        'emisor_ambiente',
        'emisor_tipo_token',
        'emisor_estado'
    ];
    protected $guarded=[
    ];
}
