<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Decimo_Cuarto extends Model
{
    use HasFactory;
    protected $table ='decimo_cuarto';
    protected $primaryKey = 'decimo_id';
    public $timestamps = true;
    protected $fillable = [        
        'decimo_fecha',
        'decimo_fecha_emision',
        'decimo_tipo', 
        'decimo_valor',
        'decimo_periodo',
        'decimo_descripcion',
        'decimo_estado',
        'diario_id',
        'empleado_id'
    ];
    protected $guarded =[
    ];
    public function scopedecimos($query){
        return $query->join('diario','diario.diario_id','=','decimo_cuarto.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('decimo_estado','=','1')->orderBy('decimo_fecha','asc');
    }
    public function scopeEmpleados($query){
        return $query->join('empleado','empleado.empleado_id','=','decimo_cuarto.empleado_id')->join('diario','diario.diario_id','=','decimo_cuarto.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('decimo_estado','=','1')->orderBy('empleado_nombre','asc');
    }
    public function scopeSucursal($query){
        return $query->join('empleado','empleado.empleado_id','=','decimo_cuarto.empleado_id')
        ->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('tipo_empleado', 'tipo_empleado.tipo_id','=','empleado.tipo_id')
        ->join('sucursal', 'sucursal.sucursal_id','=','tipo_empleado.sucursal_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('decimo_estado','=','1')->orderBy('sucursal_nombre','asc');
    }
    public function scopevalidarDecimoCuarto($query, $fechaI, $sucursal, $empleado){
        return $query->join('empleado','empleado.empleado_id','=','decimo_cuarto.empleado_id')
       ->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
       ->join('tipo_empleado', 'tipo_empleado.tipo_id','=','empleado.tipo_id')
       ->join('sucursal', 'sucursal.sucursal_id','=','tipo_empleado.sucursal_id')
       ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
       ->where('decimo_estado','=','1')->where('sucursal.sucursal_id','=',$sucursal)->where('decimo_fecha','=',$fechaI)
       ->where('empleado.empleado_id','=',$empleado);

      
   }
    public function scopebuscar($query, $fechaI, $empleado, $sucursal){
         $query->join('empleado','empleado.empleado_id','=','decimo_cuarto.empleado_id')
        ->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('tipo_empleado', 'tipo_empleado.tipo_id','=','empleado.tipo_id')
        ->join('sucursal', 'sucursal.sucursal_id','=','tipo_empleado.sucursal_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('decimo_estado','=','1')->where('decimo_fecha','=',$fechaI)->orderBy('empleado_nombre','asc');
        if($empleado != '0'){
            $query->where('empleado.empleado_id','=',$empleado);
        }  
        if($sucursal != '0'){
            $query->where('sucursal.sucursal_id','=',$sucursal);
        }   
        return $query;
    }
    public function scopedecimo($query, $id){
        return $query->join('empleado','empleado.empleado_id','=','decimo_cuarto.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('decimo_id','=',$id);
    }
    public function scopeRolFecha($query,$fechadesde){
        return $query->join('empleado','empleado.empleado_id','=','decimo_cuarto.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('decimo_estado','=','1')
        ->where('decimo_fecha', '=', $fechadesde);
    }
    public function scopeExtraerDecimoCuarto($query,$fecha,$sucursal){
        return $query->join('empleado','empleado.empleado_id','=','decimo_cuarto.empleado_id')
        ->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('tipo_empleado', 'tipo_empleado.tipo_id','=','empleado.tipo_id')
        ->where('tipo_empleado.sucursal_id','=',$sucursal)
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('decimo_estado','=','1')
        ->where('decimo_fecha', '=', $fecha)
        ->orderBy('empleado.empleado_nombre','asc');
    }
    public function scopeValidacion($query, $id){
        return $query->join('diario','diario.diario_id','=','decimo_cuarto.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_id','=',$id);
    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
   
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }
}
