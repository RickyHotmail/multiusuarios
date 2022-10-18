<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Nota_Entrega extends Model
{
    use HasFactory;
    protected $table='nota_entrega';
    protected $primaryKey = 'nt_id';
    public $timestamps=true;
    protected $fillable = [
        'nt_numero',
        'nt_serie',
        'nt_secuencial',
        'nt_fecha', 
        'nt_tipo_pago', 
        'nt_total', 
        'nt_comentario',
        'nt_estado', 
        'diario_costo_id',
        'diario_id',
        'cuenta_id',
        'bodega_id',
        'cliente_id', 
        'rango_id',
        'arqueo_id',
    ];
    protected $guarded =[
    ];
    public function scopeNotaEntregas($query){
        return $query->join('cliente','cliente.cliente_id','=','nota_entrega.cliente_id')->join('bodega','bodega.bodega_id','=','nota_entrega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('nt_estado','=','1');
    }
    public function scopeClientes($query){
        return $query->join('cliente','cliente.cliente_id','=','nota_entrega.cliente_id')->join('bodega','bodega.bodega_id','=','nota_entrega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('cliente_nombre','asc');
    }
    public function scopeSucursales($query){
        return $query->join('cliente','cliente.cliente_id','=','nota_entrega.cliente_id')->join('bodega','bodega.bodega_id','=','nota_entrega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');
    }
    public function scopeNotaEntrega($query, $id){
        return $query->join('cliente','cliente.cliente_id','=','nota_entrega.cliente_id')->join('bodega','bodega.bodega_id','=','nota_entrega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('nt_estado','=','1')->where('nt_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->join('bodega','bodega.bodega_id','=','nota_entrega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('nota_entrega.rango_id','=',$id);
    }
    public function scopeTodosDiferentes($query,$fecha_desde,$fecha_hasta,$cliente,$sucursal){
        return $query->join('cliente','cliente.cliente_id','=','nota_entrega.cliente_id')->join('bodega','bodega.bodega_id','=','nota_entrega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nt_fecha', '>=', $fecha_desde)
        ->where('nt_fecha', '<=', $fecha_hasta)
        ->where('cliente.cliente_id', '=', $cliente)
        ->where('sucursal_nombre', '=', $sucursal)
        ->orderBy('nt_numero','asc');
    }
    public function scopeFecha($query,$fechadesde,$fechahasta){
        return $query->join('cliente','cliente.cliente_id','=','nota_entrega.cliente_id')->join('bodega','bodega.bodega_id','=','nota_entrega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nt_fecha', '>=', $fechadesde)
        ->where('nt_fecha', '<=', $fechahasta)
        ->orderBy('nt_numero','asc');
    }
    public function scopebuscarCliente($query,$cliente){
        return $query->join('cliente','cliente.cliente_id','=','nota_entrega.cliente_id')->join('bodega','bodega.bodega_id','=','nota_entrega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('cliente.cliente_id', '=', $cliente)
        ->orderBy('nt_numero','asc');
    }
    public function scopebuscarSucursal($query,$sucursal){
        return $query->join('cliente','cliente.cliente_id','=','nota_entrega.cliente_id')->join('bodega','bodega.bodega_id','=','nota_entrega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('sucursal_nombre', '=', $sucursal)
        ->orderBy('nt_numero','asc');
    }
    public function scopebuscarFechaCliente($query,$fecha_desde,$fecha_hasta,$cliente){
        return $query->join('cliente','cliente.cliente_id','=','nota_entrega.cliente_id')->join('bodega','bodega.bodega_id','=','nota_entrega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nt_fecha', '>=', $fecha_desde)
        ->where('nt_fecha', '<=', $fecha_hasta)
        ->where('cliente.cliente_id', '=', $cliente)
        ->orderBy('nt_numero','asc');
    }
    public function scopebuscarFechaSucursal($query,$fecha_desde,$fecha_hasta,$sucursal){
        return $query->join('cliente','cliente.cliente_id','=','nota_entrega.cliente_id')->join('bodega','bodega.bodega_id','=','nota_entrega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('nt_fecha', '>=', $fecha_desde)
        ->where('nt_fecha', '<=', $fecha_hasta)
        ->where('sucursal_nombre', '=', $sucursal)
        ->orderBy('nt_numero','asc');
    }
    public function scopebuscarClienteSucursal($query,$cliente,$sucursal){
        return $query->join('cliente','cliente.cliente_id','=','nota_entrega.cliente_id')->join('bodega','bodega.bodega_id','=','nota_entrega.bodega_id')->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
        ->where('cliente.cliente_id', '=', $cliente)
        ->where('sucursal_nombre', '=', $sucursal)
        ->orderBy('nt_numero','asc');
    }
    public function arqueoCaja(){
        return $this->belongsTo(Arqueo_Caja::class, 'arqueo_id', 'arqueo_id');
    }
   
    public function detalle(){
        return $this->hasMany(Detalle_NE::class, 'nt_id', 'nt_id');
    }
    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'bodega_id', 'bodega_id');
    }
    public function cliente()
    {
        return $this->belongsTo(cliente::class, 'cliente_id', 'cliente_id');
    }
    public function diariocosto(){
        return $this->belongsTo(Diario::class, 'diario_costo_id', 'diario_id');
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
    public function cuentaCobrar(){
        return $this->belongsTo(Cuenta_Cobrar::class, 'cuenta_id', 'cuenta_id');
    }
}
