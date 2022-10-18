<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Descuento_Anticipo_Empleado extends Model
{
    use HasFactory;
    protected $table='descuento_anticipo_empleado';
    protected $primaryKey = 'descuento_id';
    public $timestamps=true;
    protected $fillable = [
        'descuento_fecha',
        'descuento_descripcion',
        'descuento_valor',       
        'descuento_estado', 
        'cabecera_rol_cm_id', 
        'cabecera_rol_id',        
        'anticipo_id', 
        'diario_id',
    ];
    protected $guarded =[
    ];
    public function scopeAnticipos($query, $id){
        return $query->join('diario','diario.diario_id','=','descuento_anticipo_empleado.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('anticipo_id','=',$id);
    }
    public function scopeDescuentosByAnticipo($query, $anticipo_id){
        return $query->join('anticipo_empleado','anticipo_empleado.anticipo_id','=','descuento_anticipo_empleado.anticipo_id')->where('anticipo_empleado.anticipo_id','=',$anticipo_id);
    }
    public function scopeDescuentosAnticipoByEmpleadoFecha($query, $empleado_id, $fecha){
        return $query->join('anticipo_empleado','anticipo_empleado.anticipo_id','=','descuento_anticipo_empleado.anticipo_id')->where('anticipo_empleado.empleado_id','=',$empleado_id)->where('descuento_fecha','<=',$fecha);
    }
    public function scopeDescuentosAnticipo($query, $anticipo_id, $fecha){
        return $query->join('anticipo_empleado','anticipo_empleado.anticipo_id','=','descuento_anticipo_empleado.anticipo_id')->where('anticipo_empleado.anticipo_id','=',$anticipo_id)->where('descuento_fecha','<=',$fecha);
    }
    public function rol()
    {
        return $this->belongsTo(Rol_Consolidado::class, 'cabecera_rol_id', 'cabecera_rol_id');
    } 
    public function rolcm()
    {
        return $this->belongsTo(Cabecera_Rol_CM::class, 'cabecera_rol_cm_id', 'cabecera_rol_id');
    }  
    public function anticipo()
    {
        return $this->belongsTo(Anticipo_Empleado::class, 'anticipo_id', 'anticipo_id');
    } 
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    } 

}


