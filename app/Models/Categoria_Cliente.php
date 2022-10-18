<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Categoria_Cliente extends Model
{
    use HasFactory;
    protected $table='categoria_cliente';
    protected $primaryKey = 'categoria_cliente_id';
    public $timestamps = true;
    protected $fillable = [        
        'categoria_cliente_nombre',
        'categoria_cliente_descripcion',                
        'empresa_id',
        'categoria_cliente_estado',         
    ];
    protected $guarded =[
    ];   
    public function scopeCategoriaClientes($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('categoria_cliente_estado','=','1')->orderBy('categoria_cliente_nombre','asc');
    }
    public function scopeCategoriaCliente($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('categoria_cliente_id','=',$id);
    }
    public function scopeCategoriaClienteNombre($query, $nom){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('categoria_cliente_nombre','=',$nom);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
