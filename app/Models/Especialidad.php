<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Especialidad extends Model
{
    use HasFactory;
    protected $table='especialidad';
    protected $primaryKey = 'especialidad_id';
    public $timestamps=true;
    protected $fillable = [
        'especialidad_codigo',
        'especialidad_nombre',
        'especialidad_tipo', 
        'especialidad_duracion',
        'especialidad_flexible',        
        'especialidad_estado',        
        'empresa_id',   
    ];
    protected $guarded =[
    ];
    public function empresa() {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function signosVitalesEspecialidad() {
        return $this->hasMany(Signos_Vitales_Especialidad::class, 'especialidad_id', 'especialidad_id');
    }
    public function configuracionEspecialidad() {
        return $this->hasMany(Configuracion_Especialidad::class, 'especialidad_id', 'especialidad_id');
    }
    public function scopeEspecialidades($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('especialidad_estado','=','1')->orderBy('especialidad_nombre','asc');
    }
    public function scopeEspecialidad($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('especialidad_id','=',$id);
    }
    public function scopeEspecialidadBuscar($query, $buscar){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)
        ->where(DB::raw('lower(especialidad_nombre)'), 'like', '%'.strtolower($buscar).'%');
    }
}
