<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Grupo_Activo extends Model
{
    use HasFactory;
    protected $table='grupo_activo';
    protected $primaryKey = 'grupo_id';
    public $timestamps = true;
    protected $fillable = [        
        'grupo_nombre',
        'grupo_porcentaje', 
        'grupo_estado',
        'sucursal_id',
        'cuenta_depreciacion',
        'cuenta_gasto',       
    ];
    protected $guarded =[
    ];   
    public function scopeGrupos($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('grupo_activo.grupo_estado','=','1')->orderBy('grupo_activo.grupo_nombre','asc');
    }
    public function scopeGrupo($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('grupo_id','=',$id);
    }
    public function scopeGrupoNombre($query, $nombre, $sucursal){
        return $query->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('grupo_nombre','=',$nombre)->where('sucursal_nombre','=',$sucursal);
    }
    public function scopeGrupoxSucursal($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('sucursal.sucursal_id','=',$id);
    }
    public function scopeGrupoxCuenta($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->join('cuenta','cuenta.cuenta_id','=','grupo_activo.cuenta_depreciacion')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta.cuenta_id','=',$id);
    }
    public function scopeGrupoxCuentaGasto($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->join('cuenta','cuenta.cuenta_id','=','grupo_activo.cuenta_gasto')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta.cuenta_id','=',$id);
    }
    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }
    public function cuentaDepreciacion(){
        return $this->belongsTo(Cuenta::class, 'cuenta_depreciacion', 'cuenta_id');
    }
    public function cuentaGasto(){
        return $this->belongsTo(Cuenta::class, 'cuenta_gasto', 'cuenta_id');
    }
}
