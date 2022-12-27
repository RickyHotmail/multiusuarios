<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Centro_Consumo2 extends Model{
    use HasFactory;
    protected $table='centro_consumo2';
    protected $primaryKey = 'centroc2_id';
    public $timestamps = true;
    protected $fillable = [
        'centroc2_nombre',
        'centroc2_descripcion',
        'centroc2_fecha_ingreso',
        'sustento_id',
        'centroc2_secuencial',
        'centroc2_nivel',
        'centroc2_padre_id',
        'empresa_id',
        'centroc2_estado',
    ];

    public function scopeCentroConsumos($query){
        return $query->where('empresa_id', '=', Auth::user()->empresa_id
                    )->where('centroc2_estado', '=', 1
                    )->orderBy('centroc2_secuencial');
    }

    public function scopeNivel($query, $cuentaPadre){
        if ($cuentaPadre == 0){
            return $query->where(function ($query){
                $query->whereNull('empresa_id'
                     )->orwhere('empresa_id','=',Auth::user()->empresa_id);
                    })->whereNull('centroc2_padre_id');
        }else{
            return $query->where(function ($query){
                $query->whereNull('empresa_id'
                     )->orwhere('empresa_id','=',Auth::user()->empresa_id);
                    })->where('centroc2_padre_id','=',$cuentaPadre);
        }
    }
}
