<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalle_Amortizacion extends Model
{
    use HasFactory;
    protected $table='detalle_amortizacion';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_fecha',       
        'detalle_mes',
        'detalle_anio',
        'detalle_valor',
        'detalle_estado',
        'amortizacion_id',
        'diario_id',
    ];

    protected $guarded =[
    ];
    public function scopeAmortizaciones($query, $id){
        return $query->join('amortizacion_seguros', 'amortizacion_seguros.amortizacion_id','=','detalle_amortizacion.amortizacion_id')->join('sucursal', 'sucursal.sucursal_id','=','amortizacion_seguros.sucursal_id')->where('detalle_amortizacion.amortizacion_id','=',$id)->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('detalle_estado','=','1')->orderBy('detalle_fecha','asc');;
    } 
    public function seguro(){
        return $this->belongsTo(Amortizacion_Seguros::class, 'amortizacion_id', 'amortizacion_id');
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
}
