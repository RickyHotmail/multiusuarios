<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Medico_Especialidad extends Model
{
    use HasFactory;
    protected $table='medico_especialidad';
    protected $primaryKey = 'mespecialidad_id';
    public $timestamps = true;
    protected $fillable = [        
        'especialidad_id',
        'medico_id',
    ];
    protected $guarded =[
    ];
    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id', 'especialidad_id');
    }
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id', 'medico_id');
    }
    public function horarios()
    {
        return $this->hasMany(HorarioFijo::class, 'mespecialidad_id', 'mespecialidad_id');
    }
    public function scopeMespecialidades($query)
    {
        return $query->join('especialidad','especialidad.especialidad_id','=','medico_especialidad.especialidad_id')->join('medico','medico.medico_id','=','medico_especialidad.medico_id')->where('medico.empresa_id', '=', Auth::user()->empresa_id);
    }
    public function scopeMespecialidad($query, $id)
    {
        return $query->join('especialidad','especialidad.especialidad_id','=','medico_especialidad.especialidad_id')->join('medico','medico.medico_id','=','medico_especialidad.medico_id')->where('medico.empresa_id', '=', Auth::user()->empresa_id)->where('medico_especialidad.mespecialidad_id', '=', $id);
    }  
    public function scopeMespecialidadM($query, $id)
    {
        return $query->join('especialidad', 'especialidad.especialidad_id', '=', 'medico_especialidad.especialidad_id')->join('medico', 'medico.medico_id', '=', 'medico_especialidad.medico_id')->where('medico.empresa_id', '=', Auth::user()->empresa_id)->where('medico_especialidad.medico_id', '=', $id);
    }
    public function scopeMedicosEspecialidad($query, $buscar)
    {
        return $query->join('medico','medico.medico_id','=','medico_especialidad.medico_id'
                    )->where('medico.empresa_id', '=', Auth::user()->empresa_id
                    )->where('medico_especialidad.especialidad_id', '=', $buscar);
    }
}
