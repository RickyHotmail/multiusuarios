<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Medico_Aseguradora extends Model
{
    use HasFactory;
    protected $table='medico_aseguradora';
    protected $primaryKey = 'aseguradoraM_id';
    public $timestamps = true;
    protected $fillable = [        
        'aseguradoraM_estado',
        'medico_id',
        'cliente_id',
    ];
    protected $guarded =[
    ];
    public function scopeMedicosAseguradora($query)
    {
        return $query->join('medico', 'medico.medico_id','=','medico_aseguradora.medico_id')->where('medico.empresa_id','=',Auth::user()->empresa_id)->where('aseguradoraM_estado','=','1');
    }
    public function scopeMedicoAseguradora($query, $id)
    {
        return $query->join('medico', 'medico.medico_id','=','medico_aseguradora.medico_id')->where('medico.empresa_id','=',Auth::user()->empresa_id)->where('medico_aseguradora.medico_id', '=', $id);
    }
}
