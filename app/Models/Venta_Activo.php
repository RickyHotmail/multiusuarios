<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Venta_Activo extends Model
{
    use HasFactory;
    protected $table='venta_activo';
    protected $primaryKey = 'venta_id';
    public $timestamps = true;
    protected $fillable = [        
        'venta_fecha',
        'venta_descripcion', 
        'venta_monto',
        'activo_id',
        'venta_estado',
    ];
    protected $guarded =[
    ];   
    public function scopeVentasActivo($query){
        return $query->join('activo_fijo','activo_fijo.activo_id','=','venta_activo.activo_id')->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('venta_activo.venta_estado','=','1')->orderBy('venta_activo.venta_fecha','desc');
    }
    public function scopeVentaActivo($query, $id){
        return $query->join('activo_fijo','activo_fijo.activo_id','=','venta_activo.activo_id')->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('venta_id','=',$id);
    }
    public function scopeVentaActivoxSucursalxActivo($query, $idSucursal, $idActivo){
        return $query->join('activo_fijo','activo_fijo.activo_id','=','venta_activo.activo_id')->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('sucursal.sucursal_id','=',$idSucursal)->where('venta_activo.activo_id','=',$idActivo);;
    }
    public function scopeSumactivo($query, $id){
        return $query->select(DB::raw('SUM(venta_monto) as venta_monto'))->join('activo_fijo','activo_fijo.activo_id','=','venta_activo.activo_id')->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('venta_activo.activo_id','=',$id);
    } 
    
    public function scopesumaVentaDepre($query, $id,$fech2){
        return $query->select(DB::raw('SUM(venta_monto) as venta_monto'))->join('activo_fijo','activo_fijo.activo_id','=','venta_activo.activo_id')->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('venta_activo.activo_id','=',$id)->where('venta_activo.venta_fecha','<=',$fech2);;
    }  
    public function activoFijo(){
        return $this->belongsTo(Activo_Fijo::class, 'activo_id', 'activo_id');
    }
}
