<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Arqueo_Caja extends Model
{
    use HasFactory;
    protected $table='arqueo_caja';
    protected $primaryKey = 'arqueo_id';
    public $timestamps = true;
    protected $fillable = [        
        'arqueo_fecha',
        'arqueo_hora',         
        'arqueo_observacion',
        'arqueo_tipo',
        'arqueo_saldo_inicial',
        'arqueo_monto',
        'arqueo_billete1',
        'arqueo_billete5',
        'arqueo_billete10',
        'arqueo_billete20',
        'arqueo_billete50',
        'arqueo_billete100',
        'arqueo_moneda01',
        'arqueo_moneda05',
        'arqueo_moneda10',
        'arqueo_moneda25',
        'arqueo_moneda50',
        'arqueo_moneda1',
        'arqueo_estado',
        'empresa_id',
        'caja_id',
        'user_id',
        'cierre_id',            
    ];
    protected $guarded =[
    ];
    public function scopeArqueoCajas($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('arqueo_estado','=','1')->orderBy('arqueo_fecha','asc');
    }
    public function scopeCierreCajas($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('arqueo_estado','=','1')->where('cierre_id','=', null)->where('arqueo_tipo','=', 'CIERRE')->orderBy('arqueo_fecha','desc');
    }
    public function scopeCajasAbiertas($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('arqueo_estado','=','1')->where('cierre_id','=', null)->where('arqueo_tipo','=', 'APERTURA');
          
    }
    public function scopeCajasAbiertasxSucursal($query){
        return $query->join('caja','caja.caja_id','=','arqueo_caja.caja_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('arqueo_estado','=','1')->where('cierre_id','=', null)->where('arqueo_tipo','=', 'APERTURA');
          
    }
    public function scopeArqueoCaja($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('user_id','=',$id)->where('arqueo_estado','=','1')->where('cierre_id','=', null)->where('arqueo_tipo','=', 'APERTURA');
          
    }
    public function scopeArqueoCajaxCierre($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('cierre_id','=',$id)->where('arqueo_estado','=','1')->where('arqueo_tipo','=', 'APERTURA');
          
    }
    public function scopeCierrecaja($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('arqueo_id','=',$id)->where('arqueo_estado','=','1')->where('cierre_id','=', null)->where('arqueo_tipo','=', 'CIERRE');
          
    }
    public function scopeArqueoCajaxid($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('arqueo_id','=',$id)->where('arqueo_estado','=','1')->where('cierre_id','=', null)->where('arqueo_tipo','=', 'APERTURA');
          
    }
    public function scopeArqueoCierre($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('cierre_id','=',$id)->where('arqueo_estado','=','1')->where('arqueo_tipo','=', 'APERTURA');
          
    }
    public function scopeArqueoCajaxcaja($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('caja_id','=',$id)->where('arqueo_estado','=','1')->where('cierre_id','=', null)->where('arqueo_tipo','=', 'APERTURA');
          
    }
    public function scopeArqueoCajaxuser($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('user_id','=',$id)->where('arqueo_estado','=','1')->where('cierre_id','=', null)->where('arqueo_tipo','=', 'APERTURA');
          
    }
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function caja(){
        return $this->belongsTo(Caja::class, 'caja_id', 'caja_id');
    }
    public function usuario(){
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function cierre(){
        return $this->belongsTo(Arqueo_Caja::class, 'cierre_id', 'arqueo_id');
    }   
}
