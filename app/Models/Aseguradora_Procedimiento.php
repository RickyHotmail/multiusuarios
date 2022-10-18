<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Aseguradora_Procedimiento extends Model
{
    use HasFactory;
    protected $table='aseguradora_procedimiento';
    protected $primaryKey = 'procedimientoA_id';
    public $timestamps = true;
    protected $fillable = [
        'procedimientoA_codigo',
        'procedimientoA_valor',
        'procedimientoA_estado',        
        'procedimiento_id',
        'cliente_id',
    ];
    protected $guarded =[
    ];
    public function procedimiento(){
        return $this->belongsTo(Procedimiento_Especialidad::class, 'procedimiento_id', 'procedimiento_id');
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }
    public function pro(){
        return $this->hasManyThrough(Especialidad::class, Procedimiento_Especialidad::class, 'procedimiento_id', 'especialidad_id', 'procedimiento_id', 'especialidad_id');
    }
    public function scopeAseguradoraProcedimientoById($query, $procedimientoA_id){
        return $query->join('procedimiento_especialidad','procedimiento_especialidad.procedimiento_id','=','aseguradora_procedimiento.procedimiento_id'
                    )->join('producto','producto.producto_id','=','procedimiento_especialidad.producto_id'
                    )->where('procedimientoA_id','=',$procedimientoA_id);
    }
    public function scopeAseguradoraProcedimientos($query){
        return $query->join('cliente','cliente.cliente_id','=','aseguradora_procedimiento.cliente_id'
                    )->join('tipo_cliente','tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id'
                    )->join('procedimiento_especialidad','procedimiento_especialidad.procedimiento_id','=','aseguradora_procedimiento.procedimiento_id'
                    )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'
                    )->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id
                    )->where('aseguradora_procedimiento.procedimientoA_estado','=','1'
                    )->where('tipo_cliente.tipo_cliente_nombre','=','Aseguradora');
    }
    public function scopeAseguradoraProcedimiento($query, $id){
        return $query->join('cliente','cliente.cliente_id','=','aseguradora_procedimiento.cliente_id'
                    )->join('tipo_cliente','tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id'
                    )->join('procedimiento_especialidad','procedimiento_especialidad.procedimiento_id','=','aseguradora_procedimiento.procedimiento_id'
                    )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'
                    )->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id
                    )->where('aseguradora_procedimiento.procedimientoA_estado','=','1'
                    )->where('cliente.cliente_id','=',$id);
    }
    public function scopeProcedimientosAsignados($query, $procedimiento, $aseguradora){
        return $query->join('cliente','cliente.cliente_id','=','aseguradora_procedimiento.cliente_id'
                    )->join('categoria_cliente', 'categoria_cliente.categoria_cliente_id','=','cliente.categoria_cliente_id'
                    )->join('procedimiento_especialidad','procedimiento_especialidad.procedimiento_id','=','aseguradora_procedimiento.procedimiento_id'
                    )->join('producto','producto.producto_id','=','procedimiento_especialidad.producto_id'
                    )->where('categoria_cliente.empresa_id','=',Auth::user()->empresa_id
                    )->where('aseguradora_procedimiento.procedimiento_id','=',$procedimiento
                    )->where('aseguradora_procedimiento.cliente_id','=',$aseguradora);
    }

    public function scopeAseguradoraProcedimientosProducto($query){
        return $query->join('cliente','cliente.cliente_id','=','aseguradora_procedimiento.cliente_id'
                    )->join('tipo_cliente','tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id'
                    )->join('procedimiento_especialidad','procedimiento_especialidad.procedimiento_id','=','aseguradora_procedimiento.procedimiento_id'
                    )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'
                    )->join('producto','producto.producto_id','=','procedimiento_especialidad.producto_id'
                    )->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id
                    )->where('aseguradora_procedimiento.procedimientoA_estado','=','1'
                    )->where('tipo_cliente.tipo_cliente_nombre','=','Aseguradora');
    }
}
