<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tamano_Producto extends Model
{
    use HasFactory;
    protected $table='tamano_producto';
    protected $primaryKey = 'tamano_id';
    public $timestamps = true;
    protected $fillable = [        
        'tamano_nombre',                    
        'empresa_id',
        'tamano_estado',         
    ];
    protected $guarded =[
    ];   
    public function scopeTamanoByName($query, $nombre){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tamano_nombre','=',$nombre);
    }
    public function scopeTamanos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tamano_estado','=','1')->orderBy('tamano_nombre','asc');
    }
    public function scopeTamano($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tamano_id','=',$id);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
