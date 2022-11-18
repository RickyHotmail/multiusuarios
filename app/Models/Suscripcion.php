<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Suscripcion extends Model
{
    use HasFactory;
    protected $table='suscripcion';
    protected $primaryKey='suscripcion_id';

    protected $fillable=[
        'empresa_id',
        'plan_id',
        'suscripcion_fecha_inicio',
        'suscripcion_fecha_finalizacion',
        'suscripcion_cantidad_generado',
        'suscripcion_permiso',
        'suscripcion_ilimitada',
        'suscripcion_estado'
    ];
    protected $guarded=[
    ];

    public function scopeSuscripcion($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id);
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }

    public function plan(){
        return $this->hasOne(Plan::class, 'plan_id', 'plan_id');
    }

    public function pagos(){
        return $this->hasMany(Pago::class, 'suscripcion_id', 'suscripcion_id');
    }
    
    public function scopeByEmpresa($query, $id){
        return $query->join('empresa', 'empresa.empresa_id','=','suscripcion.empresa_id')
                     ->where('empresa.empresa_id', '=', $id)
                     ->where('suscripcion_estado','=',1);
    }
}
