<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Deposito extends Model
{
    use HasFactory;
    protected $table='deposito';
    protected $primaryKey = 'deposito_id';
    public $timestamps=true;
    protected $fillable = [
        'deposito_fecha',
        'deposito_tipo',       
        'deposito_numero',        
        'deposito_valor',
        'deposito_descripcion',
        'deposito_estado',
        'empresa_id',
        'cuenta_bancaria_id',
        'deposito_conciliacion',
        'deposito_fecha_conciliacion',         
    ];
    protected $guarded =[
    ];
    public function scopeDeposito($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('deposito.deposito_id','=',$id);
    }
    public function scopeDepositosByCuenta($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('deposito.cuenta_bancaria_id','=',$id)->orderby('deposito.deposito_fecha','asc');
    }    
    public function scopeDepositosOtrosByCuenta($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('deposito.cuenta_bancaria_id','=',$id)->orderby('deposito.deposito_fecha', 'asc');
    }
    public function detalleDiario(){
        return $this->hasMany(Detalle_Diario::class, 'deposito_id', 'deposito_id');
    }
    public function cuentaBancaria()
    {
        return $this->belongsTo(Cuenta_Bancaria::class, 'cuenta_bancaria_id', 'cuenta_bancaria_id');
    }
    public function chequeCliente(){
        return $this->hasOne(Cheque_Cliente::class, 'deposito_id', 'deposito_id');
    }
    public function scopeDepositoSumatoria($query, $idCuentaBancaria,$fechaHasta){
        return $query->select(DB::raw('SUM(deposito_valor) as sumadeposito')) 
        ->where('deposito.cuenta_bancaria_id','=',$idCuentaBancaria)      
        ->where('empresa_id','=',Auth::user()->empresa_id)
        ->where('deposito_conciliacion','=',true)
        ->where('deposito_estado','=','1')
        ->where('deposito_fecha','<=',$fechaHasta)
        ->where('deposito_fecha_conciliacion','<=',$fechaHasta);

    }
}
