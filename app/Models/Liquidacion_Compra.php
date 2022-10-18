<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Liquidacion_Compra extends Model
{
    use HasFactory;
    protected $table='liquidacion_compra';
    protected $primaryKey = 'lc_id';
    public $timestamps=true;
    protected $fillable = [
        'lc_numero',
        'lc_serie',
        'lc_secuencial',
        'lc_fecha',
        'lc_subtotal',
        'lc_descuento',
        'lc_tarifa0',
        'lc_tarifa12',
        'lc_iva',
        'lc_total',
        'lc_ivaB',
        'lc_ivaS',
        'lc_dias_plazo',
        'lc_comentario',
        'lc_tipo_pago',
        'lc_porcentaje_iva',
        'lc_emision',
        'lc_ambiente',
        'lc_autorizacion',
        'lc_xml_nombre',
        'lc_xml_estado',
        'lc_xml_mensaje',
        'lc_xml_respuestaSRI',
        'lc_xml_fecha',
        'lc_xml_hora',
        'lc_estado',
        'proveedor_id',
        'sustento_id',
        'diario_id',
        'forma_pago_id',
        'cuenta_id',
        'rango_id',
        'documento_anulado_id',
        'arqueo_id'
    ];
    protected $guarded =[
    ];
    public function scopeLiquidacionCompra($query, $id){
        return $query->join('sustento_tributario','sustento_tributario.sustento_id','=','liquidacion_compra.sustento_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('lc_id','=',$id);
    }
    public function scopeSucursalDistinsc($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','liquidacion_compra.rango_id')->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->join('sustento_tributario','sustento_tributario.sustento_id','=','liquidacion_compra.sustento_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');;
    }
    public function scopeSecuencial($query, $id){
        return $query->where('rango_id','=',$id);
    }
    public function scopeLCbyFecha($query, $fechaInicio, $fechaFin){
        return $query->join('rango_documento','rango_documento.rango_id','=','liquidacion_compra.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')
        ->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('lc_fecha','>=',$fechaInicio)
        ->where('lc_fecha','<=',$fechaFin)
        ->where('lc_estado','=','1');
    }
    public function scopeLCbyFechaSucrusal($query, $fechaInicio, $fechaFin, $sucursal){
        return $query->join('rango_documento','rango_documento.rango_id','=','liquidacion_compra.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')
        ->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('lc_fecha','>=',$fechaInicio)->where('lc_fecha','<=',$fechaFin)->where('sucursal_nombre','=',$sucursal);
    }
    public function scopeLCbyNumero($query, $numero){
        return $query->join('rango_documento','rango_documento.rango_id','=','liquidacion_compra.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')
        ->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('lc_numero', 'like', '%'.$numero.'%');
    }
    public function scopeReporteLiquidaciones($query){
        return $query->join('sustento_tributario','sustento_tributario.sustento_id','=','liquidacion_compra.sustento_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('lc_estado','=','1');
    }
    public function scopeLiquidacionCompraBuscar($query, $fechaInicio, $fechaFin,$numeroDoc,$sucursal){
        return $query->join('rango_documento','rango_documento.rango_id','=','liquidacion_compra.rango_id')->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->join('sustento_tributario','sustento_tributario.sustento_id','=','liquidacion_compra.sustento_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('lc_fecha','>=',$fechaInicio)->where('lc_fecha','<=',$fechaFin)
        ->where('lc_numero','like','%'.$numeroDoc.'%')
        ->where('sucursal_nombre','=',$sucursal);
    }
    public function scopeLiquidacionCompraOn($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','liquidacion_compra.rango_id')->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->join('sustento_tributario','sustento_tributario.sustento_id','=','liquidacion_compra.sustento_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
    }
    public function scopeLiquidacionCompraFecha($query, $fechaInicio, $fechaFin,$numeroDoc){
        return $query->join('rango_documento','rango_documento.rango_id','=','liquidacion_compra.rango_id')->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->join('sustento_tributario','sustento_tributario.sustento_id','=','liquidacion_compra.sustento_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('lc_fecha','>=',$fechaInicio)->where('lc_fecha','<=',$fechaFin)
        ->where('lc_numero','like','%'.$numeroDoc.'%');
    }
    public function scopeLiquidacionCompraSucursal($query, $numeroDoc,$sucursal){
        return $query->join('rango_documento','rango_documento.rango_id','=','liquidacion_compra.rango_id')->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->join('sustento_tributario','sustento_tributario.sustento_id','=','liquidacion_compra.sustento_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('lc_numero','like','%'.$numeroDoc.'%')
        ->where('sucursal_nombre','=',$sucursal);
    }
    public function arqueoCaja(){
        return $this->belongsTo(Arqueo_Caja::class, 'arqueo_id', 'arqueo_id');
    }
    public function detalles(){
        return $this->hasMany(Detalle_LC::class, 'lc_id', 'lc_id');
    }
    public function proveedor(){
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }
    public function sustentoTributario(){
        return $this->belongsTo(Sustento_Tributario::class, 'sustento_id', 'sustento_id');
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function formaPago(){
        return $this->belongsTo(Forma_Pago::class, 'forma_pago_id', 'forma_pago_id');
    }
    public function cuentaPagar(){
        return $this->belongsTo(Cuenta_Pagar::class, 'cuenta_id', 'cuenta_id');
    }
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
    public function retencionCompra()
    {
        return $this->hasOne(Retencion_Compra::class, 'lc_id', 'lc_id');
    }
    public function documentoAnulado(){
        return $this->belongsTo(Documento_Anulado::class, 'documento_anulado_id', 'documento_anulado_id');
    }
    
}
