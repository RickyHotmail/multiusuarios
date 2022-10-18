<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Anticipo_Proveedor extends Model
{
    use HasFactory;
    protected $table ='anticipo_proveedor';
    protected $primaryKey = 'anticipo_id';
    public $timestamps = true;
    protected $fillable = [
        'anticipo_numero',
        'anticipo_serie',
        'anticipo_secuencial',        
        'anticipo_fecha',
        'anticipo_tipo', 
        'anticipo_documento',
        'anticipo_motivo',
        'anticipo_valor',
        'anticipo_saldo',
        'anticipo_saldom',
        'rango_id',
        'proveedor_id',
        'diario_id',
        'anticipo_estado',
        'arqueo_id'   
    ];
    protected $guarded =[
    ];
    public function scopeAnticipoProveedores($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','anticipo_proveedor.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('anticipo_estado','=','1')->orderBy('anticipo_fecha','asc');
    }
    public function scopeAnticipoProveedor($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','anticipo_proveedor.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('anticipo_id','=',$id);
    }
    public function scopeAnticiposByProveedor($query, $proveedor_id){
        return $query->join('rango_documento','rango_documento.rango_id','=','anticipo_proveedor.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->join('diario','diario.diario_id','=','anticipo_proveedor.diario_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('proveedor_id','=',$proveedor_id)->where('anticipo_estado','=','1')->orderBy('anticipo_fecha','asc');
    }
    public function scopeSecuencial($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','anticipo_proveedor.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('rango_documento.rango_id','=',$id);

    }
    public function scopeAntProByFec($query, $fechaI, $fechaF, $proveedor_id, $sucursal_id,$todo){
        $query->join('rango_documento','rango_documento.rango_id','=','anticipo_proveedor.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('proveedor_id','=',$proveedor_id)->orderBy('anticipo_fecha','asc');
        if($sucursal_id != '0'){
            $query->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')
            ->where('punto_emision.sucursal_id','=',$sucursal_id);
        }
        if($todo != 1){
            $query->where('anticipo_fecha','>=',$fechaI)->where('anticipo_fecha','<=',$fechaF);
        }
        return $query;
    }
    public function scopeAnticipoClienteDescuentos($query, $id){
        return $query->join('descuento_anticipo_proveedor','descuento_anticipo_proveedor.anticipo_id','=','anticipo_proveedor.anticipo_id')->where('anticipo_proveedor.anticipo_id','=',$id);
    }
    public function scopeAnticiposByProveedorFecha($query, $proveedor_id, $fecha){
        return $query->join('rango_documento','rango_documento.rango_id','=','anticipo_proveedor.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('proveedor_id','=',$proveedor_id)->where('anticipo_fecha','<=',$fecha)->orderBy('anticipo_fecha','asc');
    }
    public function scopeAnticiposByProveedorFechaSucursal($query, $proveedor, $sucursal,$desde, $hasta){
        $query->join('rango_documento','rango_documento.rango_id','=','anticipo_proveedor.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')
        ->where('anticipo_fecha','>=',$desde)
        ->where('anticipo_fecha','<=',$hasta)
        ->where('anticipo_estado','=','1')
        ->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id);
        if($proveedor != '0'){
            $query->where('proveedor_id', '=', $proveedor);
        }
        if($sucursal != '0'){
            $query->where('punto_emision.sucursal_id', '=', $sucursal);
        }
        return $query;
       
    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function arqueo()
    {
        return $this->belongsTo(Arqueo_Caja::class, 'arqueo_id', 'arqueo_id');
    }
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }
    public function rangoDocumento()
    {
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
}
