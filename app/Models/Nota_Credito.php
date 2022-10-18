<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Nota_Credito extends Model
{
    use HasFactory;
    protected $table='nota_credito';
    protected $primaryKey = 'nc_id';
    public $timestamps=true;
    protected $fillable = [
        'nc_numero',
        'nc_serie',
        'nc_secuencial', 
        'nc_fecha', 
        'nc_subtotal',
        'nc_descuento',
        'nc_tarifa0', 
        'nc_tarifa12',  
        'nc_iva',  
        'nc_total',  
        'nc_comentario',
        'nc_porcentaje_iva',
        'nc_emision',
        'nc_ambiente',
        'nc_autorizacion',
        'nc_xml_nombre',
        'nc_xml_estado',
        'nc_xml_mensaje',
        'nc_xml_respuestaSRI',
        'nc_xml_fecha',
        'nc_xml_hora',
        'nc_estado',
        'diario_id',
        'factura_id',
        'rango_id',
        'documento_anulado_id',
        'diario_costo_id',
    ];
    protected $guarded =[
    ];
    public function scopeNotasCredito($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('nc_estado','=','1')->orderBy('nc_numero','asc');
    }
    public function scopeSucursales($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');
    }
    
   
    public function scopeNotaCredito($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('nc_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('nota_credito.rango_id','=',$id);
    }
    public function scopeNotaCreditoNumero($query, $numeroDoc, $bodega){
        return $query->join('factura_venta','factura_venta.factura_id','=','nota_credito.factura_id')->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')->join('tipo_cliente', 'tipo_cliente.tipo_cliente_id','=','cliente.tipo_cliente_id')->where('tipo_cliente.empresa_id','=',Auth::user()->empresa_id)->where('factura_venta.bodega_id','=',$bodega)->where('nc_estado','=','1')->where('nc_numero','like','%'.$numeroDoc.'%')->orderBy('nc_numero','asc');
    }
    public function scopeNCbyFecha($query, $fechaInicio, $fechaFin){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')
        ->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nc_fecha','>=',$fechaInicio)->where('nc_fecha','<=',$fechaFin)->where('nc_estado','=','1');
    }
    public function scopeNCbyFechaSucrusal($query, $fechaInicio, $fechaFin, $sucursal){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nc_fecha','>=',$fechaInicio)
        ->where('nc_fecha','<=',$fechaFin)
        ->where('sucursal_nombre','=',$sucursal);
    }
    public function scopeNCbyNumero($query, $numero){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('nc_numero', 'like', '%'.$numero.'%');
    }
    public function scopeNotasCreditobuscar($query,$fechaInicio,$fechaFin,$numeroDoc, $sucursal){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nc_fecha','>=',$fechaInicio)
        ->where('nc_fecha','<=',$fechaFin)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('nc_numero','like','%'.$numeroDoc.'%')
        ->orderBy('nc_numero','asc');
    }
    public function scopeNotasCreditobuscarOn($query,$numeroDoc){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nc_numero','like','%'.$numeroDoc.'%')
        ->orderBy('nc_numero','asc');
    }
    public function scopeNotasCreditoFechaFiltrar($query,$fechaInicio,$fechaFin,$numeroDoc){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nc_fecha','>=',$fechaInicio)
        ->where('nc_fecha','<=',$fechaFin)
        ->where('nc_numero','like','%'.$numeroDoc.'%')
        ->orderBy('nc_numero','asc');
    }
    public function scopeNotasCreditoSucursalFiltrar($query,$numeroDoc, $sucursal){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('nc_numero','like','%'.$numeroDoc.'%')
        ->orderBy('nc_numero','asc');
    }
    public function detalles(){
        return $this->hasMany(Detalle_NC::class, 'nc_id', 'nc_id');
    }   
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function diarioCosto(){
        return $this->belongsTo(Diario::class, 'diario_costo_id', 'diario_id');
    }
    public function factura(){
        return $this->belongsTo(Factura_Venta::class, 'factura_id', 'factura_id');
    }
    public function cliente(){
        return $this->hasOneThrough(Cliente::class, Factura_Venta::class, 'factura_id', 'cliente_id', 'factura_id', 'cliente_id');
    }
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
    public function documentoAnulado(){
        return $this->belongsTo(Documento_Anulado::class, 'documento_anulado_id', 'documento_anulado_id');
    }
}
