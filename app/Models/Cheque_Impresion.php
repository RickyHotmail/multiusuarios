<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cheque_Impresion extends Model
{
    use HasFactory;
    protected $table='cheque_impresion';
    protected $primaryKey = 'chequei_id';
    public $timestamps=true;
    protected $fillable = [
        'chequei_id',
        'chequei_valorx',
        'chequei_valory',
        'chequei_valorfont',
        'chequei_beneficiariox',
        'chequei_beneficiarioy',
        'chequei_beneficiariofont',
        'chequei_letrasx',
        'chequei_letrasy',
        'chequei_letrasfont',
        'chequei_fechax',
        'chequei_fechay',
        'chequei_fechafont',
        'cuenta_bancaria_id',
        'chequei_estado',
                   
    ];
    protected $guarded =[
    ];
    public function scopeChequeImpresion($query, $id){
        return $query->join('cuenta_bancaria', 'cuenta_bancaria.cuenta_bancaria_id','=','cheque_impresion.cuenta_bancaria_id')->join('banco', 'banco.banco_id','=','cuenta_bancaria.banco_id')->join('banco_lista', 'banco_lista.banco_lista_id','=','banco.banco_lista_id')->where('banco_lista.empresa_id','=',Auth::user()->empresa_id)->where('chequei_estado','=','1')->where('cheque_impresion.cuenta_bancaria_id','=',$id)->orderBy('chequei_id','asc');
    }

    public function scopeChequeImpresionByUser($query, $id){
        return $query->join('cuenta_bancaria', 'cuenta_bancaria.cuenta_bancaria_id','=','cheque_impresion.cuenta_bancaria_id'
                    )->join('banco', 'banco.banco_id','=','cuenta_bancaria.banco_id'
                    )->join('banco_lista', 'banco_lista.banco_lista_id','=','banco.banco_lista_id'
                    )->where('banco_lista.empresa_id','=',Auth::user()->empresa_id
                    )->where('chequei_estado','=','1'
                    )->where('cheque_impresion.cuenta_bancaria_id','=',$id
                    )->where('cheque_impresion.user_id','=',Auth::user()->user_id
                    )->orderBy('chequei_id','asc');
    }

    
    public function cuentaBancaria()
    {
        return $this->belongsTo(Cuenta_Bancaria::class, 'cuenta_bancaria_id', 'cuenta_bancaria_id');
    }
}
