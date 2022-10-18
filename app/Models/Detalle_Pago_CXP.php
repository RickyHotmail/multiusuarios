<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_Pago_CXP extends Model
{
    use HasFactory;
    protected $table='detalle_pago_cxp';
    protected $primaryKey = 'detalle_pago_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_pago_descripcion',
        'detalle_pago_valor',       
        'detalle_pago_cuota',        
        'detalle_pago_estado',       
        'cuenta_pagar_id',    
        'pago_id'
    ];
    protected $guarded =[
    ];
    public function scopeDetallePagoCXP($query, $id){
        return $query->where('detalle_pago_cxp.detalle_pago_id','=',$id);
    }
    public function scopeCuentaPagarPagos($query, $id){
        return $query->join('cuenta_pagar','detalle_pago_cxp.cuenta_pagar_id','=','cuenta_pagar.cuenta_id')->join('pago_cxp','detalle_pago_cxp.pago_id','=','pago_cxp.pago_id')->where('cuenta_pagar.cuenta_id','=',$id);
    }
    public function scopeCuentaPagos($query, $id){
        return $query->join('cuenta_pagar','detalle_pago_cxp.cuenta_pagar_id','=','cuenta_pagar.cuenta_id')->join('pago_cxp','detalle_pago_cxp.pago_id','=','pago_cxp.pago_id')->where('detalle_pago_cxp.pago_id','=',$id);
    }
    public function scopeCuentaPagarPagosFecha($query, $cuenta_id,$fecha_ini, $fecha_fin,$todo){
        $query->join('cuenta_pagar','detalle_pago_cxp.cuenta_pagar_id','=','cuenta_pagar.cuenta_id')->join('pago_cxp','detalle_pago_cxp.pago_id','=','pago_cxp.pago_id')->where('cuenta_pagar.cuenta_id','=',$cuenta_id)->orderBy('pago_fecha','asc');
        if($todo != 1){
            $query->where('pago_fecha','>=',$fecha_ini)->where('pago_fecha','<=',$fecha_fin);
        }
        return $query;
    }
    public function scopeCuentaPagarPagosCorte($query, $cuenta_id,$fecha_corte){
        return $query->join('cuenta_pagar','detalle_pago_cxp.cuenta_pagar_id','=','cuenta_pagar.cuenta_id')->join('pago_cxp','detalle_pago_cxp.pago_id','=','pago_cxp.pago_id')->where('cuenta_pagar.cuenta_id','=',$cuenta_id)->where('pago_fecha','<=',$fecha_corte)->orderBy('pago_fecha','asc');
    }
    public function scopeCuentaPagarPagosAfterCorte($query, $cuenta_id,$fecha_corte){
        return $query->join('cuenta_pagar','detalle_pago_cxp.cuenta_pagar_id','=','cuenta_pagar.cuenta_id')->join('pago_cxp','detalle_pago_cxp.pago_id','=','pago_cxp.pago_id')->where('cuenta_pagar.cuenta_id','=',$cuenta_id)->where('pago_fecha','>',$fecha_corte)->orderBy('pago_fecha','asc');
    }
    public function cuentaPagar()
    {
        return $this->belongsTo(Cuenta_Pagar::class, 'cuenta_pagar_id', 'cuenta_id');
    }
    public function pagoCXP()
    {
        return $this->belongsTo(Pago_CXP::class, 'pago_id', 'pago_id');
    }
}
