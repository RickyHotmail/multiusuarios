<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Anticipo_Empleado extends Model
{
    use HasFactory;
    protected $table ='anticipo_empleado';
    protected $primaryKey = 'anticipo_id';
    public $timestamps = true;
    protected $fillable = [ 
        'anticipo_numero',
        'anticipo_serie',
        'anticipo_secuencial',       
        'anticipo_fecha',
        'anticipo_tipo', 
        'anticipo_documento',
        'anticipo_motivo',
        'anticipo_valor',
        'anticipo_saldo',
        'anticipo_saldom',
        'rango_id',
        'empleado_id',
        'diario_id',
        'arqueo_id',
        'anticipo_estado'       
    ];
    protected $guarded =[
    ];

    public function scopeEmpleadosRol($query){
        return $query->join('empleado','empleado.empleado_id','=','anticipo_empleado.empleado_id')->join('rango_documento','rango_documento.rango_id','=','anticipo_empleado.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('anticipo_estado', '=', '1');
    }
    public function scopeEmpleadosRolSucursal($query, $id){
        return $query->join('empleado','empleado.empleado_id','=','anticipo_empleado.empleado_id')->join('rango_documento','rango_documento.rango_id','=','anticipo_empleado.rango_id')
        ->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')
        ->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('anticipo_estado', '=', '1')
        ->where('sucursal.sucursal_id','=',$id);
    }
    
    public function scopeAnticipo($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','anticipo_empleado.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('anticipo_id','=',$id);
    }
    public function scopeAnticipoEmpleados($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','anticipo_empleado.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('anticipo_estado','=','1')->orderBy('anticipo_motivo','asc');
    }
    public function scopeSecuencial($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','anticipo_empleado.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('rango_documento.rango_id','=',$id);

    }  
    public function scopeAntProByFec($query, $fechaI, $fechaF, $empleado_id, $sucursal_id,$todo){
        $query->join('rango_documento','rango_documento.rango_id','=','anticipo_empleado.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('empleado_id','=',$empleado_id)->orderBy('anticipo_fecha','asc');
        if($sucursal_id != '0'){
            $query->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')
            ->where('punto_emision.sucursal_id','=',$sucursal_id);
        }
        if($todo != 1){
            $query->where('anticipo_fecha','>=',$fechaI)->where('anticipo_fecha','<=',$fechaF);
        }
        return $query;
    }  
    public function scopeAnticipoEmpleado($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','anticipo_empleado.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)
        ->where('empleado_id','=',$id);
    }
    public function scopeAnticipoEmpleadobuscar($query, $id){
        return $query->join('diario','diario.diario_id','=','anticipo_empleado.diario_id')
        ->join('rango_documento','rango_documento.rango_id','=','anticipo_empleado.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('empleado','empleado.empleado_id','=','anticipo_empleado.empleado_id')
        ->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)
        ->where('anticipo_estado','=','1')
        ->where('anticipo_empleado.empleado_id','=',$id);
    }
    public function scopeAnticiposByEmpleadoFecha($query, $empleado_id, $fecha){
        return $query->join('rango_documento','rango_documento.rango_id','=','anticipo_empleado.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('empleado_id','=',$empleado_id)->where('anticipo_fecha','<=',$fecha)->orderBy('anticipo_fecha','asc');
    }
    public function scopeAnticipoEmpleadoDescuentos($query, $id){
        return $query->join('descuento_anticipo_empleado','descuento_anticipo_empleado.anticipo_id','=','anticipo_empleado.anticipo_id')->where('anticipo_empleado.anticipo_id','=',$id);
    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }
    public function rango()
    {
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }

}
