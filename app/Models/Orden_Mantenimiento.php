<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Orden_Mantenimiento extends Model
{
    use HasFactory;
    protected $table="orden_mantenimiento";
    protected $primaryKey="orden_id";
    public $timestamps=true;
    protected $fillable=[
        'orden_numero',
        'orden_serie',
        'orden_secuencial',
        'orden_fecha_inicio',
        'orden_finalizacion',
        'orden_prioridad',
        'orden_lugar',
        'orden_descripcion',
        'orden_logistica',
        'orden_observacion',
        'orden_resultado',
        'orden_informe',
        'orden_recibido_por',
        'orden_estado',
        'tipo_id',
        'cliente_id',
        'user_id',
        'sucursal_id'
    ];
    protected $guarded =[
    ];
    public function tipo(){
        return $this->hasOne(Tipo_Mantenimiento::class, 'tipo_id', 'tipo_id');
    }
    public function cliente(){
        return $this->hasOne(Cliente::class, 'cliente_id', 'cliente_id');
    }
    public function detalles(){
        return $this->hasMany(Detalle_Mantenimiento::class, 'orden_id', 'orden_id');
    }
    public function detallesOrden(){
        return $this->hasMany(Detalle_Orden_Mantenimiento::class, 'orden_id', 'orden_id');
    }
    public function responsables(){
        return $this->hasMany(Responsable_Mantenimiento::class, 'orden_id', 'orden_id');
    }
    public function usuario(){
        return $this->hasOne(usuario::class, 'user_id', 'user_id');
    }
    public function sucursal(){
        return $this->hasOne(sucursal::class, 'sucursal_id', 'sucursal_id');
    }
    public function scopeOrdenes($query){
        return $query->select('orden_mantenimiento.orden_id', 'orden_mantenimiento.orden_fecha_inicio', 'orden_mantenimiento.orden_estado','orden_mantenimiento.orden_resultado','orden_mantenimiento.orden_prioridad', 'orden_mantenimiento.tipo_id', 'orden_mantenimiento.orden_lugar','cliente.cliente_id', 'cliente.cliente_nombre'
                    )->join('sucursal', 'sucursal.sucursal_id', '=', 'orden_mantenimiento.sucursal_id'
                    )->join('cliente', 'cliente.cliente_id', "=", "orden_mantenimiento.cliente_id"
                    //)->where('sucursal.empresa_id', '=', Auth::user()->empresa_id);
                    )->where('sucursal.empresa_id', '=', 1
                    )->orderBy('orden_mantenimiento.orden_id', 'desc');
    }
    public function scopeOrdenesFilterByEstado($query, $estado){
        return $query->select('orden_mantenimiento.orden_id', 'orden_mantenimiento.orden_fecha_inicio', 'orden_mantenimiento.orden_estado','orden_mantenimiento.orden_resultado','orden_mantenimiento.orden_prioridad', 'orden_mantenimiento.tipo_id', 'orden_mantenimiento.orden_lugar','cliente.cliente_id', 'cliente.cliente_nombre'
                    )->join('sucursal', 'sucursal.sucursal_id', '=', 'orden_mantenimiento.sucursal_id'
                    )->join('cliente', 'cliente.cliente_id', "=", "orden_mantenimiento.cliente_id"
                    //)->where('sucursal.empresa_id', '=', Auth::user()->empresa_id);
                    )->where('sucursal.empresa_id', '=', 1
                    )->where('orden_mantenimiento.orden_estado','=', $estado
                    )->orderBy('orden_mantenimiento.orden_id', 'desc');
    }
}
