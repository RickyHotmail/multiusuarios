<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Categoria_Producto extends Model
{
    use HasFactory;
    protected $table='categoria_producto';
    protected $primaryKey = 'categoria_id';
    public $timestamps = true;
    protected $fillable = [        
        'categoria_nombre',
        'categoria_tipo',                
        'empresa_id',
        'categoria_estado',         
    ];
    protected $guarded =[
    ];   
    public function scopeCategoriaByName($query, $nombre){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('categoria_nombre','=',$nombre);
    }
    public function scopeExtraer($query, $id, $nombre){
        return $query->join('producto','producto.categoria_id','=','categoria_producto.categoria_id')
        ->where('categoria_producto.empresa_id','=',Auth::user()->empresa_id)
        ->where('categoria_producto.categoria_id','=',$id)
        ->where(function ($query) use ($nombre) {
            $query->where(DB::raw('lower(producto.producto_codigo)'), 'like', '%'.strtolower($nombre).'%');
        })->orderBy('producto.producto_codigo','asc');
    }
    public function scopeCategorias($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('categoria_estado','=','1')->orderBy('categoria_nombre','asc');
    }
    public function scopeCategoria($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('categoria_id','=',$id);
    }
    public function categoriacosto(){
        return $this->belongsTo(Categoria_Costo::class, 'categoria_id', 'categoria_id');
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
