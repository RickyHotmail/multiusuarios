<?php

namespace App\Models;

use Facade\FlareClient\Http\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Factura_Venta extends Model
{
    use HasFactory;
    protected $table='factura_venta';
    protected $primaryKey = 'factura_id';
    public $timestamps=true;
    protected $fillable = [
        'factura_numero',
        'factura_serie',
        'factura_secuencial', 
        'factura_fecha',
        'factura_lugar',
        'factura_tipo_pago',
        'factura_dias_plazo', 
        'factura_fecha_pago', 
        'factura_subtotal',
        'factura_descuento',
        'factura_tarifa0', 
        'factura_tarifa12',  
        'factura_iva',  
        'factura_total',  
        'factura_comentario',
        'factura_porcentaje_iva',
        'factura_emision',
        'factura_ambiente',
        'factura_autorizacion',
        'factura_xml_nombre',
        'factura_xml_estado',
        'factura_xml_mensaje',
        'factura_xml_respuestaSRI',
        'factura_xml_fecha',
        'factura_xml_hora',
        'factura_estado',
        'bodega_id',
        'cliente_id',
        'diario_id',
        'forma_pago_id',
        'rango_id',
        'cuenta_id',
        'vendedor_id',
        'documento_anulado_id',
        'diario_costo_id',
        'arqueo_id',
    ];
    protected $guarded =[
    ];
    public function scopeFacturas($query){
        return $query->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('factura_estado','=','1')->orderBy('factura_numero','desc');
    }
    public function scopeBusqueda($query, $fechatodo, $desde, $hasta, $cliente, $bodega, $sucursal){
        $query->join('cliente', 'cliente.cliente_id', '=', 'factura_venta.cliente_id')
        ->join('bodega', 'bodega.bodega_id', '=', 'factura_venta.bodega_id')
        ->join('sucursal', 'sucursal.sucursal_id', '=', 'bodega.sucursal_id')
        ->join('rango_documento', 'rango_documento.rango_id', '=', 'factura_venta.rango_id')
        ->join('punto_emision', 'punto_emision.punto_id', '=', 'rango_documento.punto_id')
        ->where('sucursal.empresa_id', '=', Auth::user()->empresa_id)
        ->where('factura_venta.factura_estado','<>','2');
        if($fechatodo != 'on'){
            $query ->where('factura_fecha', '>=', $desde)->where('factura_fecha', '<=', $hasta);
        }  
        if($bodega != '0'){
            $query ->where('bodega.bodega_id', '=', $bodega);
        } 
        if($sucursal != '0'){
            $query ->where('sucursal.sucursal_id', '=', $sucursal);
        }
        if($cliente != '0'){
            $query->where('cliente.cliente_id', '=', $cliente);
        }   
        return $query;
    }
    public function scopeFiltrar($query,$fechatodo, $desde, $hasta, $numeroDoc,$sucursal,$estado){
         $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')
        ->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')
        ->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)
        ->where('factura_numero','like','%'.$numeroDoc.'%');
        if($fechatodo != 'on'){
            $query ->where('factura_fecha', '>=', $desde)->where('factura_fecha', '<=', $hasta);
        }  
        if($sucursal != '0'){
            $query ->where('sucursal.sucursal_id', '=', $sucursal);
        }
        if($estado != '0'){
            $query->where('factura_venta.factura_estado', '=', $estado);
        } 
        return $query; 
    }
    public function scopeBusquedadetalle($query, $fechatodo, $desde, $hasta, $cliente, $bodega, $sucursal){
        $query->join('detalle_fv', 'detalle_fv.factura_id', '=', 'factura_venta.factura_id')
        ->join('producto', 'producto.producto_id', '=', 'detalle_fv.producto_id')
        ->join('grupo_producto', 'grupo_producto.grupo_id', '=', 'producto.grupo_id')
        ->join('cliente', 'cliente.cliente_id', '=', 'factura_venta.cliente_id')
        ->join('bodega', 'bodega.bodega_id', '=', 'factura_venta.bodega_id')
        ->join('sucursal', 'sucursal.sucursal_id', '=', 'bodega.sucursal_id')
        ->join('rango_documento', 'rango_documento.rango_id', '=', 'factura_venta.rango_id')
        ->join('punto_emision', 'punto_emision.punto_id', '=', 'rango_documento.punto_id')
        ->where('sucursal.empresa_id', '=', Auth::user()->empresa_id)
        ->where('factura_venta.factura_estado','<>','2');
        if($fechatodo != 'on'){
            $query ->where('factura_fecha', '>=', $desde)->where('factura_fecha', '<=', $hasta);
        }  
        if($bodega != '0'){
            $query ->where('bodega.bodega_id', '=', $bodega);
        } 
        if($sucursal != '0'){
            $query ->where('sucursal.sucursal_id', '=', $sucursal);
        }
        if($cliente != '0'){
            $query->where('cliente.cliente_id', '=', $cliente);
        }   
        return $query;
    }
    public function scopeFactura($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('factura_id','=',$id);
    }
    public function scopeSurcusalDistinsc($query){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');
    }
    public function scopeEstadoDistinsc($query){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->orderBy('factura_estado','asc');
    }
    public function scopeBodegaDistinsc($query){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->orderBy('bodega_nombre','asc');
    }
    public function scopeClienteDistinsc($query){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->orderBy('cliente_nombre','asc');
    }
    public function scopeFacturaIdArqueo($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('factura_venta.factura_tipo_pago','=', 'EN EFECTIVO')->where('factura_venta.arqueo_id','=',$id);
    }
    public function scopeFacturaContadoIdArqueo($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('factura_venta.factura_tipo_pago','=', 'CONTADO')->where('factura_venta.arqueo_id','=',$id);
    }
    public function scopeFacturaCreditoIdArqueo($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('factura_venta.factura_tipo_pago','=', 'CREDITO')->where('factura_venta.arqueo_id','=',$id);
    }
    public function scopeFacturaSumaEfectivo($query, $id){
        return $query->select(DB::raw('SUM(factura_total) as sumaEfectivo'))->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('factura_venta.factura_tipo_pago','=', 'EN EFECTIVO')->where('factura_venta.arqueo_id','=',$id);
    }
    public function scopeFacturaSumaContado($query, $id){
        return $query->select(DB::raw('SUM(factura_total) as sumaContado'))->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('factura_venta.factura_tipo_pago','=', 'CONTADO')->where('factura_venta.arqueo_id','=',$id);
    }
    public function scopeFacturaSumaCredito($query, $id){
        return $query->select(DB::raw('SUM(factura_total) as sumaCredito'))->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('factura_venta.factura_tipo_pago','=', 'CREDITO')->where('factura_venta.arqueo_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('factura_venta.rango_id','=',$id);
    }
    public function scopeFacturaNumero($query, $numeroFactura, $bodega){
        return $query->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->join('vendedor','vendedor.vendedor_id','=','factura_venta.vendedor_id')->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)->where('factura_venta.bodega_id','=',$bodega)->where('factura_estado','=','1')->where('factura_numero','like','%'.$numeroFactura.'%')->orderBy('factura_numero','desc');
    }
    public function scopeFacturaNumeroAnt($query, $numeroFactura, $bodega){
        return $query->join('cuenta_cobrar','factura_venta.cuenta_id','=','cuenta_cobrar.cuenta_id')->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)->where('factura_venta.bodega_id','=',$bodega)->where('factura_estado','=','1')->where('factura_numero','like','%'.$numeroFactura.'%')->orderBy('factura_numero','desc');
    }
    public function scopeFacturasbyFecha($query, $fechaInicio, $fechaFin){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('factura_fecha','>=',$fechaInicio)->where('factura_fecha','<=',$fechaFin)->where('factura_venta.factura_estado','<>','2');
    }
    public function scopeFacturasbyFechaSucrusal($query, $fechaInicio, $fechaFin, $sucursal){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')
        ->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')
        ->where('empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('factura_fecha','>=',$fechaInicio)
        ->where('factura_fecha','<=',$fechaFin);
    }
    public function scopeFechaNumero($query, $fecha, $numero){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('factura_fecha','=',$fecha)
        ->where('factura_numero','=',$numero);
    }
    public function scopeFacturasbyNumero($query, $numero){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('factura_numero', 'like', '%'.$numero.'%');
    }
    public function scopeFacturasFiltrar($query, $fechaInicio, $fechaFin,$numeroDoc){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)->where('factura_fecha','>=',$fechaInicio)->where('factura_fecha','<=',$fechaFin)->where('factura_numero','like','%'.$numeroDoc.'%');
    }
    public function scopeFacturasFiltrarSinFecha($query, $numeroDoc){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)->where('factura_numero','like','%'.$numeroDoc.'%');
    }
    public function scopeFacturasFiltrarsucursal($query, $fechaInicio, $fechaFin,$numeroDoc,$sucursal){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)
        ->where('factura_fecha','>=',$fechaInicio)
        ->where('factura_fecha','<=',$fechaFin)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('factura_numero','like','%'.$numeroDoc.'%');
    }
    public function scopeFacturasFiltrarsucursalSinFecha($query, $numeroDoc,$sucursal){
        return $query->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('factura_numero','like','%'.$numeroDoc.'%');
    }
    public function scopeFacturaVentaxProducto($query, $producto, $bodega, $cliente, $fechadesde, $fechahasta){
        $query->select('factura_venta.factura_id','factura_venta.factura_numero'
        ,'producto.producto_codigo'
        ,'producto.producto_nombre'
        ,'factura_venta.factura_fecha'
        ,'detalle_fv.detalle_cantidad'
        ,'detalle_fv.detalle_precio_unitario'
        ,'detalle_fv.detalle_iva'
        ,DB::raw('detalle_fv.detalle_precio_unitario * detalle_fv.detalle_cantidad as subtotal')
        ,DB::raw('(detalle_fv.detalle_total + detalle_fv.detalle_iva) as total')
        ,'cliente.cliente_nombre'        
        ,'factura_venta.factura_comentario'
        ,'producto.tamano_id')
        ->join('detalle_fv','detalle_fv.factura_id','=','factura_venta.factura_id')
        ->join('producto','producto.producto_id','=','detalle_fv.producto_id')
        ->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')
        ->join('bodega','bodega.bodega_id','=','factura_venta.bodega_id')        
        ->where('factura_venta.factura_estado','=','1')
        ->where('empresa_id','=',Auth::user()->empresa_id);
        if($producto != '0'){
            $query->where('detalle_fv.producto_id', '=', $producto);
        }
        if($bodega != '0'){
        $query->where('bodega.bodega_id','=',$bodega);
        }
        if($cliente != '0'){
            $query->where('cliente.cliente_id','=',$cliente);
        }      
        $query->where('factura_venta.factura_fecha','>=',$fechadesde)->where('factura_venta.factura_fecha','<=',$fechahasta)->orderBy('factura_venta.factura_fecha','asc');  
        return $query;
    }
    public function arqueoCaja(){
        return $this->belongsTo(Arqueo_Caja::class, 'arqueo_id', 'arqueo_id');
    }
    public function detalles(){
        return $this->hasMany(Detalle_FV::class, 'factura_id', 'factura_id');
    }
    public function notacredito(){
        return $this->hasMany(Nota_Credito::class, 'factura_id', 'factura_id');
    }
    public function notaDebito(){
        return $this->hasMany(Nota_Debito::class, 'factura_id', 'factura_id');
    }
    public function cuentaCobrar(){
        return $this->belongsTo(Cuenta_Cobrar::class, 'cuenta_id', 'cuenta_id');
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function diarioCosto(){
        return $this->belongsTo(Diario::class, 'diario_costo_id', 'diario_id');
    }
    public function documentoAnulado(){
        return $this->belongsTo(Documento_Anulado::class, 'documento_anulado_id', 'documento_anulado_id');
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
    public function bodega(){
        return $this->belongsTo(Bodega::class, 'bodega_id', 'bodega_id');
    }
    public function formaPago(){
        return $this->belongsTo(Forma_Pago::class, 'forma_pago_id', 'forma_pago_id');
    }
    public function retencion(){
        return $this->hasOne(Retencion_Venta::class, 'factura_id', 'factura_id');
    }
    public function retencionRecibida(){
        return $this->belongsTo(Retencion_Venta::class, 'factura_id', 'factura_id');
    }
    public function ordenDespacho(){
        return $this->hasOne(Orden_Despacho::class, 'factura_id', 'factura_id');
    }
    public function guias(){
        return $this->hasMany(Guia_Remision::class, 'factura_id', 'factura_id');
    }
    public function prefactura(){
        return $this->belongsTo(Prefactura_Venta::class, 'factura_id', 'factura_id');
    }
    public function vendedor(){
        return $this->belongsTo(Vendedor::class, 'vendedor_id', 'vendedor_id');
    }
    public function empresa(){
        return $this->hasOneThrough(Empresa::class, Forma_Pago::class,'forma_pago_id','empresa_id','forma_pago_id','empresa_id');
    }
    public function tamano(){
        return $this->belongsTo(Tamano_Producto::class, 'tamano_id', 'tamano_id');
    }
}
