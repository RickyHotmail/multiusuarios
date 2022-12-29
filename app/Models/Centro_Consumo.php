<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Centro_Consumo extends Model
{
    use HasFactory;
    protected $table='centro_consumo';
    protected $primaryKey = 'centro_consumo_id';
    public $timestamps = true;
    protected $fillable = [        
        'centro_consumo_nombre',
        'centro_consumo_descripcion',       
        'centro_consumo_fecha_ingreso',    
        'sustento_id',                     
        'empresa_id',
        'centro_consumo_padre',  
        'centro_consumo_nivel',    
        'centro_consumo_numero', 
        'centro_consumo_secuencial',    
        'centro_consumo_estado',           
    ];
    protected $guarded =[
    ];   
    public function scopeCentroConsumos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('centro_consumo_estado','=','1')->orderBy('centro_consumo_numero','asc');
    }  
    public function scopeCentroConsumo($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('centro_consumo_id','=',$id);
    }
    public function scopeCentroConsumoxSustento($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('sustento_id','=',$id)->where('centro_consumo_estado','=','1');
    }
    public function detallesTC(){
        return $this->hasMany(Detalle_TC::class, 'centro_consumo_id', 'centro_consumo_id');
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function sustento(){
        return $this->belongsTo(Sustento_Tributario::class, 'sustento_id', 'sustento_id');
    }
    public function cuentapadre(){
        return $this->belongsTo(Centro_Consumo::class, 'centro_consumo_padre', 'centro_consumo_id');
    }
    public function scopeNivelPadre($query, $cuentaPadre){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('centro_consumo_numero','=',$cuentaPadre);
    }
    public function scopeNivel($query, $cuentaPadre){
        if ($cuentaPadre == 0){
            return $query->where(function ($query){
                $query->whereNull('empresa_id'
                     )->orwhere('empresa_id','=',Auth::user()->empresa_id);
                    })->whereNull('centro_consumo_padre');
        }else{
            return $query->where(function ($query){
                $query->whereNull('empresa_id'
                     )->orwhere('empresa_id','=',Auth::user()->empresa_id);
                    })->where('centro_consumo_padre','=',$cuentaPadre);
        }
    }
}
