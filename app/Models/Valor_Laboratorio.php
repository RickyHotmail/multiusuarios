<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Valor_Laboratorio extends Model
{
    use HasFactory;
    protected $table ='valor_laboratorio';
    protected $primaryKey = 'valor_id';
    public $timestamps=true;
    protected $fillable = [   
        'valor_nombre',     
        'valor_estado',
        'detalle_id',        
    ];
    protected $guarded =[
    ];    
    
    public function scopeValorLaboratorios($query){
        return $query->join('detalle_laboratorio','detalle_laboratorio.detalle_id','=','valor_laboratorio.detalle_id'
                    )->join('examen','examen.examen_id','=','detalle_laboratorio.examen_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->where('tipo_examen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('valor_laboratorio.valor_estado','=','1')->orderBy('valor_nombre','asc');
    }
    public function scopeValorLaboratorio($query, $id){
        return $query->join('detalle_laboratorio','detalle_laboratorio.detalle_id','=','valor_laboratorio.detalle_id'
                    )->join('examen','examen.examen_id','=','detalle_laboratorio.examen_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->where('tipo_examen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('valor_laboratorio.valor_id','=',$id);
    }
    public function scopeValorLaboratorioexamen($query, $id){
        return $query->join('detalle_laboratorio','detalle_laboratorio.detalle_id','=','valor_laboratorio.detalle_id'
                    )->join('examen','examen.examen_id','=','detalle_laboratorio.examen_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->where('tipo_examen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('valor_laboratorio.detalle_id','=',$id);
    }
    public function scopeValorLaboratoriodetalle($query, $id){
        return $query->join('detalle_laboratorio','detalle_laboratorio.detalle_id','=','valor_laboratorio.detalle_id'
                    )->join('examen','examen.examen_id','=','detalle_laboratorio.examen_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->where('tipo_examen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('valor_laboratorio.detalle_id','=',$id);
    }
    public function detallelaboratorio()
    {
        return $this->belongsTo(Detalle_Laboratorio::class, 'detalle_id', 'detalle_id');
    }
    
}
