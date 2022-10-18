<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Provincia extends Model
{
    use HasFactory;
    protected $table='provincia';
    protected $primaryKey = 'provincia_id';
    public $timestamps=true;
    protected $fillable = [
        'provincia_nombre',
        'provincia_codigo',        
        'provincia_estado',        
        'pais_id',            
    ];
    protected $guarded =[
    ];
    public function scopeProvincias($query){
        return $query->join('pais','pais.pais_id','=','provincia.pais_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('provincia_estado','=','1')->orderBy('provincia_nombre','asc');
    }
    public function scopeExiste($query, $id){
        return $query->join('pais','pais.pais_id','=','provincia.pais_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('provincia_nombre','=',$id);
    }
    public function scopeProvincia($query, $id){
        return $query->join('pais','pais.pais_id','=','provincia.pais_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('provincia_id','=',$id);
    }
    public function scopeProvinciaNombre($query, $nombre){
        return $query->join('pais','pais.pais_id','=','provincia.pais_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('provincia_nombre','=',$nombre);
    }
    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id', 'pais_id');
    }

    public function scopePaisProvincias($query, $id){
        return $query->join('pais', 'pais.pais_id','=','provincia.pais_id' 
                    )->where('pais.empresa_id','=',Auth::user()->empresa_id)->where('provincia.pais_id','=',$id)->where('provincia.provincia_estado','=','1');
    }
}
