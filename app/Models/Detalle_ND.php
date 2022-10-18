<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_ND extends Model
{
    use HasFactory;
    protected $table='detalle_nd';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_cantidad',
        'detalle_precio_unitario',       
        'detalle_descuento',        
        'detalle_iva', 
        'detalle_total',
        'detalle_estado',
        'nd_id',
        'producto_id',
        'movimiento_id'
    ];
    protected $guarded =[
    ];
    public function scopeDetalleNotaDebito($query, $ndID){
        return $query->join('producto','producto.producto_id','=','detalle_nd.producto_id')->where('nd_id','=', $ndID)->orderBy('detalle_id','asc');
    }
    public function scopeDetalleNotaDebitoTotal($query, $ndID){
        return $query->join('nota_debito','nota_debito.nd_id','=','detalle_nd.nd_id')->join('producto','producto.producto_id','=','detalle_nd.producto_id')->where('detalle_nd.nd_id','=', $ndID)->orderBy('detalle_id','asc');
    }
    public function movimiento(){
        return $this->belongsTo(Movimiento_Producto::class, 'movimiento_id', 'movimiento_id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function notaDebito()
    {
        return $this->belongsTo(Nota_Debito::class, 'nd_id', 'nd_id');
    }
}
