<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $table='plan';
    protected $primaryKey='plan_id';

    protected $fillable=[
        'plan_nombre',
        'plan_cantidad_documentos',
        'plan_tiempo',
        'plan_precio',
        'plan_estado'
    ];

    protected $guarded=[
    ];


    public function scopePlanes($query){
        return $query->where('plan_estado', '=', 1);
    }

    public function scopeByNombre($query, $nombre){
        return $query->where('plan_estado', '=', 1)
                     ->where('plan_nombre', '=', $nombre);
    }
}
