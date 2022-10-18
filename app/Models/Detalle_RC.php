<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalle_RC extends Model
{
    use HasFactory;
    protected $table='detalle_rc';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_tipo',
        'detalle_base',       
        'detalle_porcentaje',        
        'detalle_valor', 
        'detalle_asumida',
        'detalle_estado',
        'retencion_id',
        'concepto_id',
    ];
    protected $guarded =[
    ];
    public function scopeDetalleByFecha($query, $fechaInicio, $fechaFin){
        return $query->join('retencion_compra','retencion_compra.retencion_id','=','detalle_rc.retencion_id')->join('concepto_retencion','concepto_retencion.concepto_id','=','detalle_rc.concepto_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('retencion_fecha','>=',$fechaInicio)->where('retencion_fecha','<=',$fechaFin);
    }
    public function conceptoRetencion(){
        return $this->belongsTo(Concepto_Retencion::class, 'concepto_id', 'concepto_id');
    }
}
