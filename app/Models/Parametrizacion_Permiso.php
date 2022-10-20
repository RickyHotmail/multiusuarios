<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametrizacion_Permiso extends Model
{
    use HasFactory;
    protected $table='parametrizacion_permiso';
    protected $primaryKey = 'parametrizacionp_id';
    public $timestamps=true;
    protected $fillable = [
        'permiso_id',
        'parametrizacionp_general',
        'parametrizacionp_medico',
        'parametrizacionp_camaronero',
        'parametrizacionp_facturacion',
        'parametrizacionp_estado'
    ];

    protected $guarded =[
    ];

    public function scopeParametrizacionesPermiso($query){
        return $query->where('parametrizacionp_estado', '=', 1);
    }

    public function permiso(){
        return $this->hasOne(Permiso::class, 'permiso_id', 'permiso_id');
    }



    public function scopeByTipo($query, $nombre){
        $query->join('permiso', 'permiso.permiso_id', '=', 'parametrizacion_permiso.permiso_id')
              ->where('parametrizacionp_estado', 1);

        if($nombre=='GENERAL') $query->where('parametrizacion_permiso.parametrizacionp_general', 1);
        if($nombre=='FACTURACION') $query->where('parametrizacion_permiso.parametrizacionp_facturacion', 1);

        return $query->select('permiso.permiso_id', 'permiso.permiso_nombre', 'permiso.permiso_ruta', 'permiso.permiso_tipo','permiso.permiso_icono','permiso.permiso_orden','permiso.grupo_id');
    }
}
