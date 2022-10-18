<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class HorarioFijo extends Model
{
    use HasFactory;
    protected $table='horario_fijo';
    protected $primaryKey = 'horario_id';
    public $timestamps = true;
    protected $fillable = [
        'horario_hora_inicio',  
        'horario_hora_fin',
        'horario_dia',
        'horario_estado',
        'mespecialidad_id',
    ];
    protected $guarded =[
    ];
    public function scopeHorarioByEspecialidad($query, $mespecialidad_id){
        return $query->where('mespecialidad_id','=',$mespecialidad_id);
    }
    public function scopeHorarios($query)
    {
        return $query->join('medico_especialidad','medico_especialidad.mespecialidad_id','=','horario_fijo.mespecialidad_id'
                    )->join('especialidad','especialidad.especialidad_id','=','medico_especialidad.especialidad_id'
                    )->join('medico','medico.medico_id','=','medico_especialidad.medico_id'
                    )->where('medico.empresa_id', '=', Auth::user()->empresa_id)->orderBy('horario_hora_inicio','asc');
    }
    public function scopeHorario($query, $id)
    {
        return $query->join('medico_especialidad','medico_especialidad.mespecialidad_id','=','horario_fijo.mespecialidad_id'
                    )->join('especialidad','especialidad.especialidad_id','=','medico_especialidad.especialidad_id'
                    )->join('medico','medico.medico_id','=','medico_especialidad.medico_id'
                    )->where('medico.empresa_id', '=', Auth::user()->empresa_id)->where('horario_fijo.horario_id', '=', $id);
    }
    public function scopeHorarioDia($query, $mespecialidad_id)
    {
        return $query->join('medico_especialidad','medico_especialidad.mespecialidad_id','=','horario_fijo.mespecialidad_id'
                    )->join('medico','medico.medico_id','=','medico_especialidad.medico_id'
                    )->join('especialidad','especialidad.especialidad_id','=','medico_especialidad.especialidad_id'
                    )->where('medico.empresa_id', '=', Auth::user()->empresa_id
                    )->where('horario_fijo.mespecialidad_id', '=', $mespecialidad_id)->orderBy('horario_id','asc');//->orderBy('horario_hora_inicio','asc');
    }    
}
