<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Piscina extends Model
{
    use HasFactory;
    protected $table='piscina';
    protected $primaryKey = 'piscina_id';
    public $timestamps=true;
    protected $fillable = [
        'piscina_codigo',
        'piscina_nombre',  
        'piscina_largo',    
        'piscina_tipo',    
        'piscina_secuencial',        
        'piscina_ancho',  
        'piscina_columna_agua',
        'piscina_espejo_agua',
        'piscina_volumen_agua',  
        'piscina_declinacion',  
        'piscina_entrada_agua',      
        'piscina_salida_agua',  
        'piscina_tipo_estado',
        'piscina_estado',
        'camaronera_id',
        
    ];
    protected $guarded =[
    ];
    public function scopePiscinas($query){
        return $query->join('camaronera','camaronera.camaronera_id','=','piscina.camaronera_id')->where('camaronera.empresa_id','=',Auth::user()->empresa_id)->where('piscina_estado','=','1')->orderBy('piscina_nombre','asc');
    }
    public function scopePiscina($query, $id){
        return $query->join('camaronera','camaronera.camaronera_id','=','piscina.camaronera_id')->where('camaronera.empresa_id','=',Auth::user()->empresa_id)->where('piscina_id','=',$id);
    } 
    public function scopePiscinasActiva($query){
        return $query->join('camaronera','camaronera.camaronera_id','=','piscina.camaronera_id')->where('camaronera.empresa_id','=',Auth::user()->empresa_id)->where('piscina_estado','=','1')->where('piscina_tipo_estado','!=','EN PRODUCCIÓN')->orderBy('piscina_nombre','asc');
    }
    
    public function scopetipoPiscinas($query, $id){
        return $query->join('camaronera','camaronera.camaronera_id','=','piscina.camaronera_id')->where('camaronera.empresa_id','=',Auth::user()->empresa_id)->where('piscina_tipo','=',$id);
    }
    public function tipopiscina()
    {
        return $this->belongsTo(Tipo_Piscina::class, 'tipo_id', 'tipo_id');
    }
    public function camaronera()
    {
        return $this->belongsTo(Camaronera::class, 'camaronera_id', 'camaronera_id');
    }
}
