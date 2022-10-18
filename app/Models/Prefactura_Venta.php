<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Prefactura_Venta extends Model
{
    use HasFactory;
    protected $table='prefactura_venta';
    protected $primaryKey = 'prefactura_id';
    public $timestamps=true;
    protected $fillable = [
        'prefactura_numero',
        'prefactura_serie',
        'prefactura_secuencial', 
        'prefactura_tipo', 
        'prefactura_aguaje', 
        'prefactura_fecha',
        'prefactura_lugar',
        'prefactura_tipo_pago',
        'prefactura_dias_plazo', 
        'prefactura_fecha_pago', 
        'prefactura_subtotal',
        'prefactura_descuento',
        'prefactura_tarifa0', 
        'prefactura_tarifa12',  
        'prefactura_iva',  
        'prefactura_total',  
        'prefactura_total_sacos',  
        'prefactura_comentario',
        'prefactura_porcentaje_iva',
        'prefactura_estado',
        'bodega_id',
        'cliente_id',
        'factura_id',
        'forma_pago_id',
        'rango_id',
    ];
    protected $guarded =[
    ];
    public function scopeprefacturas($query){
        return $query->join('cliente','cliente.cliente_id','=','prefactura_venta.cliente_id')->join('bodega','bodega.bodega_id','=','prefactura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('prefactura_estado','=','1')->orderBy('prefactura_numero','desc');
    }
    public function scopeprefacturacion($query){
        return $query->join('cliente','cliente.cliente_id','=','prefactura_venta.cliente_id')->join('bodega','bodega.bodega_id','=','prefactura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
    }
    public function scopebuscar($query, $cliente, $estado, $todo, $desde, $hasta, $numeroDoc){
        $query->join('cliente','cliente.cliente_id','=','prefactura_venta.cliente_id')->join('bodega','bodega.bodega_id','=','prefactura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
        if($todo != 'on'){
            $query ->where('prefactura_fecha', '>=', $desde)->where('prefactura_fecha', '<=', $hasta);
        }  
        if($estado != '3'){
            $query ->where('prefactura_estado', '=', $estado);
        } 
        if($cliente != '0'){
            $query->where('cliente.cliente_id', '=', $cliente);
        }

        $query->where('prefactura_numero','like','%'.$numeroDoc.'%')->orderBy('prefactura_fecha','desc');
        return $query; 
    }
    public function scopetotales($query, $id){
        return $query->join('detalle_pfv','detalle_pfv.prefactura_id','=','prefactura_venta.prefactura_id')->join('producto','detalle_pfv.producto_id','=','producto.producto_id')->where('producto.empresa_id','=',Auth::user()->empresa_id)->where('prefactura_venta.prefactura_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','prefactura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('prefactura_venta.rango_id','=',$id);
    }
    public function scopeGuias($query, $id){
        return $query->join('detalle_pfv','detalle_pfv.prefactura_id','=','prefactura_venta.prefactura_id')->join('guia_remision','detalle_pfv.gr_id','=','guia_remision.gr_id')->join('bodega','bodega.bodega_id','=','prefactura_venta.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('prefactura_venta.prefactura_id','=',$id);
    }
    public function detalles(){
        return $this->hasMany(Detalle_PFV::class, 'prefactura_id', 'prefactura_id');
    }  
    
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }
    public function bodega(){
        return $this->belongsTo(Bodega::class, 'bodega_id', 'bodega_id');
    }
    public function factura(){
        return $this->belongsTo(Factura_Venta::class, 'factura_id', 'factura_id');
    }
    public function formapago(){
        return $this->belongsTo(Forma_Pago::class, 'forma_pago_id', 'forma_pago_id');
    }
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
}
