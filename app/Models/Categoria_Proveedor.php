<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Categoria_Proveedor extends Model
{
    use HasFactory;
    protected $table='categoria_proveedor';
    protected $primaryKey = 'categoria_proveedor_id';
    public $timestamps = true;
    protected $fillable = [        
        'categoria_proveedor_nombre',
        'categoria_proveedor_descripcion',                
        'empresa_id',
        'categoria_proveedor_estado',         
    ];
    protected $guarded =[
    ];   
    public function scopeCategoriaProveedores($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('categoria_proveedor_estado','=','1')->orderBy('categoria_proveedor_nombre','asc');
    }
    public function scopeCategoriaProveedor($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('categoria_proveedor_id','=',$id);
    }
    public function scopeCategoriaProveedorNombre($query, $nombre){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('categoria_proveedor_nombre','=',$nombre);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
