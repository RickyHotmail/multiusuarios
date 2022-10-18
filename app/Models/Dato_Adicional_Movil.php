<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dato_Adicional_Movil extends Model
{
    use HasFactory;
    protected $table="dato_adicional_movil";
    protected $primaryKey="datoa_id";

    public $timestamps=true;
    
    protected $fillable=[
        'datoa_nombre',
        'datoa_descripcion',
        'datoa_estado',
        'factura_id'
    ];

    protected $guarded=[
    ];
}
