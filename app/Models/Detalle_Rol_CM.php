<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_Rol_CM extends Model
{
    use HasFactory;
    protected $table='detalle_rol_cm';
    protected $primaryKey = 'detalle_rol_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_rol_fecha_inicio',
        'detalle_rol_fecha_fin',  
        'detalle_rol_descripcion',       
        'detalle_rol_valor',  
        'detalle_rol_tipo',     
        'detalle_rol_contabilizado',     
        'detalle_rol_estado',     
        'rubro_id',     
        'cabecera_rol_id',       
    ];
    protected $guarded =[
    ];
    public function scopeDetalleRol($query, $id){
        return $query->join('cabecera_rol_cm','cabecera_rol_cm.cabecera_rol_id','=','detalle_rol_cm.cabecera_rol_id')->where('cabecera_rol_cm.cabecera_rol_id','=', $id);
    }
    public function rol(){
        return $this->belongsTo(Cabecera_Rol_CM::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
   
    public function movimiento(){
        return $this->belongsTo(Rubro::class, 'rubro_id', 'rubro_id');
    }
}
