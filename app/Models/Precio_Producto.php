<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Precio_Producto extends Model
{
    use HasFactory;
    protected $table='precio_producto';
    protected $primaryKey = 'precio_id';
    public $timestamps=true;
    protected $fillable = [
        'precio_dias',
        'precio_valor',
        'precio_estado', 
        'producto_id',
    ];
    protected $guarded =[
    ];

    public function scopePrecioByProducto($query, $producto_id, $plazo)
    {
        return $query->join('producto','precio_producto.producto_id','=','producto.producto_id')
                    ->where('producto.empresa_id','=',Auth::user()->empresa_id)
                    ->where('precio_producto.producto_id', '=', $producto_id)
                    ->where('precio_producto.precio_dias', '=', $plazo);
    }
}
