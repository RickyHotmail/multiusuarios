<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Transferencia extends Model
{
    use HasFactory;
    protected $table ='transferencia';
    protected $primaryKey = 'transferencia_id';
    public $timestamps = true;
    protected $fillable = [        
        'transferencia_descripcion', 
        'transferencia_beneficiario',
        'transferencia_fecha',   
        'transferencia_valor',
        'transferencia_estado',
        'cuenta_bancaria_id',
        'empresa_id', 
        'transferencia_conciliacion',
        'transferencia_fecha_conciliacion',               
    ];
    protected $guarded =[
    ];

    public function scopeTransferencias($query){
        return $query->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')->join('cuenta','cuenta.cuenta_id','=','cuenta_bancaria.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('transferencia_estado','=','1')->orderBy('transferencia_numero','asc');
    }
    public function scopeTransferencia($query, $id){
        return $query->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')->join('cuenta','cuenta.cuenta_id','=','cuenta_bancaria.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('transferencia_id','=',$id);
    }
    public function scopeTransferenciaByCuenta($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('transferencia.cuenta_bancaria_id','=',$id)->orderby('transferencia.transferencia_fecha','asc');
    }
    public function scopeTransferenciaOtrosByCuenta($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('transferencia.cuenta_bancaria_id','=',$id)->orderby('transferencia.transferencia_fecha','asc');
    }
  
    public function detalleDiario()
    {
        return $this->hasMany(Detalle_Diario::class, 'transferencia_id', 'transferencia_id');
    }
    public function cuentaBancaria()
    {
        return $this->belongsTo(Cuenta_Bancaria::class, 'cuenta_bancaria_id', 'cuenta_bancaria_id');
    }
    public function egresoBanco()
    {
        return $this->belongsTo(Egreso_Banco::class, 'transferencia_id', 'transferencia_id');
    }
    public function scopeTransferenciasSumtorias($query,$idCuentaBancaria,$fechaHasta){
        return $query->select(DB::raw('SUM(transferencia_valor) as sumatransferencia'))
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','transferencia.cuenta_bancaria_id')
        ->join('cuenta','cuenta.cuenta_id','=','cuenta_bancaria.cuenta_id')
        ->where('cuenta.empresa_id','=',Auth::user()->empresa_id)
        ->where('transferencia_estado','=','1')
        ->where('transferencia.cuenta_bancaria_id','=',$idCuentaBancaria)    
        ->where('transferencia_conciliacion','=',true)       
        ->where('transferencia_fecha','<=',$fechaHasta)
        ->where('transferencia_fecha_conciliacion','<=',$fechaHasta);
    }
}