<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Parametrizacion_Permiso extends Model
{
    use HasFactory;
    protected $table='parametrizacion_permiso';
    protected $primaryKey = 'parametrizacionp_id';
    public $timestamps=true;
    protected $fillable = [
        'parametrizaciong_id',
        'permiso_id',
        'parametrizacionp_estado'
    ];

    protected $guarded =[
    ];

    public function scopeByPermisoId($query, $id){
        return $query->where('permiso_id', '=', $id);
    }

    public function scopeParametrizacionesPermiso($query, $id){
        return $query->join('permiso', 'permiso.permiso_id', '=', 'parametrizacion_permiso.permiso_id')        
                     ->orderBy('permiso.grupo_id')
                     ->orderBy('permiso.tipo_id')
                     ->where('parametrizacionp_estado', '=', 1)
                     ->where('parametrizaciong_id', '=', $id)
                     ->orderBy('permiso.permiso_nombre','desc')
                     ->select(DB::raw('parametrizacion_permiso.*'));
    }

    public function scopeGrupos($query){
        return $query->join('permiso', 'permiso.permiso_id', '=', 'parametrizacion_permiso.permiso_id')
                     ->where('parametrizacionp_estado', '=', 1);
    }

    public function permiso(){
        return $this->hasOne(Permiso::class, 'permiso_id', 'permiso_id');
    }

    public function scopeByTipo($query, $pg_id){
        $query->join('permiso', 'permiso.permiso_id', '=', 'parametrizacion_permiso.permiso_id')
              ->where('parametrizacionp_estado', 1)
              ->where('parametrizaciong_id','=', $pg_id);

        return $query->select('permiso.permiso_id', 'permiso.permiso_nombre', 'permiso.permiso_ruta', 'permiso.permiso_tipo','permiso.permiso_icono','permiso.permiso_orden','permiso.grupo_id', 'permiso.tipo_id');
    }
}
