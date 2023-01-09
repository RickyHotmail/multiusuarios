<?php

namespace App\Models;

use GuzzleHttp\Handler\Proxy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalle_Diario extends Model
{
    use HasFactory;
    protected $table='detalle_diario';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_debe',
        'detalle_haber',       
        'detalle_comentario',        
        'detalle_tipo_documento', 
        'detalle_numero_documento',
        'detalle_fecha_conciliacion',
        'detalle_conciliacion',
        'detalle_estado',
        'diario_id',
        'cuenta_id',

        'cliente_id',
        'proveedor_id',
        'empleado_id',
        'movimiento_id',

        'transferencia_id',
        'cheque_id',
        'deposito_id',
        'voucher_id',
        'centro_consumo_id'
    ];
    protected $guarded =[
    ];
    public function scopeDetalleDiarios($query){
        return $query->join('cuenta','cuenta.cuenta_id','=','detalle_diario.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('detalle_estado','=','1');
    }
    public function scopeDetalleDiario($query, $id){
        return $query->join('cuenta','cuenta.cuenta_id','=','detalle_diario.cuenta_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_id','=',$id);
    }
    public function scopeEmpleadodiario($query, $id,$empleado){
        return $query->join('cuenta','cuenta.cuenta_id','=','detalle_diario.cuenta_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario_id','=',$id)->where('empleado_id','=',$empleado);
    }

    public function scopeDetalleDiarioXdiario($query, $id){
        return $query->join('cuenta','cuenta.cuenta_id','=','detalle_diario.cuenta_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario_id','=',$id);
    }
    public function scopeDetalleDiarioXdiarioYcuenta($query, $id, $cuenta){
        return $query->join('cuenta','cuenta.cuenta_id','=','detalle_diario.cuenta_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario_id','=',$id)->where('detalle_diario.cuenta_id','=',$cuenta);
    }
    public function scopeMovimientosCuenta($query, $id, $fechaInicio, $fechaFin, $sucursal){
        if($sucursal == 0){
            return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_diario.cuenta_id','=',$id)->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin)->orderby('diario.diario_fecha');
        }else{
            return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_diario.cuenta_id','=',$id)->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin)->where('diario.sucursal_id','=',$sucursal)->orderby('diario.diario_fecha');
        }
    }
    public function scopeSaldoAnteriorCuentaSucursal($query, $id, $fecha,$sucursal){
        if($sucursal == 0){
            return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_diario.cuenta_id','=',$id)->where('diario.diario_fecha','<',$fecha);
        }else{
            return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_diario.cuenta_id','=',$id)->where('diario.diario_fecha','<',$fecha)->where('diario.sucursal_id','=',$sucursal);
        }
    }
    public function scopeMovimientosByCuenta($query, $id, $fechaInicio, $fechaFin){
        return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_diario.cuenta_id','=',$id)->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin)->orderby('diario.diario_fecha');
    }
    public function scopeMovimientosByCuentaOtros($query, $id, $fecha){
        return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_diario.cuenta_id','=',$id)
        ->where(function($query) use($fecha){
            $query->where('detalle_conciliacion','=','0')->where('diario.diario_fecha','<',$fecha);
        })
        ->orwhere(function($query) use($fecha) {
            $query->where('detalle_conciliacion','=','1')->where('diario.diario_fecha','<',$fecha)->where('detalle_fecha_conciliacion','>=',$fecha);
        })->orderby('diario.diario_fecha');
    }
    public function scopeMovimientosByTipo($query, $id, $fechaInicio, $fechaFin, $tipoDocumento){
        return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_diario.cuenta_id','=',$id)->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin)->where('detalle_tipo_documento','=',$tipoDocumento);
    }
    public function scopeMovimientosByTipoSaldo($query, $id, $fecha, $tipoDocumento){
        return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_diario.cuenta_id','=',$id)->where('diario.diario_fecha','<',$fecha)->where('detalle_tipo_documento','=',$tipoDocumento);
    }
    public function scopeSaldoAnteriorCuenta($query, $id, $fecha){
        return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_diario.cuenta_id','=',$id)->where('diario.diario_fecha','<',$fecha);
    }
    public function scopeSaldoAnteriorCuentaincial($query, $id, $fecha){
        return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_diario.cuenta_id','=',$id)->where('diario.diario_fecha','=',$fecha)->orderby('diario.diario_codigo');
    }
    public function scopeSaldoActualCuentaByFecha($query, $id, $fechaInicio, $fechaFin){
        return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_diario.cuenta_id','=',$id)->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin);
    }
    public function scopeSaldoActualByFecha($query, $numero, $fechaInicio, $fechaFin){
        if($fechaInicio == '0'){
            return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->join('cuenta','cuenta.cuenta_id','=','detalle_diario.cuenta_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('cuenta.cuenta_numero','like',"".$numero."%")->where('diario.diario_fecha','<=',$fechaFin);
        }else{
            return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->join('cuenta','cuenta.cuenta_id','=','detalle_diario.cuenta_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('cuenta.cuenta_numero','like',"".$numero."%")->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin);
        }
    }
    public function scopeSaldoActualByFechaCuenta($query, $cuenta, $fechaInicio, $fechaFin){
        if($fechaInicio == '0'){
            return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->join('cuenta','cuenta.cuenta_id','=','detalle_diario.cuenta_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('cuenta.cuenta_id','=',$cuenta)->where('diario.diario_fecha','<=',$fechaFin);
        }else{
            return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->join('cuenta','cuenta.cuenta_id','=','detalle_diario.cuenta_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('cuenta.cuenta_id','=',$cuenta)->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin);
        }
    }
    public function scopeSaldoActualCuenta($query, $id, $fecha){
        return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_diario.cuenta_id','=',$id)->where('diario.diario_fecha','<=',$fecha);
    }
    public function scopeMayorCliente($query, $cliente_id,$fechaInicio, $fechaFin,$sucursal){
        $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin)->where('detalle_diario.cliente_id','=',$cliente_id)->orderBy('diario.diario_fecha','asc');
        if($sucursal != 0){
            $query->where('diario.sucursal_id','=',$sucursal);
        }
        return $query;    
    }
    public function scopeMayorCliente2($query, $cliente_id,$fechaInicio, $fechaFin,$cuenta,$sucursal){
        $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin)->where('detalle_diario.cuenta_id','=',$cuenta)->where('detalle_diario.cliente_id','=',$cliente_id)->orderBy('diario.diario_fecha','asc');
        if($sucursal != 0){
            $query->where('diario.sucursal_id','=',$sucursal);
        }
        return $query;    
    }
    public function scopeMayorClienteCuenta($query, $cliente_id,$fechaInicio, $fechaFin,$sucursal,$cuenta){
        $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin)->where('detalle_diario.cliente_id','=',$cliente_id)->orderBy('diario.diario_fecha','asc');
        if($sucursal != 0){
            $query->where('diario.sucursal_id','=',$sucursal);
        }
        if($cuenta != 0){
            $query->where('detalle_diario.cuenta_id','=',$cuenta);
        }
        return $query;    
    }
    public function scopeMayorClienteAnt($query, $cliente_id,$fechaInicio,$sucursal){
        $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_fecha','<',$fechaInicio)->where('detalle_diario.cliente_id','=',$cliente_id);
        if($sucursal != 0){
            $query->where('diario.sucursal_id','=',$sucursal);
        }
        return $query;
    }
    public function scopeMayorClienteAntCuenta($query, $cliente_id,$fechaInicio,$sucursal,$cuenta){
        $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_fecha','<',$fechaInicio)->where('detalle_diario.cliente_id','=',$cliente_id);
        if($sucursal != 0){
            $query->where('diario.sucursal_id','=',$sucursal);
        }
        if($cuenta != 0){
            $query->where('detalle_diario.cuenta_id','=',$cuenta);
        }
        return $query;
    }
    public function scopeMayorProveedor($query, $proveedor_id,$fechaInicio, $fechaFin){
        return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin)->where('detalle_diario.proveedor_id','=',$proveedor_id)->orderBy('diario.diario_fecha','asc');
    }
    public function scopeMayorProveedor2($query, $proveedor_id,$fechaInicio, $fechaFin,$cuenta,$sucursal){
         $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin)->where('detalle_diario.cuenta_id','=',$cuenta)->where('detalle_diario.proveedor_id','=',$proveedor_id)->orderBy('diario.diario_fecha','asc');
        if($sucursal != 0){
            $query->where('diario.sucursal_id','=',$sucursal);
        }
        return $query;
    }
    public function scopeMayorProveedorCuenta($query, $proveedor_id,$fechaInicio, $fechaFin,$cuenta){
        $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_fecha','>=',$fechaInicio)->where('diario.diario_fecha','<=',$fechaFin)->where('detalle_diario.proveedor_id','=',$proveedor_id)->orderBy('diario.diario_fecha','asc');
        if($cuenta != 0){
            $query->where('detalle_diario.cuenta_id','=',$cuenta);
        }
        return $query;
    }
    public function scopeMayorProveedorAnt($query, $proveedor_id,$fechaInicio){
        return $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_fecha','<',$fechaInicio)->where('detalle_diario.proveedor_id','=',$proveedor_id);
    }
    public function scopeMayorProveedorAnt2($query, $proveedor_id,$fechaInicio,$cuenta,$sucursal){
         $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_fecha','<',$fechaInicio)->where('detalle_diario.cuenta_id','=',$cuenta)->where('detalle_diario.proveedor_id','=',$proveedor_id);
         if($sucursal != 0){
            $query->where('diario.sucursal_id','=',$sucursal);
        }
        return $query;
    }
    public function scopeMayorProveedorAntCuenta($query, $proveedor_id,$fechaInicio,$cuenta){
        $query->join('diario','diario.diario_id','=','detalle_diario.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('diario.diario_fecha','<',$fechaInicio)->where('detalle_diario.proveedor_id','=',$proveedor_id);
        if($cuenta != 0){
            $query->where('detalle_diario.cuenta_id','=',$cuenta);
        }
        return $query;
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function cuenta(){
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'cuenta_id');
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function sucursal(){
        return $this->hasOneThrough(Sucursal::class, Diario::class,'diario_id','sucursal_id','diario_id','sucursal_id');
    }
    public function cheque(){
        return $this->belongsTo(Cheque::class, 'cheque_id', 'cheque_id');
    }
    public function transferencia(){
        return $this->belongsTo(Transferencia::class, 'transferencia_id', 'transferencia_id');
    }
    public function deposito(){
        return $this->belongsTo(Deposito::class, 'deposito_id', 'deposito_id');
    }
    public function voucher(){
        return $this->belongsTo(Voucher::class, 'voucher_id', 'voucher_id');
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }
    public function proveedor(){
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }
    public function empleado(){
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }
    public function movimientoProducto(){
        return $this->belongsTo(Movimiento_Producto::class, 'movimiento_id', 'movimiento_id');
    }
}
