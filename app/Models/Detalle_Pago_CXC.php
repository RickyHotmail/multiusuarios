<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_Pago_CXC extends Model
{
    use HasFactory;
    protected $table='detalle_pago_cxc';
    protected $primaryKey = 'detalle_pago_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_pago_descripcion',
        'detalle_pago_valor',       
        'detalle_pago_cuota',        
        'detalle_pago_estado',       
        'cuenta_id',    
        'pago_id'
    ];
    protected $guarded =[
    ];
    
    public function scopeDetallePago($query, $id){
        return $query->where('detalle_pago_id','=',$id);
    }
    public function scopeCuentaCobrarPagos($query, $id){
        return $query->join('cuenta_cobrar','detalle_pago_cxc.cuenta_id','=','cuenta_cobrar.cuenta_id')->join('pago_cxc','detalle_pago_cxc.pago_id','=','pago_cxc.pago_id')->where('cuenta_cobrar.cuenta_id','=',$id);
    }
    public function scopeCuentaCobrarPagosFecha($query, $cuenta_id,$fecha_ini, $fecha_fin,$todo){
        $query->join('cuenta_cobrar','detalle_pago_cxc.cuenta_id','=','cuenta_cobrar.cuenta_id')
              ->join('pago_cxc','detalle_pago_cxc.pago_id','=','pago_cxc.pago_id')
              ->where('cuenta_cobrar.cuenta_id','=',$cuenta_id)
              ->orderBy('pago_fecha','asc');
        
        if($todo != 1){
            $query->where('pago_fecha','>=',$fecha_ini)->where('pago_fecha','<=',$fecha_fin);
        }
        return $query;
    }
    public function scopeCuentaCobrarPagosCorte($query, $cuenta_id,$fecha_corte){
        return $query->join('cuenta_cobrar','detalle_pago_cxc.cuenta_id','=','cuenta_cobrar.cuenta_id')->join('pago_cxc','detalle_pago_cxc.pago_id','=','pago_cxc.pago_id')->where('cuenta_cobrar.cuenta_id','=',$cuenta_id)->where('pago_fecha','<=',$fecha_corte)->orderBy('pago_fecha','asc');
    }
    public function scopeCuentaCobrarPagosAfterCorte($query, $cuenta_id,$fecha_corte){
        return $query->join('cuenta_cobrar','detalle_pago_cxc.cuenta_id','=','cuenta_cobrar.cuenta_id')->join('pago_cxc','detalle_pago_cxc.pago_id','=','pago_cxc.pago_id')->where('cuenta_cobrar.cuenta_id','=',$cuenta_id)->where('pago_fecha','>',$fecha_corte)->orderBy('pago_fecha','asc');
    }
    public function cuentaCobrar()
    {
        return $this->belongsTo(Cuenta_Cobrar::class, 'cuenta_id', 'cuenta_id');
    }
    public function pagoCXC()
    {
        return $this->belongsTo(Pago_CXC::class, 'pago_id', 'pago_id');
    }
}
