<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo_Detalle_Consulta extends Model
{
    use HasFactory;
    protected $table="tipo_detalle_consulta";
    protected $primaryKey='tipod_id';
    public $timestamps=true;

    protected $fillable=[
        'tipod_id',
        'tipod_descripcion',
        'tipod_estado'
    ];

    protected $guarded=[
    ];


    public function scopeTiposDetalles($query){
        return $query;
    }
}
