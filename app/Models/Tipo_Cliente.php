<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Cliente extends Model
{
    use HasFactory;
    protected $table='tipo_cliente';
    protected $primaryKey = 'tipo_cliente_id';
    public $timestamps = true;
    protected $fillable = [        
        'tipo_cliente_nombre',               
        'tipo_cliente_estado', 
        'empresa_id',        
    ];
    protected $guarded =[
    ];   
    public function scopeTipoClientes($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_cliente_estado','=','1')->orderBy('tipo_cliente_nombre','asc');
    }
    public function scopeTipoCliente($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_cliente_id','=',$id);
    }
    public function scopeTipoClienteNombre($query, $nom){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_cliente_nombre','=',$nom);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
