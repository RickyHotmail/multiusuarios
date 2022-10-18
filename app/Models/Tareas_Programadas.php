<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tareas_Programadas extends Model
{
    use HasFactory;

    protected $table='tarea_programada';
    protected $primaryKey = 'tarea_id';
    public $timestamps = true;
    protected $fillable = [        
        'tarea_nombre',
        'tarea_tipo_tiempo',
        'tarea_procedimiento',
        'tarea_hora_ejecucion',      
        'tarea_estado'
    ];
    protected $guarded =[

    ];

    public function scopeTareas($query){
        return $query->where('empresa_id','=', "1");
    }
}
