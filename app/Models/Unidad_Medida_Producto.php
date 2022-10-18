<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Unidad_Medida_Producto extends Model
{
    use HasFactory;
    protected $table='unidad_medida_producto';
    protected $primaryKey = 'unidad_medida_id';
    public $timestamps = true;
    protected $fillable = [        
        'unidad_medida_nombre',                     
        'empresa_id',
        'unidad_medida_estado',         
    ];
    protected $guarded =[
    ];   
    public function scopeUnidadByName($query, $nombre){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('unidad_medida_nombre','=',$nombre);
    }
    public function scopeUnidadMedidas($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('unidad_medida_estado','=','1')->orderBy('unidad_medida_nombre','asc');
    }
    public function scopeUnidadMedida($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('unidad_medida_id','=',$id);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
