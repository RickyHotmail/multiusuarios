<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente_Movil extends Model
{
    use HasFactory;
    protected $table="cliente_movil";
    protected $primaryKey="cliente_id";
    public $timestamps=true;

    protected $fillable=[
        'cliente_nombre',
        'cliente_apellido',
        'cliente_tipo',
        'cliente_tipo_identificacion',
        'cliente_identificacion',
        'cliente_correo',
        'cliente_direccion',
        'cliente_telefono_convencional',
        'cliente_telefono_extesion',
        'cliente_telefono_celular',
        'cliente_estado'
    ];

    protected $guarded=[
    ];
}
