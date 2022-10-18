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
        'centro_consumo_estado',         
    ];
    protected $guarded =[
    ];   
    public function scopeCentroConsumos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('centro_consumo_estado','=','1')->orderBy('centro_consumo_nombre','asc');
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
}
