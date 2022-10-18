<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Entidad_Procedimiento extends Model
{
    use HasFactory;
    protected $table='entidad_procedimiento';
    protected $primaryKey = 'ep_id';
    public $timestamps = true;
    protected $fillable = [        
        'ep_tipo',
        'ep_valor',
        'ep_estado',
        'procedimiento_id',
        'entidad_id',
    ];
    protected $guarded =[
    ];
    public function scopeEntidadProcedimientos($query)
    {
        return $query->join('entidad', 'entidad.entidad_id','=','entidad_procedimiento.entidad_id')->where('entidad.empresa_id','=',Auth::user()->empresa_id)->where('ep_estado','=','1');
    }
    public function scopeEntidadProcedimiento($query, $id)
    {
        return $query->join('entidad', 'entidad.entidad_id','=','entidad_procedimiento.entidad_id')->where('entidad.empresa_id','=',Auth::user()->empresa_id)->where('ep_id', '=', $id);
    }
    public function scopeValorAsignado($query, $procedimiento, $entidad){
        return $query->join('entidad','entidad.entidad_id','=','entidad_procedimiento.entidad_id'
                    )->join('procedimiento_especialidad','procedimiento_especialidad.procedimiento_id','=','entidad_procedimiento.procedimiento_id'
                    )->where('entidad.empresa_id','=',Auth::user()->empresa_id
                    )->where('entidad_procedimiento.procedimiento_id','=',$procedimiento
                    )->where('entidad_procedimiento.entidad_id','=',$entidad);
    }
    public function scopeValorAsignadoproducto($query, $procedimiento, $entidad,$producto){
        return $query->join('entidad','entidad.entidad_id','=','entidad_procedimiento.entidad_id'
                    )->join('procedimiento_especialidad','procedimiento_especialidad.procedimiento_id','=','entidad_procedimiento.procedimiento_id'
                    )->where('entidad.empresa_id','=',Auth::user()->empresa_id
                    )->where('entidad_procedimiento.procedimiento_id','=',$procedimiento
                    )->where('entidad_procedimiento.entidad_id','=',$entidad
                    )->where('procedimiento_especialidad.producto_id','=',$producto);
    }
    public function procedimiento()
    {
        return $this->belongsTo(Procedimiento_Especialidad::class, 'procedimiento_id', 'procedimiento_id');
    } 

}
