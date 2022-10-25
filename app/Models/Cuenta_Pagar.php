<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cuenta_Pagar extends Model
{ 
    use HasFactory;
    protected $table='cuenta_pagar';
    protected $primaryKey = 'cuenta_id';
    public $timestamps=true;
    protected $fillable = [
        'cuenta_descripcion',
        'cuenta_tipo',
        'cuenta_fecha',         
        'cuenta_fecha_inicio',       
        'cuenta_fecha_fin',    
        'cuenta_monto',
        'cuenta_saldo',
        'cuenta_saldom',
        'cuenta_valor_factura',
        'cuenta_estado',
        'proveedor_id',
        'sucursal_id'
    ];
    protected $guarded =[
    ];
    public function transaccionCompra()
    {
        return $this->hasOne(Transaccion_Compra::class, 'cuenta_id', 'cuenta_id');
    }
    public function liquidacionCompra()
    {
        return $this->hasOne(Liquidacion_Compra::class, 'cuenta_id', 'cuenta_id');
    }
    public function ingresoBodega()
    {
        return $this->hasOne(Ingreso_Bodega::class, 'cuenta_id', 'cuenta_id');
    }
    public function pagos(){
        return $this->hasMany(Detalle_Pago_CXP::class, 'cuenta_id', 'cuenta_pagar_id');
    }
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }
    public function scopeCuentaByNumero($query, $numeroFactura, $proveedor_id){
        return $query->join('proveedor','proveedor.proveedor_id','=','cuenta_pagar.proveedor_id')
            ->join('sucursal','sucursal.sucursal_id','=','cuenta_pagar.sucursal_id')
            ->select('cuenta_pagar.cuenta_id','cuenta_pagar.cuenta_descripcion','cuenta_pagar.cuenta_valor_factura','cuenta_pagar.cuenta_saldo','cuenta_pagar.cuenta_fecha','proveedor.proveedor_nombre','proveedor.proveedor_ruc','proveedor.proveedor_id')
            ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_descripcion','like','%'.$numeroFactura.'%')
            ->where('cuenta_estado','=','1')->where('cuenta_pagar.proveedor_id','=',$proveedor_id)
            ->havingRaw("(SELECT transaccion_compra.transaccion_id FROM transaccion_compra WHERE transaccion_compra.cuenta_id = cuenta_pagar.cuenta_id) is null")
            ->groupBy('cuenta_pagar.cuenta_id','cuenta_pagar.cuenta_descripcion','cuenta_pagar.cuenta_valor_factura','cuenta_pagar.cuenta_saldo','cuenta_pagar.cuenta_fecha','proveedor.proveedor_nombre','proveedor.proveedor_ruc','proveedor.proveedor_id')
            ->orderBy('cuenta_pagar.cuenta_fecha','asc');
    }
    public function scopeCuentaByFacturaMigrada($query, $facturaMigrada){
        return $query->join('proveedor','proveedor.proveedor_id','=','cuenta_pagar.proveedor_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_pagar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_descripcion','like','%'.$facturaMigrada.'%');
    }
    public function scopeCuenta($query, $id){
        return $query->join('proveedor','proveedor.proveedor_id','=','cuenta_pagar.proveedor_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_pagar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_id','=',$id)->orderBy('proveedor.proveedor_nombre','asc');
    }
    public function scopeProveedoresCXPSucursal($query,$sucursal_id){
        $query->join('proveedor','proveedor.proveedor_id','=','cuenta_pagar.proveedor_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_pagar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_estado','=','1')->orderBy('proveedor.proveedor_nombre','asc');
        if($sucursal_id != 0){
            $query->where('sucursal.sucursal_id','=',$sucursal_id);
        }
        return $query;
    }
    public function scopeProveedoresCXP($query){
        return $query->join('proveedor','proveedor.proveedor_id','=','cuenta_pagar.proveedor_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_pagar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_estado','=','1')->orderBy('proveedor.proveedor_nombre_comercial','asc');
    }
    public function scopeCuentasProveedores($query, $id){
        return $query->join('proveedor','proveedor.proveedor_id','=','cuenta_pagar.proveedor_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_pagar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('proveedor.proveedor_id','=',$id)->orderBy('cuenta_pagar.cuenta_fecha','asc');
    }
    public function scopeCuentaPagarPagos($query, $id){
        return $query->join('detalle_pago_cxp','detalle_pago_cxp.cuenta_pagar_id','=','cuenta_pagar.cuenta_id')->where('cuenta_pagar.cuenta_id','=',$id);
    }
    public function scopeCuentasDeudas($query, $proveedor_id, $fechaI,$fechaF,$todo,$sucursal_id,$credito,$contado,$efectivo,$otro){
        $query->join('proveedor','proveedor.proveedor_id','=','cuenta_pagar.proveedor_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_pagar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_pagar.proveedor_id','=',$proveedor_id);
        if($todo == 0){
            $query->where('cuenta_fecha','>=',$fechaI)->where('cuenta_fecha','<=',$fechaF);
        }
        if($sucursal_id <> 0){
            $query->where('sucursal.sucursal_id','=',$sucursal_id);
        }
        $query->where(function($query) use($contado,$credito,$efectivo,$otro){
            if($contado == 'on'){
                $query->orwhere('cuenta_tipo','=','CONTADO');
            }
            if($credito == 'on'){
                $query->orwhere('cuenta_tipo','=','CREDITO');
            }
            if($efectivo == 'on'){
                $query->orwhere('cuenta_tipo','=','EN EFECTIVO');
            }
            if($otro == 'on'){
                $query->orwhere('cuenta_tipo','=','OTRO');
            }
        });
        return $query;
    }
    public function scopeCuentasByProveedor($query, $proveedor_id,$sucursal){
        $query->join('proveedor','proveedor.proveedor_id','=','cuenta_pagar.proveedor_id')
        ->join('sucursal','sucursal.sucursal_id','=','cuenta_pagar.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_pagar.proveedor_id','=',$proveedor_id)
        ->where('cuenta_estado','=','1')->orderBy('cuenta_pagar.cuenta_fecha','asc');
        if($sucursal != 0){
            $query->where('cuenta_pagar.sucursal_id','=',$sucursal);
        }
        return $query;
    }
    public function scopeCuentasPagarByPagos($query,$fecha_ini,$fecha_fin,$proveedor_id,$todo,$sucursal){
        $query->join('detalle_pago_cxp','cuenta_pagar.cuenta_id','=','detalle_pago_cxp.cuenta_pagar_id')->join('pago_cxp','detalle_pago_cxp.pago_id','=','pago_cxp.pago_id')->join('proveedor','proveedor.proveedor_id','=','cuenta_pagar.proveedor_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_pagar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_pagar.proveedor_id','=',$proveedor_id)->orderBy('cuenta_pagar.cuenta_fecha','asc')->orderBy('cuenta_pagar.cuenta_id', 'asc');
        if($sucursal != '0'){
            $query->where('sucursal.sucursal_id','=',$sucursal);
        }
        if($todo != 1){
            $query->where('pago_fecha','>=',$fecha_ini)->where('pago_fecha','<=',$fecha_fin);
        }
        return $query;
    }
    public function scopeCuentasPagarPendientes($query,$fecha_corte,$proveedor_id,$sucursal){
        $query->join('proveedor','proveedor.proveedor_id','=','cuenta_pagar.proveedor_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_pagar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_pagar.cuenta_saldo','>', 0)->where('cuenta_pagar.proveedor_id','=',$proveedor_id)->where('cuenta_fecha','<=',$fecha_corte)->orderBy('cuenta_pagar.cuenta_fecha','asc')->orderBy('cuenta_pagar.cuenta_id', 'asc');
        if($sucursal != '0'){
            $query->where('sucursal.sucursal_id','=',$sucursal);
        }
        return $query;
    }
    public function scopeCuentasPagarPendientesCorte($query,$fecha_corte,$proveedor_id,$sucursal){
        $query->join('proveedor','proveedor.proveedor_id','=','cuenta_pagar.proveedor_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_pagar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_pagar.proveedor_id','=',$proveedor_id)->where('cuenta_fecha','<=',$fecha_corte)->orderBy('cuenta_pagar.cuenta_fecha','asc')->orderBy('cuenta_pagar.cuenta_id', 'asc');
        if($sucursal != '0'){
            $query->where('sucursal.sucursal_id','=',$sucursal);
        }
        return $query;
    }
    public function scopeScucursalesxCXP($query,$proveedor_id){
        return $query->join('proveedor','proveedor.proveedor_id','=','cuenta_pagar.proveedor_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_pagar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_estado','=','1')->where('cuenta_pagar.proveedor_id','=',$proveedor_id);
    }
}
