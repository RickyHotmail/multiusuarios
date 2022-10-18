<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Valor_Referencial extends Model
{
    use HasFactory;
    protected $table ='valor_referencial';
    protected $primaryKey = 'valor_id';
    public $timestamps=true;
    protected $fillable = [   
        'valor_Columna1',
        'valor_Columna2',  
        'valor_estado',
        'detalle_id',        
    ];
    protected $guarded =[
    ];    
    
    public function scopeValorReferenciales($query){
        return $query->join('detalle_laboratorio','detalle_laboratorio.detalle_id','=','valor_referencial.detalle_id'
                    )->join('examen','examen.examen_id','=','detalle_laboratorio.examen_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->where('tipo_examen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('valor_referencial.valor_estado','=','1')->orderBy('valor_nombre','asc');
    }
    public function scopeValorReferencial($query, $id){
        return $query->join('detalle_laboratorio','detalle_laboratorio.detalle_id','=','valor_referencial.detalle_id'
                    )->join('examen','examen.examen_id','=','detalle_laboratorio.examen_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->where('tipo_examen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('valor_referencial.valor_id','=',$id);
    }
    public function scopeValorReferencialdetalle($query, $id){
        return $query->join('detalle_laboratorio','detalle_laboratorio.detalle_id','=','valor_referencial.detalle_id'
                    )->join('examen','examen.examen_id','=','detalle_laboratorio.examen_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->where('tipo_examen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('valor_referencial.detalle_id','=',$id);
    }
    public function scopeReferencialdetalle($query, $id){
        return $query->where('valor_referencial.detalle_id','=',$id);
    }
    public function detallelaboratorio()
    {
        return $this->belongsTo(Detalle_Laboratorio::class, 'detalle_id', 'detalle_id');
    }
}
