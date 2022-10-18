<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class Ingreso_Bodega extends Model
{
    use HasFactory;
    protected $table='cabecera_ingreso_bodega';
    protected $primaryKey = 'cabecera_ingreso_id';
    public $timestamps=true;
    protected $fillable = [
        'cabecera_ingreso_numero',
        'cabecera_ingreso_serie',
        'cabecera_ingreso_secuencial', 
        'cabecera_ingreso_fecha',
        'cabecera_ingreso_motivo',
        'cabecera_ingreso_pago',
        'cabecera_ingreso_plazo',
        'cabecera_ingreso_total',  
        'cabecera_ingreso_comentario',    
        'cabecera_ingreso_estado',
        'bodega_id',
        'diario_id',
        'user_id',
        'rango_id',
        'tipo_id',
        'cuenta_id',
        'proveedor_id',
        
    ];
    protected $guarded =[
    ];
    public function scopeIngresos($query){
        return $query->join('bodega','bodega.bodega_id','=','cabecera_ingreso_bodega.bodega_id')->join('users','users.user_id','=','cabecera_ingreso_bodega.user_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('users.user_id','=',Auth::user()->user_id)->where('cabecera_ingreso_estado','=','1')->orderBy('cabecera_ingreso_id','asc');
    }
   
   
    public function scopeIngresosDiferentes($query,$fechadesde,$fechahasta,$bodega){
        return $query->join('users','users.user_id','=','cabecera_ingreso_bodega.user_id')->join('bodega','bodega.bodega_id','=','cabecera_ingreso_bodega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('users.user_id','=',Auth::user()->user_id)
        ->where('cabecera_ingreso_fecha', '>=', $fechadesde)
        ->where('cabecera_ingreso_fecha', '<=', $fechahasta)
        ->where('bodega.bodega_id','=',$bodega)
        ->orderBy('cabecera_ingreso_id','asc');
    }
    public function scopeIngresosFecha($query,$fechadesde,$fechahasta){
        return $query->join('users','users.user_id','=','cabecera_ingreso_bodega.user_id')->join('bodega','bodega.bodega_id','=','cabecera_ingreso_bodega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('users.user_id','=',Auth::user()->user_id)
        ->where('cabecera_ingreso_fecha', '>=', $fechadesde)
        ->where('cabecera_ingreso_fecha', '<=', $fechahasta)
        ->orderBy('cabecera_ingreso_id','asc');
    }
    public function scopeIngresosBodega($query,$bodega){
        return $query->join('users','users.user_id','=','cabecera_ingreso_bodega.user_id')->join('bodega','bodega.bodega_id','=','cabecera_ingreso_bodega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('users.user_id','=',Auth::user()->user_id)
        ->where('bodega.bodega_id','=',$bodega)
        ->orderBy('cabecera_ingreso_id','asc');
    }
   
    public function scopeIngreso($query, $id){
        return $query->join('users','users.user_id','=','cabecera_ingreso_bodega.user_id')->join('bodega','bodega.bodega_id','=','cabecera_ingreso_bodega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('users.user_id','=',Auth::user()->user_id)->where('cabecera_ingreso_estado','=','1')->where('cabecera_ingreso_id','=',$id);
    }
    public function scopeBodegaDistinsc($query){
        return $query->join('users','users.user_id','=','cabecera_ingreso_bodega.user_id')->join('bodega','bodega.bodega_id','=','cabecera_ingreso_bodega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('cabecera_ingreso_bodega.user_id','=',Auth::user()->user_id)->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('bodega.bodega_nombre','asc');
    }
    public function scopeSecuencial($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','cabecera_ingreso_bodega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('cabecera_ingreso_bodega.rango_id','=',$id);
    }
    public function detalles(){
        return $this->hasMany(Detalle_IB::class, 'cabecera_ingreso_id', 'cabecera_ingreso_id');
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
    public function cuentapagar(){
        return $this->belongsTo(Cuenta_Pagar::class, 'cuenta_id', 'cuenta_id');
    }
    public function proveedor(){
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }
    public function tipo(){
        return $this->belongsTo(Tipo_MI::class, 'tipo_id', 'tipo_id');
    }
}
