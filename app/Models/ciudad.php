<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Ciudad extends Model
{
    use HasFactory;
    protected $table='ciudad';
    protected $primaryKey = 'ciudad_id';
    public $timestamps=true;
    protected $fillable = [
        'ciudad_nombre',
        'ciudad_codigo',       
        'ciudad_estado',        
        'provincia_id',           
    ];
    protected $guarded =[
    ];
    public function scopeCiudades($query){
        return $query->join('provincia','provincia.provincia_id','=','ciudad.provincia_id')->join('pais','pais.pais_id','=','provincia.pais_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('ciudad_estado','=','1')->orderBy('ciudad_nombre','asc');
    }
    public function scopeCiudad($query, $id){
        return $query->join('provincia','provincia.provincia_id','=','ciudad.provincia_id')->join('pais','pais.pais_id','=','provincia.pais_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('ciudad_nombre','=',$id);
    }
    public function scopeExiste($query, $id){
        return $query->join('provincia','provincia.provincia_id','=','ciudad.provincia_id')->join('pais','pais.pais_id','=','provincia.pais_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('ciudad_id','=',$id);
    }
    public function scopeCiudadNombre($query, $nombre){
        return $query->join('provincia','provincia.provincia_id','=','ciudad.provincia_id')->join('pais','pais.pais_id','=','provincia.pais_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('ciudad_nombre','=',$nombre);
    }
    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id', 'provincia_id');
    }

    public function scopeProvinciaCiudades($query, $id){
        return $query->join('provincia', 'provincia.provincia_id','=','ciudad.provincia_id'
                    )->join('pais', 'pais.pais_id','=','provincia.pais_id' 
                    )->where('pais.empresa_id','=',Auth::user()->empresa_id)->where('ciudad.provincia_id','=',$id)->where('ciudad.ciudad_estado','=','1');
    }
}
