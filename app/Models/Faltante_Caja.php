<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Faltante_Caja extends Model
{
    use HasFactory;
    protected $table='faltante_caja';
    protected $primaryKey = 'faltante_id';
    public $timestamps=true;
    protected $fillable = [
        'faltante_numero',
        'faltante_serie',
        'faltante_secuencial',
        'faltante_fecha', 
        'faltante_observacion',
        'faltante_estado',
        'arqueo_id', 
        'diario_id',
        'rango_id',
                    
    ];
    protected $guarded =[
    ];

    public function scopeFaltantes($query){
        return $query->join('arqueo_caja','arqueo_caja.arqueo_id','=','faltante_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->orderBy('faltante_fecha','asc');
    }    
    public function scopeFaltante($query, $id){
        return $query->join('arqueo_caja','arqueo_caja.arqueo_id','=','faltante_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('faltante_id','=',$id);
    }
    public function scopeFaltantexArqueo($query, $id){
        return $query->join('arqueo_caja','arqueo_caja.arqueo_id','=','faltante_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('faltante_caja.arqueo_id','=',$id);
    }
    public function scopeFaltantexArqueoSuma($query, $id){
        return $query->select(DB::raw('SUM(faltante_monto) as sumaFaltante'))->join('arqueo_caja','arqueo_caja.arqueo_id','=','faltante_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('faltante_caja.arqueo_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->join('arqueo_caja','arqueo_caja.arqueo_id','=','faltante_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('rango_id','=',$id);

    }
    public function arqueo()
    {
        return $this->belongsTo(Arqueo_Caja::class, 'arqueo_id', 'arqueo_id');
    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
}
