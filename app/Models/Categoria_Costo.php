<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Categoria_Costo extends Model
{
    use HasFactory;
    protected $table='categoria_costo';
    protected $primaryKey = 'categoriac_id';
    public $timestamps = true;
    protected $fillable = [        
        'categoriac_general',
        'categoriac_costo',                
        'categoriac_racewas',
        'categoriac_sin_aplicacion',
        'categoriac_visible',
        'categoriac_estado',
        'categoria_id',         
    ];
    protected $guarded =[
    ];   
    public function scopecategoriaClientes($query){
        return $query->join('categoria_producto','categoria_producto.categoria_id','=','categoria_costo.categoria_id')->where('categoria_producto.empresa_id','=',Auth::user()->empresa_id)->where('categoriac_estado','=','1')->orderBy('categoriac_general','asc');
    }
    public function scopecategoriaCliente($query, $id){
        return $query->join('categoria_producto','categoria_producto.categoria_id','=','categoria_costo.categoria_id')->where('categoria_producto.empresa_id','=',Auth::user()->empresa_id)->where('categoriac_id','=',$id);
    }
    public function categoriaproducto(){
        return $this->belongsTo(Categoria_Producto::class, 'categoria_id', 'categoria_id');
    }
}
