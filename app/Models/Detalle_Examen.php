<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalle_Examen extends Model
{
    use HasFactory;
    protected $table ='detalle_examen';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [        
        'detalle_estado',
        'orden_id',
        'examen_id',        
    ];
    protected $guarded =[
    ];    
    public function scopeDetalleExamenes($query){
        return $query->join('examen','examen.examen_id','=','detalle_examen.examen_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->where('tipo_examen.empresa_id','=',Auth::user()->empresa_id
                    )->where('detalle_examen.detalle_estado','=','1');                       
    }

    public function scopeDetalleByProducto($query, $id){
        return $query->where('examen_id','=', $id);
    }

    public function scopeDetalleExamen($query, $id){
        return $query->join('examen','examen.examen_id','=','detalle_examen.examen_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->where('tipo_examen.empresa_id','=',Auth::user()->empresa_id
                    )->where('detalle_examen.detalle_id','=',$id);
    }   
    public function ordenexamen(){
        return $this->belongsTo(Orden_Examen::class, 'orden_id', 'orden_id');
    }
    public function examen(){
        return $this->belongsTo(Examen::class, 'examen_id', 'examen_id');
    }
}
