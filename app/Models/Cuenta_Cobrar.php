<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cuenta_Cobrar extends Model
{
    use HasFactory;
    protected $table='cuenta_cobrar';
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
        'cuenta_cheque_anticipado',
        'cuenta_banco_anticipado',
        'cuenta_estado',
        'cliente_id',
        'sucursal_id'
    ];
    protected $guarded =[
    ];

    public function facturaVenta()
    {
        return $this->hasOne(Factura_Venta::class, 'cuenta_id', 'cuenta_id');
    }
    public function notaEntrega()
    {
        return $this->hasOne(Nota_Entrega::class, 'cuenta_id', 'cuenta_id');
    }
    public function notaDebito()
    {
        return $this->hasOne(Nota_Debito::class, 'cuenta_id', 'cuenta_id');
    }
    public function detallepago()
    {
        return $this->hasMany(Detalle_Pago_CXC::class, 'cuenta_id', 'cuenta_id');
    }
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }
    
    public function scopeCuentaByNumero($query, $numeroFactura){
        return $query->join('cliente','cliente.cliente_id','=','cuenta_cobrar.cliente_id')
            ->join('sucursal','sucursal.sucursal_id','=','cuenta_cobrar.sucursal_id')
            ->select('cuenta_cobrar.cuenta_id','cuenta_cobrar.cuenta_descripcion','cuenta_cobrar.cuenta_valor_factura','cuenta_cobrar.cuenta_saldo','cuenta_cobrar.cuenta_fecha','cliente.cliente_nombre','cliente.cliente_cedula','cliente.cliente_id')
            ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_descripcion','like','%'.$numeroFactura.'%')
            ->where('cuenta_estado','=','1')
            ->orderBy('cuenta_cobrar.cuenta_fecha','asc');
    }
    public function scopeCuenta($query, $id){
        return $query->join('cliente','cliente.cliente_id','=','cuenta_cobrar.cliente_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_cobrar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_id','=',$id)->orderBy('cliente.cliente_nombre','asc');
    }
    public function scopeClientesCXC($query){
        return $query->join('cliente','cliente.cliente_id','=','cuenta_cobrar.cliente_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_cobrar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_estado','=','1')->orderBy('cliente.cliente_nombre','asc');
    }
    public function scopeClientesCXCSucursal($query,$sucursal_id){
        $query->join('cliente','cliente.cliente_id','=','cuenta_cobrar.cliente_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_cobrar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_estado','=','1')->orderBy('cliente.cliente_nombre','asc');
        if($sucursal_id != 0){
            $query->where('sucursal.sucursal_id','=',$sucursal_id);
        }
        return $query;
    }
    public function scopeCuentasClientes($query, $id){
        return $query->join('cliente','cliente.cliente_id','=','cuenta_cobrar.cliente_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_cobrar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_cobrar.cliente_id','=',$id)->orderBy('cuenta_cobrar.cuenta_fecha','asc');
    }
    public function scopeCuentaCobrarPagos($query, $id){
        return $query->join('detalle_pago_cxc','detalle_pago_cxc.cuenta_id','=','cuenta_cobrar.cuenta_id')->where('cuenta_cobrar.cuenta_id','=',$id);
    }
    public function scopeCuentasCartera($query, $cliente_id, $fechaI,$fechaF,$todo,$sucursal_id,$credito,$contado,$efectivo){
        $query->join('cliente','cliente.cliente_id','=','cuenta_cobrar.cliente_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_cobrar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_cobrar.cliente_id','=',$cliente_id);
        if($todo == 0){
            $query->where('cuenta_fecha','>=',$fechaI)->where('cuenta_fecha','<=',$fechaF);
        }
        if($sucursal_id != 0){
            $query->where('sucursal.sucursal_id','=',$sucursal_id);
        }
        $query->where(function($query) use($contado,$credito,$efectivo){
            if($contado == 'on'){
                $query->orwhere('cuenta_tipo','=','CONTADO');
            }
            if($credito == 'on'){
                $query->orwhere('cuenta_tipo','=','CREDITO');
            }
            if($efectivo == 'on'){
                $query->orwhere('cuenta_tipo','=','EN EFECTIVO');
            }
        });
        return $query;
    }
    public function scopeCuentasByCliente($query, $cliente_id,$sucursal){
        $query->join('cliente','cliente.cliente_id','=','cuenta_cobrar.cliente_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_cobrar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_cobrar.cliente_id','=',$cliente_id)->where('cuenta_estado','=','1')->orderBy('cuenta_cobrar.cuenta_fecha','asc')->orderBy('cuenta_cobrar.cuenta_id','asc');
        if($sucursal != 0){
            $query->where('sucursal.sucursal_id','=',$sucursal);
        }
        return $query;
    }
    public function scopeCuentasCobrarByPagos($query,$fecha_ini,$fecha_fin,$cliente_id,$todo,$sucursal){
        $query->join('detalle_pago_cxc','cuenta_cobrar.cuenta_id','=','detalle_pago_cxc.cuenta_id')->join('pago_cxc','detalle_pago_cxc.pago_id','=','pago_cxc.pago_id')->join('cliente','cliente.cliente_id','=','cuenta_cobrar.cliente_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_cobrar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_cobrar.cliente_id','=',$cliente_id)->orderBy('cuenta_cobrar.cuenta_fecha','asc')->orderBy('cuenta_cobrar.cuenta_id', 'asc');
        if($sucursal != '0'){
            $query->where('sucursal.sucursal_id','=',$sucursal);
        }
        if($todo != 1){
            $query->where('pago_fecha','>=',$fecha_ini)->where('pago_fecha','<=',$fecha_fin);
        }
        return $query;
    }
    public function scopeCuentasCobrarPendientes($query,$fecha_corte,$cliente_id,$sucursal){
        $query->join('cliente','cliente.cliente_id','=','cuenta_cobrar.cliente_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_cobrar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_cobrar.cliente_id','=',$cliente_id)->where('cuenta_fecha','<=',$fecha_corte)->orderBy('cuenta_cobrar.cuenta_fecha','asc')->orderBy('cuenta_cobrar.cuenta_id', 'asc');
        if($sucursal != '0'){
            $query->where('sucursal.sucursal_id','=',$sucursal);
        }
        return $query;
    }
    public function scopeScucursalesxCXC($query,$cliente_id){
        return $query->join('cliente','cliente.cliente_id','=','cuenta_cobrar.cliente_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_cobrar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_estado','=','1')->where('cuenta_cobrar.cliente_id','=',$cliente_id);
    }
    public function scopeCuentaByFacturaMigrada($query, $facturaMigrada){
        return $query->join('cliente','cliente.cliente_id','=','cuenta_cobrar.cliente_id')->join('sucursal','sucursal.sucursal_id','=','cuenta_cobrar.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_descripcion','like','%'.$facturaMigrada.'%');
    }
}
