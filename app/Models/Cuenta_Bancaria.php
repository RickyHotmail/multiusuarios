<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Cuenta_Bancaria extends Model
{
    use HasFactory;
    protected $table='cuenta_bancaria';
    protected $primaryKey = 'cuenta_bancaria_id';
    public $timestamps=true;
    protected $fillable = [
        'cuenta_bancaria_numero',
        'cuenta_bancaria_tipo',       
        'cuenta_bancaria_saldo_inicial',        
        'cuenta_bancaria_jefe',
        'cuenta_bancaria_estado',
        'banco_id',
        'cuenta_id',                 
    ];
    protected $guarded =[
    ];
    public function scopeCuentaBancarias($query){
        return $query->join('cuenta', 'cuenta.cuenta_id','=','cuenta_bancaria.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_bancaria_estado','=','1')->orderBy('cuenta_bancaria_numero','asc');
    }
    public function scopeCuentaBancaria($query, $id){
        return $query->join('cuenta', 'cuenta.cuenta_id','=','cuenta_bancaria.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_bancaria_id','=',$id);
    }
    public function scopeCuentaBancariasBanco($query, $id){
        return $query->join('cuenta', 'cuenta.cuenta_id','=','cuenta_bancaria.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('banco_id','=',$id)->where('cuenta_bancaria_estado','=','1')->orderBy('cuenta_bancaria_numero','asc');
    }
    public function scopeCuentaBancoNumero($query, $id){
        return $query->join('banco', 'banco.banco_id','=','cuenta_bancaria.banco_id')->join('banco_lista', 'banco_lista.banco_lista_id','=','banco.banco_lista_id')->where('banco_lista.empresa_id','=',Auth::user()->empresa_id)->where('banco_lista.banco_lista_id','=',$id)->where('cuenta_bancaria_estado','=','1')->orderBy('cuenta_bancaria_numero','asc');
    }
    public function scopeCuentaBanco($query, $id){
        return $query->join('cuenta', 'cuenta.cuenta_id','=','cuenta_bancaria.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_bancaria_id','=',$id)->where('cuenta_bancaria_estado','=','1');
    }
    public function scopeCuentBancariaId($query, $id){
        return $query->join('cuenta', 'cuenta.cuenta_id','=','cuenta_bancaria.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('cuenta.cuenta_id','=',$id)->where('cuenta_bancaria_estado','=','1');
    }
    public function banco()
    {
        return $this->belongsTo(Banco::class, 'banco_id', 'banco_id');
    }
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'cuenta_id');
    }

}
