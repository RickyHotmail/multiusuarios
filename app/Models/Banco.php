<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Banco extends Model
{
    use HasFactory;
    protected $table='banco';
    protected $primaryKey = 'banco_id';
    public $timestamps = true;
    protected $fillable = [        
        'banco_direccion',
        'banco_telefono',
        'banco_email',
        'banco_telefono',
        'banco_estado',         
        'banco_lista_id',
    ];
    protected $guarded =[
    ];
    public function scopeBancos($query){
        return $query->join('banco_lista', 'banco_lista.banco_lista_id','=','banco.banco_lista_id')->where('banco_lista.empresa_id','=',Auth::user()->empresa_id)->where('banco_estado','=','1')->orderBy('banco_id','asc');
    }
    public function scopeBanco($query, $id){
        return $query->join('banco_lista', 'banco_lista.banco_lista_id','=','banco.banco_lista_id')->where('banco_lista.empresa_id','=',Auth::user()->empresa_id)->where('banco_id','=',$id);
        
    }
    public function scopeBancoXbancolista($query, $id){
        return $query->join('banco_lista', 'banco_lista.banco_lista_id','=','banco.banco_lista_id')->where('banco_lista.empresa_id','=',Auth::user()->empresa_id)->where('banco.banco_lista_id','=',$id);
        
    }
    public function bancoLista()
    {
        return $this->belongsTo(Banco_Lista::class, 'banco_lista_id', 'banco_lista_id');
    }        
    
}
