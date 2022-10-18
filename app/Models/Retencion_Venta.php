<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Retencion_Venta extends Model
{
    use HasFactory;
    protected $table='retencion_venta';
    protected $primaryKey = 'retencion_id';
    public $timestamps=true;
    protected $fillable = [
        'retencion_fecha',
        'retencion_numero',       
        'retencion_serie',        
        'retencion_secuencial', 
        'retencion_estado',
        'retencion_emision',
        'factura_id',
        'nd_id',
        'diario_id'
    ];
    protected $guarded =[
    ];
    public function scopeRetencionBySerieSecuancial($query, $serie, $secuencial,$clienteRuc){
        return $query->join('factura_venta', 'factura_venta.factura_id', '=', 'retencion_venta.factura_id')
        ->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')
        ->where('retencion_serie','=',$serie)->where('retencion_secuencial','=',$secuencial)
        ->where('cliente.cliente_cedula','=',$clienteRuc);
    }
    public function scopeRetencionBySerieSecuancialND($query, $serie, $secuencial,$clienteRuc){
        return $query->join('nota_debito','nota_debito.nd_id','=','retencion_venta.nd_id')
        ->join('factura_venta', 'factura_venta.factura_id', '=', 'nota_debito.factura_id')
        ->join('cliente','cliente.cliente_id','=','factura_venta.cliente_id')
        ->where('retencion_serie','=',$serie)->where('retencion_secuencial','=',$secuencial)
        ->where('cliente.cliente_cedula','=',$clienteRuc);
    }
    public function scopelistadoRetencionesEmitidas($query, $fechaInicio, $fechaFin){
        return $query->join('factura_venta','factura_venta.factura_id','=','retencion_venta.factura_id')->join('rango_documento','rango_documento.rango_id','=','factura_venta.rango_id')
        ->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')
        ->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->join('diario','diario.diario_id','=','retencion_venta.diario_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('retencion_fecha','>=',$fechaInicio)
        ->where('retencion_fecha','<=',$fechaFin)
        ->where('retencion_estado','=','1');
    }
    public function scopelistadoRetencionesEmitidasSucursal($query, $fechaInicio, $fechaFin, $sucursal){
        return $query->join('factura_venta','factura_venta.factura_id','=','retencion_venta.factura_id')->join('rango_documento','rango_documento.rango_id','=','factura_venta.rango_id')
        ->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')
        ->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->join('diario','diario.diario_id','=','retencion_venta.diario_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('retencion_fecha','>=',$fechaInicio)
        ->where('retencion_fecha','<=',$fechaFin)
        ->where('sucursal_nombre','=',$sucursal)
        ->where('retencion_estado','=','1');
    }
    public function scopeSucursales($query){
        return $query->join('factura_venta','factura_venta.factura_id','=','retencion_venta.factura_id')->join('rango_documento','rango_documento.rango_id','=','factura_venta.rango_id')->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('retencion_estado','=','1')->orderBy('sucursal_nombre','asc');
    }
    public function scopeRetByFecha($query, $fechaInicio, $fechaFin){
        return $query->join('diario','diario.diario_id','=','retencion_venta.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('retencion_fecha','>=',$fechaInicio)->where('retencion_fecha','<=',$fechaFin);
    }
    public function scopeRetencionByFactura($query,$idFactura){
        return $query->join('factura_venta','factura_venta.factura_id','=','retencion_venta.factura_id')
        ->join('rango_documento','rango_documento.rango_id','=','factura_venta.rango_id')
        ->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')
        ->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->join('detalle_rv','detalle_rv.retencion_id','=','retencion_venta.retencion_id')
        ->join('concepto_retencion','concepto_retencion.concepto_id','=','detalle_rv.concepto_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('retencion_estado','=','1')
        ->where('retencion_venta.factura_id','=',$idFactura);
    }
    public function scopeRetencionByNotaDebito($query,$idFactura){
        return $query->join('factura_venta','factura_venta.factura_id','=','retencion_venta.factura_id')
        ->join('rango_documento','rango_documento.rango_id','=','factura_venta.rango_id')
        ->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')
        ->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->join('detalle_rv','detalle_rv.retencion_id','=','retencion_venta.retencion_id')
        ->join('concepto_retencion','concepto_retencion.concepto_id','=','detalle_rv.concepto_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('retencion_estado','=','1')
        ->where('retencion_venta.nd_id','=',$idFactura);
    }
    public function scopeRetencionByFacturaS($query,$idFactura){
        return $query->join('factura_venta','factura_venta.factura_id','=','retencion_venta.factura_id')
        ->join('rango_documento','rango_documento.rango_id','=','factura_venta.rango_id')
        ->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')
        ->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('retencion_estado','=','1')
        ->where('retencion_venta.factura_id','=',$idFactura);
    }
    public function detalles(){
        return $this->hasMany(Detalle_RV::class, 'retencion_id', 'retencion_id');
    }
    public function factura(){
        return $this->belongsTo(Factura_Venta::class, 'factura_id', 'factura_id');
    }
    public function notaDebito()
    {
        return $this->belongsTo(Nota_Debito::class, 'nd_id', 'nd_id');
    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
}
