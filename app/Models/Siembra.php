<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Siembra extends Model
{
    use HasFactory;
    protected $table='siembra';
    protected $primaryKey = 'siembra_id';
    public $timestamps = true;
    protected $fillable = [
        'siembra_secuencial',        
        'siembra_codigo',
        'siembra_larvas',   
        'siembra_entregas',              
        'siembra_fecha',
        'siembra_fecha_costo',  
        'siembra_fecha_siembra',
        'siembra_longitud',
        'siembra_peso',
        'siembra_costo_inicial',
        'siembra_costo',
        'siembra_densidad',    
        'siembra_cultivo',              
        'siembra_precio_larva', 
        'siembra_estado',      
        'siembra_ref_id',        
        'piscina_id',
        'nauplio_id',
        'laboratorio_id',
    ];
    protected $guarded =[
    ];   
    public function scopeSiembras($query){
        return $query->join('piscina','siembra.piscina_id','=','piscina.piscina_id')->join('camaronera','camaronera.camaronera_id','=','piscina.camaronera_id')->where('camaronera.empresa_id','=',Auth::user()->empresa_id)->where('siembra_estado','=','1')->orderBy('siembra_codigo','asc');
    }
    public function scopeSiembrasActiva($query){
        return $query->join('piscina','siembra.piscina_id','=','piscina.piscina_id')->join('camaronera','camaronera.camaronera_id','=','piscina.camaronera_id')->where('camaronera.empresa_id','=',Auth::user()->empresa_id)->where('siembra_estado','=','1')->orderBy('siembra_codigo','asc');
    }
    public function scopeSiembra($query, $id){
        return $query->join('nauplio','nauplio.nauplio_id','=','siembra.nauplio_id')->join('laboratorio_camaronera','laboratorio_camaronera.laboratorio_id','=','nauplio.laboratorio_id')->join('piscina','siembra.piscina_id','=','piscina.piscina_id')->join('camaronera','camaronera.camaronera_id','=','piscina.camaronera_id')->where('camaronera.empresa_id','=',Auth::user()->empresa_id)->where('siembra_id','=',$id);
    }
    public function scopePiscinasActiva($query, $id){
        return $query->join('piscina','siembra.piscina_id','=','piscina.piscina_id')->join('camaronera','camaronera.camaronera_id','=','piscina.camaronera_id')->where('camaronera.empresa_id','=',Auth::user()->empresa_id)->where('piscina_tipo_estado','!=','EN PRODUCCIÓN')->where('piscina.piscina_id','=',$id);
    }
    public function scopePiscinas($query, $id){
        return $query->join('piscina','siembra.piscina_id','=','piscina.piscina_id')->join('camaronera','camaronera.camaronera_id','=','piscina.camaronera_id')->where('camaronera.empresa_id','=',Auth::user()->empresa_id)->where('piscina.piscina_id','=',$id);
    }
    public function transferencias(){
        return $this->hasMany(Transferencia_Siembra::class, 'piscina_id', 'piscina_id');
    }
    public function piscina(){
        return $this->belongsTo(Piscina::class, 'piscina_id', 'piscina_id');
    }
    public function siembrapadre(){
        return $this->belongsTo(Siembra::class, 'siembra_ref_id', 'piscina_id');
    }
    public function nauplio(){
        return $this->belongsTo(Nauplio::class, 'nauplio_id', 'nauplio_id');
    }
   
}
