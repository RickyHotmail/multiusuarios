<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Imagen extends Model
{
    use HasFactory;
    protected $table='imagen';
    protected $primaryKey = 'imagen_id';
    public $timestamps = true;
    protected $fillable = [
        'imagen_nombre',
        'imagen_estado',
        'tipo_id',
        'producto_id'
    ];
    protected $guarded =[];
    
    public function scopeImagenes($query){
        return $query->join('tipo_imagen','tipo_imagen.tipo_id','=','imagen.tipo_id'
                    )->where('tipo_imagen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('imagen.imagen_estado','=','1')->orderBy('imagen_nombre','asc');

    }
    public function scopeImagen($query, $id){
        return $query->join('tipo_imagen','tipo_imagen.tipo_id','=','imagen.tipo_id'
                    )->where('tipo_imagen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('imagen.imagen_id','=',$id);
    }  

    public function scopeBuscarImagenes($query, $buscar){
        return $query->join('tipo_imagen','tipo_imagen.tipo_id','=','imagen.tipo_id'
                    )->join('producto','producto.producto_id','=','imagen.producto_id'
                    )->where('tipo_imagen.empresa_id', '=', Auth::user()->empresa_id
                    )->where(DB::raw('lower(producto.producto_nombre)'), 'like', '%'.strtolower($buscar).'%'
                    )->where('imagen.imagen_estado','=','1')->orderBy('imagen_nombre','asc');
    }

    public function scopeBuscarImagenesEspecialidad($query, $buscar, $especialidad, $paciente){
        return $query->join('producto','producto.producto_id','=','imagen.producto_id'
                    //)->where('tipo_imagen.empresa_id', '=', Auth::user()->empresa_id
                    
                    )->join('procedimiento_especialidad','procedimiento_especialidad.producto_id','=','producto.producto_id'
                    )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'
                    )->join('aseguradora_procedimiento','aseguradora_procedimiento.procedimiento_id','=','procedimiento_especialidad.procedimiento_id'
                    )->join('cliente','cliente.cliente_id','=','aseguradora_procedimiento.cliente_id'
                    )->join('paciente','paciente.cliente_id','=','cliente.cliente_id'
                    )->where('imagen.imagen_estado','=','1')->orderBy('imagen_nombre','asc'
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('paciente.paciente_id','=',$paciente
                    )->where('especialidad.especialidad_id','=',$especialidad
                    )->where(DB::raw('lower(producto.producto_nombre)'), 'like', '%'.strtolower($buscar).'%');
                    
    }

    public function producto()
    {
        return $this->hasOne(Producto::class, 'producto_id', 'producto_id');
    }
}

//"select * from "imagen" inner join "productos" on "producto"."producto_id" = "imagen"."producto_id" inner join "procedimiento_especialidad" on "procedimiento_especialidad"."producto_id" = "producto"."producto_id" inner join "especialidad" on "especialidad"."especialidad_id" = "procedimiento_especialidad"."especialidad_id" inner join "aseguradora_procedimiento" on "aseguradora_procedimiento"."procedimiento_id" = "procedimiento_especialidad"."procedimiento_id" inner join "cliente" on "cliente"."cliente_id" = "aseguradora_procedimiento"."cliente_id" inner join "paciente" on "paciente"."cliente_id" = "cliente"."cliente_id" where "imagen"."imagen_estado" = 1 and "producto"."empresa_id" = 1 and "paciente"."paciente_id" = 11 and "especialidad"."especialidad_id" = 6 and lower(producto.producto_nombre)::text like %a% order by "imagen_nombre" asc"

//
/* public function scopeBuscarProductosProcedimiento($query, $paciente, $especialidad){
    return $query->join('producto','examen.producto_id','=','producto.producto_id'
                )->join('procedimiento_especialidad','procedimiento_especialidad.producto_id','=','producto.producto_id'
                )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'
                )->join('aseguradora_procedimiento','aseguradora_procedimiento.procedimiento_id','=','procedimiento_especialidad.procedimiento_id'
                )->join('cliente','cliente.cliente_id','=','aseguradora_procedimiento.cliente_id'
                )->join('paciente','paciente.cliente_id','=','cliente.cliente_id'
                )->where('producto.empresa_id','=',Auth::user()->empresa_id
                )->where('paciente.paciente_id','=',$paciente
                )->where('especialidad.especialidad_id','=',$especialidad);
}  */ 