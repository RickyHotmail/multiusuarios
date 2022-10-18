<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Nota_Debito extends Model
{
    use HasFactory;
    protected $table='nota_debito';
    protected $primaryKey = 'nd_id';
    public $timestamps=true;
    protected $fillable = [
        'nd_numero',
        'nd_serie',
        'nd_secuencial', 
        'nd_fecha',
        'nd_tipo_pago',
        'nd_dias_plazo', 
        'nd_fecha_pago', 
        'nd_subtotal',
        'nd_descuento',
        'nd_tarifa0', 
        'nd_tarifa12',  
        'nd_iva',  
        'nd_total',  
        'nd_motivo',
        'nd_comentario',
        'nd_porcentaje_iva',
        'nd_emision',
        'nd_ambiente',
        'nd_autorizacion',
        'nd_xml_nombre',
        'nd_xml_estado',
        'nd_xml_mensaje',
        'nd_xml_respuestaSRI',
        'nd_xml_fecha',
        'nd_xml_hora',
        'nd_estado',
        'forma_pago_id',
        'factura_id',
        'diario_id',
        'rango_id',
        'cuenta_id',
        'documento_anulado_id',
        'arqueo_id',
    ];
    protected $guarded =[
    ];
    public function scopeNotasDebito($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_debito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('nd_estado','=','1')->orderBy('nd_numero','asc');
    }
    public function scopeSucursales($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_debito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');
    }
    public function scopeNotaDebito($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_debito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('nd_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_debito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('nota_debito.rango_id','=',$id);
    }
    public function scopeNotaDebitoNumero($query, $numeroDoc, $bodega){
        return $query->join('factura_venta','factura_venta.factura_id','=','nota_debito.factura_id')->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)->where('factura_venta.bodega_id','=',$bodega)->where('nd_estado','=','1')->where('nd_numero','like','%'.$numeroDoc.'%')->orderBy('nd_numero','asc');
    }
    public function scopeNDbyFecha($query, $fechaInicio, $fechaFin){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_debito.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nd_fecha','>=',$fechaInicio)
        ->where('nd_fecha','<=',$fechaFin)->where('nd_estado','=','1');
    }
    public function scopeNDbyFechaSucrusal($query, $fechaInicio, $fechaFin,$sucursal){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_debito.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nd_fecha','>=',$fechaInicio)
        ->where('nd_fecha','<=',$fechaFin)
        ->where('sucursal_nombre','=',$sucursal);
    }
    public function scopeNDbyNumero($query, $numero){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_debito.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('nd_numero', 'like', '%'.$numero.'%');
    }
    
    public function scopeNotasDebitobuscar($query,$fechaInicio,$fechaFin,$numeroDoc,$sucursal){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_debito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nd_fecha','>=',$fechaInicio)
        ->where('nd_fecha','<=',$fechaFin)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('nd_numero','like','%'.$numeroDoc.'%')
        ->orderBy('nd_numero','asc');
    }
    public function scopeNotasDebitobuscarOn($query,$numeroDoc){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_debito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nd_numero','like','%'.$numeroDoc.'%')
        ->orderBy('nd_numero','asc');
    }
    public function scopeNotasDebitoFechaFiltrar($query,$fechaInicio,$fechaFin,$numeroDoc){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_debito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nd_fecha','>=',$fechaInicio)
        ->where('nd_fecha','<=',$fechaFin)
        ->where('nd_numero','like','%'.$numeroDoc.'%')
        ->orderBy('nd_numero','asc');
    }
    public function scopeNotasDebitoSucursalFiltrar($query,$numeroDoc,$sucursal){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_debito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('nd_numero','like','%'.$numeroDoc.'%')
        ->orderBy('nd_numero','asc');
    }
    public function detalles(){
        return $this->hasMany(Detalle_ND::class, 'nd_id', 'nd_id');
    }
    public function arqueoCaja(){
        return $this->belongsTo(Arqueo_Caja::class, 'arqueo_id', 'arqueo_id');
    }
    public function cuentaCobrar(){
        return $this->belongsTo(Cuenta_Cobrar::class, 'cuenta_id', 'cuenta_id');
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function factura(){
        return $this->belongsTo(Factura_Venta::class, 'factura_id', 'factura_id');
    }
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
    public function formaPago(){
        return $this->belongsTo(Forma_Pago::class, 'forma_pago_id', 'forma_pago_id');
    }
    public function retencion(){
        return $this->hasOne(Retencion_Venta::class, 'nd_id', 'nd_id');
    }
    public function documentoAnulado(){
        return $this->belongsTo(Documento_Anulado::class, 'documento_anulado_id', 'documento_anulado_id');
    }
}
