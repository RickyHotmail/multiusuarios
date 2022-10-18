<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Configuracion_Especialidad extends Model
{
    use HasFactory;
    protected $table='configuracion_especialidad';
    protected $primaryKey = 'configuracion_id';
    public $timestamps=true;
    protected $fillable = [
        'configuracion_nombre',
        'configuracion_tipo',       
        'configuracion_medida',      
        'configuracion_url',  
        'configuracion_multiple',
        'configuracion_estado',     
        'especialidad_id',      
    ];
    protected $guarded =[
    ];
    public function scopeConfiguracionEspecialidades($query){
        return $query->where('configuracion_estado','=','1')->orderBy('configuracion_nombre','asc');
    }
    
    public function scopeConfiguracionEspecialidad($query, $id){
        return $query->join('especialidad', 'configuracion_especialidad.especialidad_id', '=', 'especialidad.especialidad_id'
        )->where('especialidad.empresa_id','=',Auth::user()->empresa_id)
        ->where('configuracion_estado','=','1')
        ->where('configuracion_id','=',$id);
    }
    public function scopeConfiEspecialidades($query, $id){
        return $query->join('especialidad', 'configuracion_especialidad.especialidad_id', '=', 'especialidad.especialidad_id')
        ->where('especialidad.empresa_id','=',Auth::user()->empresa_id)
        ->where('configuracion_estado','=','1')->where('especialidad.especialidad_id','=',$id);
    }
    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id', 'especialidad_id');
    }
}
