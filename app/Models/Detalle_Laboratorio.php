<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalle_Laboratorio extends Model
{
    use HasFactory;
    protected $table ='detalle_laboratorio';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [   
        'detalle_nombre',     
        'detalle_medida',
        'detalle_maximo',
        'detalle_minimo',
        'detalle_abreviatura', 
        'detalle_estado',
        'examen_id',        
    ];
    protected $guarded =[
    ];    
    
    public function scopeDetalleLaboratorios($query){
        return $query->join('examen','examen.examen_id','=','detalle_laboratorio.examen_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->where('tipo_examen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('detalle_laboratorio.detalle_estado','=','1')->orderBy('detalle_nombre','asc');
    }
    public function scopeDetalleLaboratorio($query, $id){
        return $query->join('examen','examen.examen_id','=','detalle_laboratorio.examen_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->where('tipo_examen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('detalle_laboratorio.detalle_id','=',$id);
    }
    public function valorreferencial()
    {
        return $this->hasMany(Valor_Referencial::class, 'detalle_id', 'detalle_id');
    }
    public function valorlaboratorio()
    {
        return $this->hasMany(Valor_Laboratorio::class, 'detalle_id', 'detalle_id');
    }
    
}
