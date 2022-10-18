<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Logistica extends Model{
    use HasFactory;
    protected $table='logistica';
    protected $primaryKey='logistica_id';
    public $timestamps=true;

    protected $fillable=[
        'logistica_nombre',
        'logisitica_descripcion',
        'logistica_estado',
        'empresa_id'
    ];

    protected $guarded=[
    ];

    public function scopeLogisticas($query){
        return $query->where('empresa_id', '=', Auth::user()->empresa_id);
    }
}