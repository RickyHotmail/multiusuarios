<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Decimo_Tercero extends Model
{
    use HasFactory;
    protected $table ='decimo_tercero';
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
        return $query->join('diario','diario.diario_id','=','decimo_tercero.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('decimo_estado','=','1')->orderBy('decimo_fecha','asc');
    }
    public function scopedecimo($query, $id){
        return $query->join('diario','diario.diario_id','=','decimo_tercero.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('decimo_id','=',$id);
    }
    public function scopeRolFecha($query,$fechadesde){
        return $query->join('empleado','empleado.empleado_id','=','decimo_tercero.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('decimo_estado','=','1')
        ->where('decimo_fecha', '=', $fechadesde);
    }
    public function scopeExtraerDecimoTercero($query,$fecha){
        return $query->join('empleado','empleado.empleado_id','=','decimo_tercero.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('decimo_estado','=','1')
        ->where('decimo_fecha', '=', $fecha)
        ->orderBy('empleado.empleado_nombre','asc');
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
