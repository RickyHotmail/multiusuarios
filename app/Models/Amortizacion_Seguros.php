<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Amortizacion_Seguros extends Model
{
    use HasFactory;
    protected $table ='amortizacion_seguros';
    protected $primaryKey = 'amortizacion_id';
    public $timestamps = true;
    protected $fillable = [        
        'amortizacion_fecha',
        'amortizacion_periodo', 
        'amortizacion_total',
        'amortizacion_pago_total',
        'amortizacion_observacion',
        'amortizacion_estado',
        'cuenta_debe',
        'transaccion_id',
        'sucursal_id',
    ];
    protected $guarded =[
    ];
    public function scopeseguros($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','amortizacion_seguros.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('amortizacion_estado','=','1')->orderBy('amortizacion_fecha','asc');
    }
    public function scopesegurosdistinc($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','amortizacion_seguros.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
    }
    public function scopeseguro($query,$id){
        return $query->join('sucursal','sucursal.sucursal_id','=','amortizacion_seguros.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('amortizacion_id','=',$id);
    }
    public function scopesegurosucursal($query,$id,$estado){
        $query->join('sucursal','sucursal.sucursal_id','=','amortizacion_seguros.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
        if($id != '0'){
            $query->where('amortizacion_seguros.sucursal_id','=',$id);
        }
        if($estado != '0'){
            $query->where('amortizacion_estado','=',$estado);
        }
        return $query->orderBy('amortizacion_fecha','asc');
    }
    public function detalles(){
        return $this->hasMany(Detalle_Amortizacion::class, 'detalle_id', 'detalle_id');
    }
    public function transaccionCompra(){
        return $this->belongsTo(Transaccion_Compra::class, 'transaccion_id', 'transaccion_id');
    }
    public function cuentadebe(){
        return $this->belongsTo(Cuenta::class, 'cuenta_debe', 'cuenta_id');
    }
    public function sucursal(){
        return $this->belongsTo(sucursal::class, 'sucursal_id', 'sucursal_id');
    }
}
