<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Prescripcion_Medicamento extends Model
{
    use HasFactory;
    protected $table ='prescripcion_medicamento';
    protected $primaryKey = 'prescripcionm_id';
    public $timestamps=true;
    protected $fillable = [
        'prescripcionm_cantidad',        
        'prescripcionm_indicacion',
        'prescripcionm_estado',
        'prescripcion_id',
        'medicamento_id',     
        'movimiento_id',           
    ];
    protected $guarded =[
    ];    
    public function scopePrescripcionMedicamentos($query){
        return $query->join('medicamento','medicamento.medicamento_id','=','prescripcion_medicamento.medicamento_id'
                    )->join('tipo_medicamento','tipo_medicamento.tipo_id','=','medicamento.tipo_id'
                    )->where('tipo_medicamento.empresa_id','=',Auth::user()->empresa_id
                    )->where('prescripcion_medicamento.prescripcionm_estado','=','1');                           
    }
    public function scopePrescripcionMedicamento($query, $id){
        return $query->join('medicamento','medicamento.medicamento_id','=','prescripcion_medicamento.medicamento_id'
                    )->join('tipo_medicamento','tipo_medicamento.tipo_id','=','medicamento.tipo_id'
                    )->where('tipo_medicamento.empresa_id','=',Auth::user()->empresa_id
                    )->where('prescripcion_medicamento.prescripcionm_id','=',$id);
    }   
    public function movimiento()
    {
        return $this->belongsTo(Movimiento_Producto::class, 'movimiento_id', 'movimiento_id');
    }
    public function prescripcion()
    {
        return $this->belongsTo(Prescripcion::class, 'prescripcion_id', 'prescripcion_id');
    }
    public function medicamento()
    {
        return $this->hasOne(Medicamento::class, 'medicamento_id', 'medicamento_id');
    }
}
