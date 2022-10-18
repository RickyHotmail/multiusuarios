<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Grupo_Producto extends Model
{
    use HasFactory;
    protected $table='grupo_producto';
    protected $primaryKey = 'grupo_id';
    public $timestamps = true;
    protected $fillable = [        
        'grupo_nombre',                    
        'empresa_id',
        'grupo_estado',         
    ];
    protected $guarded =[
    ];   
    public function scopeGrupoByName($query, $nombre){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('grupo_nombre','=',$nombre);
    }
    public function scopeGrupos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('grupo_estado','=','1')->orderBy('grupo_nombre','asc');
    }
    public function scopeGrupo($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('grupo_id','=',$id);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
