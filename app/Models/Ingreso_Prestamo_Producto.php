<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Ingreso_Prestamo_Producto extends Model
{
    use HasFactory;
    protected $table ='ingreso_prestamo_producto';
    protected $primaryKey = 'ingreso_id';
    public $timestamps = true;
    protected $fillable = [
        'ingreso_numero',
        'ingreso_serie',
        'ingreso_secuencial',        
        'ingreso_fecha',  
        'ingreso_placa',  
        'ingreso_valor',
        'ingreso_descripcion',
        'cliente_id',
        'producto_id', 
        'transportista_id', 
        'ingreso_estado',
        'rango_id',
        'movimiento_id',
        'empresa_id',
    ];
    protected $guarded =[
    ];
    public function scopeIngresos($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','ingreso_prestamo_producto.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('ingreso_estado','=','1')->orderBy('ingreso_fecha','asc');
    }
    public function scopebuscar($query,$desde,$hasta,$cliente){
        $query->join('rango_documento','rango_documento.rango_id','=','ingreso_prestamo_producto.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('ingreso_estado','=','1')
        ->where('ingreso_fecha', '>=', $desde)->where('ingreso_fecha', '<=', $hasta);
        if($cliente != '0'){
            $query->where('cliente_id', '=', $cliente);
        } 
        return $query->orderBy('ingreso_fecha','asc') ;
    }
    public function scopeSecuencial($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','ingreso_prestamo_producto.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('rango_documento.rango_id','=',$id);

    }
    public function movimiento(){
        return $this->belongsTo(Movimiento_Prestamo_Producto::class, 'movimiento_id', 'movimiento_id');
    }
    public function Transportista(){
        return $this->belongsTo(Transportista::class, 'transportista_id', 'transportista_id');
    }
    public function Cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
}
