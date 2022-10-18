<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Activo_Fijo extends Model
{
    use HasFactory;
    protected $table='activo_fijo';
    protected $primaryKey = 'activo_id';
    public $timestamps = true;
    protected $fillable = [ 
        'activo_fecha_inicio',
        'activo_fecha_documento',
        'activo_descripcion',
        'activo_valor', 
        'activo_valor2',
        'activo_base_depreciar',
        'activo_vida_util',
        'activo_valor_util',
        'activo_depreciacion',
        'activo_depreciacion_mensual',
        'activo_depreciacion_anual',
        'activo_depreciacion_acumulada',
        'activo_estado',
        'grupo_id',
        'diario_id',
        'producto_id',
        'proveedor_id',
        'transaccion_id',
        'departamento_id',
        'empleado_id',                                                 
    ];
    protected $guarded =[
    ];   
    public function scopeActivoFijos($query){
        return $query->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('activo_fijo.activo_estado','=','1');
    }
    public function scopeActivoFijo($query, $id){
        return $query->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('activo_id','=',$id);
    }
    public function scopeActivos($query){
        return $query->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
    }
    public function scopeActivoFijoxSucursal($query, $id){
        return $query->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('sucursal.sucursal_id','=',$id);
    }
    public function scopebuscar($query, $id, $estado){
         $query->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
        if($id != '0'){
            $query->where('sucursal.sucursal_id','=',$id);
        }
        if($estado != '4'){
            $query->where('activo_estado','=',$estado);
        }
        return $query->orderBy('activo_fecha_inicio','asc');
    }
    public function scopeActivoFijoxSucursalprodu($query, $id){
        return $query->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('producto','producto.producto_id','=','activo_fijo.producto_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('sucursal.sucursal_id','=',$id);
    }
    
    public function grupoActivo(){
        return $this->belongsTo(Grupo_Activo::class, 'grupo_id', 'grupo_id');
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function proveedor(){
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }
    public function transaccionCompra(){
        return $this->belongsTo(Transaccion_Compra::class, 'transaccion_id', 'transaccion_id');
    }
    public function departamento(){
        return $this->belongsTo(Empresa_Departamento::class, 'departamento_id', 'departamento_id');
    }
    public function empleado(){
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }
}
