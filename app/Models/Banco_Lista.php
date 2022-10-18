<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Banco_Lista extends Model
{
    use HasFactory;
    protected $table='banco_lista';
    protected $primaryKey = 'banco_lista_id';
    public $timestamps = true;
    protected $fillable = [        
        'banco_lista_nombre',
        'banco_lista_estado',         
        'empresa_id', 
    ];
    protected $guarded =[
    ];
    public function scopeBancoListas($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('banco_lista_estado','=','1')->orderBy('banco_lista_nombre','asc');
    }
    public function scopeBancoLista($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('banco_lista_id','=',$id);
    }
     public function scopeBancoListaByNom($query, $nom){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('banco_lista_nombre','=',$nom);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function scopeBanco($query){
        return $query->join('banco', 'banco_lista.banco_lista_id','=','banco.banco_lista_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('banco_lista_estado','=','1')->orderBy('banco_lista_nombre','asc');
    }   
}
