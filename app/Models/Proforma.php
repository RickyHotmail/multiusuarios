<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class Proforma extends Model
{
    use HasFactory;
    protected $table='proforma';
    protected $primaryKey = 'proforma_id';
    public $timestamps=true;
    protected $fillable = [
        'proforma_numero',
        'proforma_serie',
        'proforma_secuencial',
        'proforma_fecha', 
        'proforma_tipo_pago', 
        'proforma_dias_plazo', 
        'proforma_subtotal', 
        'proforma_tarifa0', 
        'proforma_tarifa12', 
        'proforma_descuento', 
        'proforma_iva', 
        'proforma_total', 
        'proforma_comentario',
        'proforma_porcentaje_iva', 
        'proforma_estado', 
        'bodega_id',
        'cliente_id',        
        'rango_documento_id',        
    ];
    protected $guarded =[
    ];

    public function scopeProformas($query){
        return $query->join('cliente','cliente.cliente_id','=','proforma.cliente_id')->join('bodega','bodega.bodega_id','=','proforma.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('proforma_numero','asc');
    }
    public function scopeClientesDistinsc($query){
        return $query->join('cliente','cliente.cliente_id','=','proforma.cliente_id')->join('bodega','bodega.bodega_id','=','proforma.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->join('tipo_identificacion','tipo_identificacion.tipo_identificacion_id','=','cliente.tipo_identificacion_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('cliente.cliente_nombre','asc');
    }
    public function scopeSucursalDistinsc($query){
        return $query->join('cliente','cliente.cliente_id','=','proforma.cliente_id')->join('bodega','bodega.bodega_id','=','proforma.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->join('tipo_identificacion','tipo_identificacion.tipo_identificacion_id','=','cliente.tipo_identificacion_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');
    }
    public function scopeProforma($query, $id){
        return $query->join('cliente','cliente.cliente_id','=','proforma.cliente_id')->join('bodega','bodega.bodega_id','=','proforma.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('proforma_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','proforma.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('proforma.rango_id','=',$id);
    }
    public function detalles(){
        return $this->hasMany(Detalle_Proforma::class, 'proforma_id', 'proforma_id');
    }
    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'bodega_id', 'bodega_id');
    }
    public function scopeProformaFiltrar($query, $fechatodo, $fechaInicio, $fechaFin,$sucursal,$cliente){
        $query->join('cliente', 'cliente.cliente_id', '=', 'proforma.cliente_id')->join('bodega', 'bodega.bodega_id', '=', 'proforma.bodega_id')->join('sucursal', 'sucursal.sucursal_id', '=', 'bodega.sucursal_id')->join('rango_documento', 'rango_documento.rango_id', '=', 'proforma.rango_id')->join('punto_emision', 'punto_emision.punto_id', '=', 'rango_documento.punto_id')
       ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('proforma.proforma_fecha','desc');
       if($cliente != '0'){
           $query->where('cliente.cliente_id','=',$cliente);
       } 
       if($sucursal != '0'){
           $query->where('sucursal.sucursal_id','=',$sucursal);
       } 
       if($fechatodo != 'on'){
           $query->where('proforma.proforma_fecha','>=',$fechaInicio)
           ->where('proforma.proforma_fecha','<=',$fechaFin);
       }
       return $query; 
   }
    public function cliente()
    {
        return $this->belongsTo(cliente::class, 'cliente_id', 'cliente_id');
    }
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
    

}
