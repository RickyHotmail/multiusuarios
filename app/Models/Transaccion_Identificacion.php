<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transaccion_Identificacion extends Model
{
    use HasFactory;
    protected $table='transaccion_identificacion';
    protected $primaryKey = 'transaccion_id';
    public $timestamps=true;
    protected $fillable = [
        'transaccion_codigo',         
        'transaccion_estado',       
        'tipo_transaccion_id', 
        'tipo_identificacion_id',    
                 
    ];
    protected $guarded =[
    ];

    public function scopeTransaccionIdentificaciones($query){
        return $query->join('tipo_transaccion', 'tipo_transaccion.tipo_transaccion_id','=','transaccion_identificacion.tipo_transaccion_id')->join('tipo_identificacion', 'tipo_identificacion.tipo_identificacion_id','=','transaccion_identificacion.tipo_identificacion_id')->where('tipo_transaccion.empresa_id','=',Auth::user()->empresa_id)->where('transaccion_estado','=','1')->orderBy('transaccion_codigo','asc');
    }

    public function scopeTransaccionIdentificacion($query, $id){
        return $query->join('tipo_transaccion', 'tipo_transaccion.tipo_transaccion_id','=','transaccion_identificacion.tipo_transaccion_id')->join('tipo_identificacion', 'tipo_identificacion.tipo_identificacion_id','=','transaccion_identificacion.tipo_identificacion_id')->where('tipo_transaccion.empresa_id','=',Auth::user()->empresa_id)->where('transaccion_id','=',$id);
    }

    public function scopeIdentificacion($query, $identificacion_id, $transaccion){
        return $query->join('tipo_transaccion', 'tipo_transaccion.tipo_transaccion_id','=','transaccion_identificacion.tipo_transaccion_id')->where('tipo_transaccion.empresa_id','=',Auth::user()->empresa_id)->where('tipo_transaccion.tipo_transaccion_nombre','=',$transaccion)->where('tipo_identificacion_id','=',$identificacion_id);
    }

    public function transaccion()
    {
        return $this->belongsTo(Tipo_Transaccion::class, 'tipo_transaccion_id', 'tipo_transaccion_id');
    }
}
