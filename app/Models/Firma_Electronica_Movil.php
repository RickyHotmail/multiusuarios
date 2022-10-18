<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Firma_Electronica_Movil extends Model
{
    use HasFactory;
    protected $table="firma_electronica_movil";
    protected $primaryKey="firmae_id";
    public $timestamps=true;
    protected $fillable=[
        'firmae_fecha',
        'firmae_archivo',
        'firmae_disponibilidad',
        'firmae_ambiente',
        'firmae_estado',
        'cliente_id'
    ];

    protected $guarded=[
    ];
}
