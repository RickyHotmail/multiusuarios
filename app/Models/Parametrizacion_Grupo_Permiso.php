<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametrizacion_Grupo_Permiso extends Model{
    use HasFactory;
    protected $table='parametrizacion_grupo_permiso';
    protected $primaryKey='parametrizaciong_id';
    public $timestamps=true;
    protected $fillable=[
        'parametrizaciong_nombre',
        'parametrizaciong_estado'
    ];


    protected $guarded=[
    ];

    public function scopeGrupos($query){
        return $query->where('parametrizaciong_estado','=',1);
    }

    public function grupo(){
        return $this->hasMany(Parametrizacion_Permiso::class, 'parametrizaciong_id', 'parametrizaciong_id');
    }
}