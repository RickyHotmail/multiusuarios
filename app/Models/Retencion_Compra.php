<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Retencion_Compra extends Model
{
    use HasFactory;
    protected $table='retencion_compra';
    protected $primaryKey = 'retencion_id';
    public $timestamps=true;
    protected $fillable = [
        'retencion_fecha',
        'retencion_numero',       
        'retencion_serie',        
        'retencion_secuencial', 
        'retencion_emision',
        'retencion_ambiente',
        'retencion_autorizacion',
        'retencion_xml_nombre',
        'retencion_xml_estado',
        'retencion_xml_mensaje',
        'retencion_xml_respuestaSRI',
        'retencion_xml_fecha',
        'retencion_xml_hora',
        'retencion_estado',
        'transaccion_id',
        'lc_id',
        'documento_anulado_id',
        'rango_id'
    ];
    protected $guarded =[
    ];
    public function scopeRetencion($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','retencion_compra.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('retencion_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->where('retencion_compra.rango_id','=',$id);
    }
    public function scopelistadoRetencionesEmitidas($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','retencion_compra.rango_id')->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id');
    }
    public function scopelistadoRetencionesAnuladas($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','retencion_compra.rango_id')->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->where('retencion_estado','=','0');
    }
    public function scopeSucursales($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','retencion_compra.rango_id')->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->orderBy('sucursal_nombre','asc');
    }
    public function scoperetbyFecha($query, $fechaInicio, $fechaFin){
        return $query->join('rango_documento','rango_documento.rango_id','=','retencion_compra.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('retencion_fecha','>=',$fechaInicio)->where('retencion_fecha','<=',$fechaFin);
    }
    public function scoperetbyFechaSucrusal($query, $fechaInicio, $fechaFin,$sucursal){
        return $query->join('rango_documento','rango_documento.rango_id','=','retencion_compra.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('retencion_fecha','>=',$fechaInicio)
        ->where('retencion_fecha','<=',$fechaFin)
        ->where('sucursal_nombre','=',$sucursal);
    }
    public function scoperetbyNumero($query, $numero){
        return $query->join('rango_documento','rango_documento.rango_id','=','retencion_compra.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','rango_documento.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('retencion_numero', 'like', '%'.$numero.'%');
    }
    public function detalles(){
        return $this->hasMany(Detalle_RC::class, 'retencion_id', 'retencion_id');
    }
    public function transaccionCompra(){
        return $this->belongsTo(Transaccion_Compra::class, 'transaccion_id', 'transaccion_id');
    }
    public function liquidacionCompra()
    {
        return $this->belongsTo(Liquidacion_Compra::class, 'lc_id', 'lc_id');
    }
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
    public function dopcumentoanulado(){
        return $this->belongsTo(Documento_Anulado::class, 'documento_anulado_id', 'documento_anulado_id');
    }
}
