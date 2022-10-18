<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_NC extends Model
{
    use HasFactory;
    protected $table='detalle_nc';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_cantidad',
        'detalle_precio_unitario',       
        'detalle_descuento',        
        'detalle_iva', 
        'detalle_total',
        'detalle_estado',
        'nc_id',
        'producto_id',
        'movimiento_id'
    ];
    protected $guarded =[
    ];
    public function scopeDetalleNotaCredito($query, $ncID){
        return $query->join('producto','producto.producto_id','=','detalle_nc.producto_id')->where('nc_id','=', $ncID)->orderBy('detalle_id','asc');
    }
    public function movimiento(){
        return $this->belongsTo(Movimiento_Producto::class, 'movimiento_id', 'movimiento_id');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function notaCredito()
    {
        return $this->belongsTo(Nota_Credito::class, 'nc_id', 'nc_id');
    }
}