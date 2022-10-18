<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Examen extends Model
{
    use HasFactory;
    protected $table='examen';
    protected $primaryKey = 'examen_id';
    public $timestamps = true;
    protected $fillable = [
        'examen_estado',
        'tipo_id',
        'producto_id',
    ];
    protected $guarded =[];
    
    public function scopeExamenes($query){
        return $query->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->join('producto','examen.producto_id','=','producto.producto_id'
                    )->where('tipo_examen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('examen.examen_estado','=','1')->orderBy('producto_nombre','asc');
    }
    public function scopeExamen($query, $id){
        return $query->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->where('tipo_examen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('examen.examen_id','=',$id);
    }
    public function scopeBuscarProductoslaboratorio($query, $id){
        return $query->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->join('detalle_laboratorio','examen.examen_id','=','detalle_laboratorio.examen_id'
                    )->where('tipo_examen.empresa_id', '=', Auth::user()->empresa_id
                    )->where('examen.producto_id','=',$id);
    }
    public function scopeExamenByIdProducto($query, $id){
        return $query->join('producto', 'producto.producto_id', '=', 'examen.producto_id'
                    )->where('producto.empresa_id', '=', Auth::user()->empresa_id
                    )->where('examen.producto_id','=',$id);
    }
    public function scopeBuscarProductosProcedimiento($query, $paciente, $especialidad){
        return $query->join('producto','examen.producto_id','=','producto.producto_id'
                    )->join('procedimiento_especialidad','procedimiento_especialidad.producto_id','=','producto.producto_id'
                    )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'
                    )->join('aseguradora_procedimiento','aseguradora_procedimiento.procedimiento_id','=','procedimiento_especialidad.procedimiento_id'
                    )->join('cliente','cliente.cliente_id','=','aseguradora_procedimiento.cliente_id'
                    )->join('paciente','paciente.cliente_id','=','cliente.cliente_id'
                    )->where('producto.empresa_id','=',Auth::user()->empresa_id
                    )->where('paciente.paciente_id','=',$paciente
                    )->where('especialidad.especialidad_id','=',$especialidad);
    }  
    
    public function detalleslaboratorio()
    {
        return $this->hasMany(Detalle_Laboratorio::class, 'examen_id', 'examen_id');
    }
    public function detallesexamen()
    {
        return $this->hasMany(Detalle_Examen::class, 'examen_id', 'examen_id');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }

    public function tipoExamen()
    {
        return $this->belongsTo(Tipo_Examen::class, 'tipo_id', 'tipo_id');
    } 
     
}
