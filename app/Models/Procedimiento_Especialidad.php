<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Procedimiento_Especialidad extends Model
{
    use HasFactory;
    protected $table='procedimiento_especialidad';
    protected $primaryKey = 'procedimiento_id';
    public $timestamps=true;
    protected $fillable = [
        'procedimiento_estado',        
        'especialidad_id',
        'producto_id',
    ];
    protected $guarded =[
    ];
    public function especialidad(){
        return $this->belongsTo(Especialidad::class, 'especialidad_id', 'especialidad_id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function scopeProcedimientoEspecialidades($query){
        return $query->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'
                    )->join('producto','producto.producto_id','=','procedimiento_especialidad.producto_id'
                    )->join('grupo_producto','grupo_producto.grupo_id','=','producto.grupo_id'
                    )->where('especialidad.empresa_id','=',Auth::user()->empresa_id
                    )->where('procedimiento_especialidad.procedimiento_estado','=','1'
                    )->where('grupo_producto.grupo_nombre','=','Laboratorio'
                    )->where('grupo_producto.grupo_nombre','=','Imagen'
                    )->orwhere('grupo_producto.grupo_nombre','=','Procedimiento');
    }
    public function scopeProcedimientoEspecialidad($query, $id){
        return $query->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id')->where('especialidad.empresa_id','=',Auth::user()->empresa_id)->where('procedimiento_id','=',$id);
    }  
    public function scopeProcedimientoEspecialidadE($query, $buscar){
        return $query->join('producto','producto.producto_id','=','procedimiento_especialidad.producto_id'
                    )->join('grupo_producto','grupo_producto.grupo_id','=','producto.grupo_id'
                    )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'                    
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('procedimiento_especialidad.especialidad_id','=',$buscar);
    }
    public function scopeProcedimientoProducto($query, $id){
        return $query->join('producto','producto.producto_id','=','procedimiento_especialidad.producto_id'
                    )->join('grupo_producto','grupo_producto.grupo_id','=','producto.grupo_id'
                    )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'                    
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('procedimiento_especialidad.producto_id','=',$id);
    }
    public function scopeProcedimientoProductoEspecialidad($query, $producto,$especialidad){
        return $query->join('producto','producto.producto_id','=','procedimiento_especialidad.producto_id'
                    )->join('grupo_producto','grupo_producto.grupo_id','=','producto.grupo_id'
                    )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'                    
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('procedimiento_especialidad.producto_id','=',$producto
                    )->where('procedimiento_especialidad.especialidad_id','=',$especialidad);
    }
    public function scopeProcedimientoEspecialidadbyEspecialidad($query,$especialidad){
        return $query->join('producto','producto.producto_id','=','procedimiento_especialidad.producto_id'
                    )->join('grupo_producto','grupo_producto.grupo_id','=','producto.grupo_id'
                    )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'                    
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('procedimiento_especialidad.especialidad_id','=',$especialidad);
    }

    public function aseguradoraprocedimientos(){
        return $this->hasMany(Aseguradora_Procedimiento::class, 'procedimiento_id', 'procedimiento_id');
    }
  
    public function entidadprocedimientos(){
        return $this->hasMany(Entidad_Procedimiento::class, 'procedimiento_id', 'procedimiento_id');
    }
}
