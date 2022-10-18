<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Egreso_Bodega extends Model
{
    use HasFactory;
    protected $table='cabecera_egreso_bodega';
    protected $primaryKey = 'cabecera_egreso_id';
    public $timestamps=true;
    protected $fillable = [
        'cabecera_egreso_numero',
        'cabecera_egreso_serie',
        'cabecera_egreso_secuencial', 
        'cabecera_egreso_fecha',
        'cabecera_egreso_destino',
        'cabecera_egreso_destinatario',
        'cabecera_egreso_motivo',
        'cabecera_egreso_total',  
        'cabecera_egreso_comentario',
        'cabecera_egreso_estado',
        'bodega_id',
        'diario_id',
        'user_id',
        'rango_id',
        'tipo_id',
    ];
    protected $guarded =[
    ];
    public function scopeEgresos($query){
        return $query->join('bodega','bodega.bodega_id','=','cabecera_egreso_bodega.bodega_id')->join('users','users.user_id','=','cabecera_egreso_bodega.user_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('users.user_id','=',Auth::user()->user_id)->where('cabecera_egreso_estado','=','1')->orderBy('cabecera_egreso_id','asc');
    }
   
   
    public function scopeEgresosDiferentes($query,$fechadesde,$fechahasta,$bodega){
        return $query->join('users','users.user_id','=','cabecera_egreso_bodega.user_id')->join('bodega','bodega.bodega_id','=','cabecera_egreso_bodega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('users.user_id','=',Auth::user()->user_id)
        ->where('cabecera_egreso_fecha', '>=', $fechadesde)
        ->where('cabecera_egreso_fecha', '<=', $fechahasta)
        ->where('bodega.bodega_id','=',$bodega)
        ->orderBy('cabecera_egreso_id','asc');
    }
    public function scopeEgresosFecha($query,$fechadesde,$fechahasta){
        return $query->join('users','users.user_id','=','cabecera_egreso_bodega.user_id')->join('bodega','bodega.bodega_id','=','cabecera_egreso_bodega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('users.user_id','=',Auth::user()->user_id)
        ->where('cabecera_egreso_fecha', '>=', $fechadesde)
        ->where('cabecera_egreso_fecha', '<=', $fechahasta)
        ->orderBy('cabecera_egreso_id','asc');
    }
    public function scopeEgresosBodega($query,$bodega){
        return $query->join('users','users.user_id','=','cabecera_egreso_bodega.user_id')->join('bodega','bodega.bodega_id','=','cabecera_egreso_bodega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('users.user_id','=',Auth::user()->user_id)
        ->where('bodega.bodega_id','=',$bodega)
        ->orderBy('cabecera_egreso_id','asc');
    }
   
    public function scopeEgreso($query, $id){
        return $query->join('users','users.user_id','=','cabecera_egreso_bodega.user_id')->join('bodega','bodega.bodega_id','=','cabecera_egreso_bodega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('users.user_id','=',Auth::user()->user_id)->where('cabecera_egreso_estado','=','1')->where('cabecera_egreso_id','=',$id);
    }
    public function scopeBodegaDistinsc($query){
        return $query->join('users','users.user_id','=','cabecera_egreso_bodega.user_id')->join('bodega','bodega.bodega_id','=','cabecera_egreso_bodega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('cabecera_egreso_bodega.user_id','=',Auth::user()->user_id)->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('bodega.bodega_nombre','asc');
    }
    public function scopeSecuencial($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','cabecera_egreso_bodega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('cabecera_egreso_bodega.rango_id','=',$id);
    }
    public function detalles(){
        return $this->hasMany(Detalle_EB::class, 'cabecera_egreso_id', 'cabecera_egreso_id');
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function usuario(){
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
    public function bodega(){
        return $this->belongsTo(Bodega::class, 'bodega_id', 'bodega_id');
    }
    public function tipo(){
        return $this->belongsTo(Tipo_MI::class, 'tipo_id', 'tipo_id');
    }

}
