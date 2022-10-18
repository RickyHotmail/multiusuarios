<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tarifa_Iva extends Model
{
    use HasFactory;
    protected $table='tarifa_iva';
    protected $primaryKey = 'tarifa_iva_id';
    public $timestamps = true;
    protected $fillable = [        
        'tarifa_iva_codigo',
        'tarifa_iva_porcentaje',  
        'empresa_id',
        'tarifa_iva_estado',         
    ];
    protected $guarded =[
    ];   
    public function scopeTarifaIvas($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tarifa_iva_estado','=','1')->orderBy('tarifa_iva_codigo','asc');
    }
    public function scopeTarifaIva($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tarifa_iva_id','=',$id);
    }
    public function scopeTarifaIvaCodigo($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tarifa_iva_codigo','=',$id);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
