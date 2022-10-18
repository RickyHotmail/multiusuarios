<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transaccion_Compra extends Model
{
    use HasFactory;
    protected $table='transaccion_compra';
    protected $primaryKey = 'transaccion_id';
    public $timestamps=true;
    protected $fillable = [
        'transaccion_fecha',
        'transaccion_caducidad',         
        'transaccion_impresion',       
        'transaccion_vencimiento',    
        'transaccion_inventario',
        'transaccion_numero',
        'transaccion_serie',
        'transaccion_secuencial',
        'transaccion_subtotal',
        'transaccion_descuento',
        'transaccion_tarifa0',
        'transaccion_tarifa12',
        'transaccion_iva',
        'transaccion_total',
        'transaccion_ivaB',
        'transaccion_ivaS',
        'transaccion_dias_plazo',
        'transaccion_descripcion',
        'transaccion_tipo_pago',
        'transaccion_porcentaje_iva',
        'transaccion_autorizacion',
        'transaccion_estado',
        'proveedor_id',
        'tipo_comprobante_id',
        'sustento_id',
        'diario_id',
        'forma_pago_id',
        'cuenta_id',
        'sucursal_id',
        'transaccion_id_f',
        'arqueo_id',
        'transaccion_factura_manual',
        'transaccion_autorizacion_manual'
    ];
    protected $guarded =[
    ];
    public function scopeTransaccionDuplicada($query, $numero,$tipoDocumento,$proveedor){
        return $query->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')
        ->where('empresa_id','=',Auth::user()->empresa_id)
        ->where('transaccion_compra.transaccion_numero','=',$numero)
        ->where('transaccion_compra.proveedor_id','=',$proveedor)
        ->where('transaccion_compra.tipo_comprobante_id','=',$tipoDocumento)
        ->where('transaccion_estado','=','1');
    }
    public function scopeTransaccionDuplicadaActualizar($query, $numero,$tipoDocumento,$proveedor,$transaccionID){
        return $query->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')
        ->where('empresa_id','=',Auth::user()->empresa_id)
        ->where('transaccion_compra.transaccion_numero','=',$numero)
        ->where('transaccion_compra.proveedor_id','=',$proveedor)
        ->where('transaccion_compra.tipo_comprobante_id','=',$tipoDocumento)
        ->where('transaccion_compra.transaccion_id','<>',$transaccionID)
        ->where('transaccion_estado','=','1');
    }
    public function scopeTransacciones($query, $proveedor_id){
        return $query->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('transaccion_compra.proveedor_id','=',$proveedor_id)->where('transaccion_estado','=','1')->orderBy('transaccion_numero','desc');
    }
    public function scopeTransaccionactiva($query, $proveedor_id){
        return $query->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')->join('cuenta_pagar','cuenta_pagar.cuenta_id','=','transaccion_compra.cuenta_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('transaccion_compra.proveedor_id','=',$proveedor_id)->where('transaccion_estado','=','1')->where('cuenta_estado','=','1')->orderBy('transaccion_numero','desc');
    }
    public function scopeTransaccionID($query, $transaccion_id){
        return $query->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('transaccion_compra.transaccion_id','=',$transaccion_id)->where('transaccion_estado','=','1')->orderBy('transaccion_numero','desc');
    }
    public function scopeReporteTransacciones($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','transaccion_compra.sucursal_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('transaccion_estado','=','1');
    }
    public function scopetransaccionesSoloFacturas($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','transaccion_compra.sucursal_id')
                     ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')
                     ->where('tipo_comprobante.tipo_comprobante_nombre','=', 'Factura')
                     ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
                     ->where('transaccion_estado','=','1');
    }
    public function scopeSucursalDistinsc($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','transaccion_compra.sucursal_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');
    }
    public function scopeProveedorDistinsc($query){
        return $query->join('proveedor','transaccion_compra.proveedor_id','=','proveedor.proveedor_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->orderBy('proveedor_nombre','asc');
    }
    public function scopeTransaccionesalimentacion($query,$numeroFactura,$proveedor){
        return $query->join('proveedor','transaccion_compra.proveedor_id','=','proveedor.proveedor_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('transaccion_estado','=','1')->where('transaccion_compra.proveedor_id','=',$proveedor)->where('transaccion_numero','like','%'.$numeroFactura.'%');
    }
    public function scopeTransaccion($query, $id){
        return $query->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('transaccion_id','=',$id)->orderBy('transaccion_numero','desc');
    }
    public function scopeTransaccionpunto($query, $id){
        return $query->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')->join('rango_documento','rango_documento.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('transaccion_id','=',$id)->orderBy('transaccion_numero','desc');
    }
    public function scopeTransaccionByFecha($query, $fechaInicio, $fechaFin){
        return $query->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('transaccion_fecha','>=',$fechaInicio)->where('transaccion_fecha','<=',$fechaFin);
    }
    public function scopeTransaccionSinFecha($query){
        return $query->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')
        ->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id);
    }
    public function scopeMovimientoCConsumo($query, $cc, $fechaInicio, $fechaFin){
        $query->join('detalle_tc','detalle_tc.transaccion_id','=','transaccion_compra.transaccion_id')
        ->join('producto','producto.producto_id','=','detalle_tc.producto_id')
        ->join('centro_consumo','centro_consumo.centro_consumo_id','=','detalle_tc.centro_consumo_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')
        ->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)
        ->where('transaccion_fecha','>=',$fechaInicio)
        ->where('transaccion_fecha','<=',$fechaFin);
        if($cc != '0'){
            $query->where('centro_consumo.centro_consumo_id','=',$cc);
        } 
        return $query; 
    }
    public function scopeFacturaNumeroAnt($query, $numeroFactura, $proveedor){
        return $query->join('cuenta_pagar','transaccion_compra.cuenta_id','=','cuenta_pagar.cuenta_id')->join('proveedor','proveedor.proveedor_id','=','transaccion_compra.proveedor_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('transaccion_compra.proveedor_id','=',$proveedor)->where('transaccion_estado','=','1')->where('transaccion_numero','like','%'.$numeroFactura.'%')->orderBy('transaccion_numero','desc');
    }
    public function scopeTransaccionFiltrar($query, $fechatodo, $fechaInicio, $fechaFin,$numeroDoc,$sucursal,$proveedor){
         $query->join('proveedor','proveedor.proveedor_id','=','transaccion_compra.proveedor_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')
        ->join('sucursal','sucursal.sucursal_id','=','transaccion_compra.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('transaccion_numero','like','%'.$numeroDoc.'%');
        if($proveedor != '0'){
            $query->where('proveedor.proveedor_id','=',$proveedor);
        } 
        if($sucursal != '0'){
            $query->where('sucursal.sucursal_id','=',$sucursal);
        } 
        if($fechatodo != 'on'){
            $query->where('transaccion_fecha','>=',$fechaInicio)
            ->where('transaccion_fecha','<=',$fechaFin);
        }
       
       
        return $query; 
    }
    public function scopeTransaccionOnFiltrar($query,$numeroDoc){
        return $query->join('proveedor','proveedor.proveedor_id','=','transaccion_compra.proveedor_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')
        ->join('sucursal','sucursal.sucursal_id','=','transaccion_compra.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('transaccion_numero','like','%'.$numeroDoc.'%');
    }
    public function scopeTransaccionFechaFiltrar($query, $fechaInicio, $fechaFin,$numeroDoc){
        return $query->join('proveedor','proveedor.proveedor_id','=','transaccion_compra.proveedor_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')
        ->join('sucursal','sucursal.sucursal_id','=','transaccion_compra.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('transaccion_fecha','>=',$fechaInicio)
        ->where('transaccion_fecha','<=',$fechaFin)
        ->where('transaccion_numero','like','%'.$numeroDoc.'%');
    }
    public function scopeTransaccionSucursalFiltrar($query,$numeroDoc,$sucursal){
        return $query->join('proveedor','proveedor.proveedor_id','=','transaccion_compra.proveedor_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')
        ->join('sucursal','sucursal.sucursal_id','=','transaccion_compra.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal.sucursal_nombre','=',$sucursal)
        ->where('transaccion_numero','like','%'.$numeroDoc.'%');
    }
    public function scopeTransaccionByAutorizacion($query, $autorizacion){
        return $query->join('sucursal','sucursal.sucursal_id','=','transaccion_compra.sucursal_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','transaccion_compra.tipo_comprobante_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('transaccion_estado','=','1')
        ->where('transaccion_autorizacion','=',$autorizacion);
    }
    public function detalles(){
        return $this->hasMany(Detalle_TC::class, 'transaccion_id', 'transaccion_id');
    }
    public function activo(){
        return $this->belongsTo(Activo_Fijo::class, 'transaccion_id', 'transaccion_id');
    }
    public function arqueoCaja(){
        return $this->belongsTo(Arqueo_Caja::class, 'arqueo_id', 'arqueo_id');
    }
    public function cuentaPagar(){
        return $this->belongsTo(Cuenta_Pagar::class, 'cuenta_id', 'cuenta_id');
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }
    public function proveedor(){
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }
    public function tipoComprobante(){
        return $this->belongsTo(Tipo_Comprobante::class, 'tipo_comprobante_id', 'tipo_comprobante_id');
    }
    public function sustentoTributario(){
        return $this->belongsTo(Sustento_Tributario::class, 'sustento_id', 'sustento_id');
    }
    public function facturaModificar(){
        return $this->belongsTo(transaccion_compra::class, 'transaccion_id_f', 'transaccion_id');
    }
    public function notas_d_c(){
        return $this->hasMany(transaccion_compra::class, 'transaccion_id_f', 'transaccion_id');
    }
    public function ordenrecepcion(){
        return $this->hasMany(Orden_Recepcion::class, 'transaccion_id', 'transaccion_id');
    }
    public function retencionCompra()
    {
        return $this->hasOne(Retencion_Compra::class, 'transaccion_id', 'transaccion_id');
    }
    public function formaPago(){
        return $this->belongsTo(Forma_Pago::class, 'forma_pago_id', 'forma_pago_id');
    }
    public function empresa(){
        return $this->hasOneThrough(Empresa::class, Tipo_Comprobante::class,'tipo_comprobante_id','empresa_id','tipo_comprobante_id','empresa_id');
    }
}
