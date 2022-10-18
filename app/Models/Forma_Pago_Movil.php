<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forma_Pago_Movil extends Model
{
    use HasFactory;
    protected $table="forma_pago_movil";
    protected $primaryKey="formap_id";
    public $timestamps=true;
    protected $fillable=[
        'formap_valor',
        'formap_tiempo',
        'formap_plazo',
        'formap_estado',
        'factura_id',
        'tipo_id'
    ];

    protected $guarded=[
    ];
}
