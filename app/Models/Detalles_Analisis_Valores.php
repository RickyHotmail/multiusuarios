<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalles_Analisis_Valores extends Model
{
    use HasFactory;
    protected $table='detalle_analisis_valores';
    protected $primaryKey = 'detalle_valores_id';
    public $timestamps=true;
    protected $fillable = [    
        'detalle_id',
        'resultado',
        'unidad_medida',
        'id_externo_parametro',
        'nombre_parametro',
        'valor_minimo',
        'valor_maximo',
        'valor_normal',
        'interpretacion',
        'comentario'
        
    ];
    protected $guarded =[
    ];
    public function detalles(){
        return $this->hasMany(Detalles_Analisis_Referenciales::class, 'detalle_valores_id', 'detalle_valores_id');
    }
    public function scopeAnalisis($query,$id,$detalle){
        return $query->where('detalle_descripcion','=',$detalle)->where('detalle_id','=',$id);
    }
}
