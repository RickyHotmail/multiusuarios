<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Codigo_Producto extends Model
{
    use HasFactory;
    protected $table='codigo_producto';
    protected $primaryKey = 'codigo_id';
    public $timestamps=true;
    protected $fillable = [
        'codigo_nombre',
        'codigo_estado',       
        'proveedor_id',        
        'producto_id',                  
    ];
    protected $guarded =[
    ];
    public function scopecodigoproductos($query){
        return $query->join('producto','producto.producto_id','=','codigo_producto.producto_id')->where('producto.empresa_id','=',Auth::user()->empresa_id)->where('codigo_estado','=','1')->orderBy('producto.producto_nombre','asc');
    }
    public function scopecodigoproducto($query, $id){
        return $query->join('producto','producto.producto_id','=','codigo_producto.producto_id')->where('producto.empresa_id','=',Auth::user()->empresa_id)->where('codigo_producto.codigo_id','=',$id);
    }
    public function scopebuscarproductoproveedor($query, $id){
        return $query->join('producto','codigo_producto.producto_id','=','producto.producto_id')->where('codigo_producto.proveedor_id','=',$id);
    }
    public function scopebuscarproducto($query, $codigo, $id){
        return $query->join('producto','codigo_producto.producto_id','=','producto.producto_id')->where('codigo_producto.codigo_nombre','=',$codigo)->where('codigo_producto.proveedor_id','=',$id);
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }
}
