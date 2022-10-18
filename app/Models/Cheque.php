<?php

namespace App\Models;

use App\Observers\RangoChequeObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cheque extends Model
{
    use HasFactory;
    protected $table ='cheque';
    protected $primaryKey = 'cheque_id';
    public $timestamps = true;
    protected $fillable = [        
        'cheque_numero',
        'cheque_descripcion', 
        'cheque_beneficiario',
        'cheque_fecha_emision',
        'cheque_fecha_pago',
        'cheque_valor',
        'cheque_valor_letras',
        'cheque_estado',
        'cuenta_bancaria_id',
        'empresa_id',
        'cheque_conciliacion',
        'cheque_fecha_conciliacion',         
    ];
    protected $guarded =[
    ];
    protected static function booted()
    {
        Cheque::observe(RangoChequeObserver::class);
    }
    public function scopeCheques($query){
        return $query->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')->join('cuenta','cuenta.cuenta_id','=','cuenta_bancaria.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('cheque_estado','=','1')->orderBy('cheque_numero','asc');
    }
    public function scopeCheque($query, $id){
        return $query->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')->join('cuenta','cuenta.cuenta_id','=','cuenta_bancaria.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('cheque_id','=',$id);
    }
    public function scopelistadoCheques($query){
        return $query->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')->join('banco','banco.banco_id','=','cuenta_bancaria.banco_id')->join('banco_lista','banco_lista.banco_lista_id','=','banco.banco_lista_id');
    }
    public function scopeChequeByCuenta($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('cheque.cuenta_bancaria_id','=',$id)->where('cheque.cheque_estado','=','1')->orderby('cheque.cheque_numero','asc');
    }
    public function scopeChequeByCuentaAnulados($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('cheque.cuenta_bancaria_id','=',$id)->where('cheque.cheque_estado','=','2')->orderby('cheque.cheque_numero','asc');
    }
    public function scopeChequeByCuentaActivoAnulado($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('cheque.cuenta_bancaria_id','=',$id)->orderby('cheque.cheque_numero','asc');
    }
    public function scopeChequeOtrosByCuenta($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('cheque.cuenta_bancaria_id','=',$id)->where('cheque.cheque_estado','=','1')->orderby('cheque.cheque_numero','asc');
    }
    public function scopeChequeSumaByCuenta($query, $id, $fechaHasta){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('cheque.cuenta_bancaria_id','=',$id)->where('cheque.cheque_estado','=','1')->where('cheque.cheque_fecha_emision','<=',$fechaHasta)->orderby('cheque.cheque_fecha_emision');
    }
    public function detalleDiario()
    {
        return $this->hasMany(Detalle_Diario::class, 'cheque_id', 'cheque_id');
    }
    public function cuentaBancaria()
    {
        return $this->belongsTo(Cuenta_Bancaria::class, 'cuenta_bancaria_id', 'cuenta_bancaria_id');
    }
    public function empresa()
    {
        return $this->belongsTo(empresa::class, 'empresa_id', 'empresa_id');
    }
    public function scopeBuscarRol($query, $id){

        return $query->join('detalle_diario','cheque.cheque_id','=','detalle_diario.cheque_id')->join('diario','diario.diario_id','=','detalle_diario.diario_id')->join('cabecera_rol','diario.diario_id','=','cabecera_rol.diario_pago_id')->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('cabecera_rol.cabecera_rol_id','=',$id);
    }

    public function scopeChequeSumatoria($query, $idCuentaBancaria,$FechaHasta){
        return $query->select(DB::raw('SUM(cheque_valor) as sumacheque'))
        ->join('cuenta_bancaria','cuenta_bancaria.cuenta_bancaria_id','=','cheque.cuenta_bancaria_id')
        ->join('cuenta','cuenta.cuenta_id','=','cuenta_bancaria.cuenta_id')
        ->where('cuenta.empresa_id','=',Auth::user()->empresa_id)
        ->where('cheque_estado','=','1')
        ->where('cheque.cuenta_bancaria_id','=',$idCuentaBancaria)      
        ->where('cheque_conciliacion','=',true)        
        ->where('cheque_fecha_emision','<=',$FechaHasta)
        ->where('cheque_fecha_conciliacion','<=',$FechaHasta);

    }
    
}
