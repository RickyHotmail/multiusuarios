<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo_Pago_Movil extends Model
{
    use HasFactory;
    protected $table="tipo_pago_movil";
    protected $primaryKey="tipop_id";
    public $timestamps=true;
    protected $fillable=[
        'tipop_id',
        'tipop_codigo',
        'tipop_descripcion',
        'tipop_estado'
    ];

    protected $guarded=[
    ];
}
