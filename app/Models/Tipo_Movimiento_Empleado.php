<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Movimiento_Empleado extends Model
{
    use HasFactory;
    protected $table='tipo_movimiento_empleado';
    protected $primaryKey = 'tipo_id';
    public $timestamps=true;
    protected $fillable = [
        'tipo_nombre', 
        'tipo_estado',    
        'empresa_id',
        'cuenta_id',
    ];
    protected $guarded =[
    ];  
    public function scopeTipoMovimientos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_estado','=','1')->orderBy('tipo_nombre','asc');
    }
    public function scopeTipoMovimiento($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_id','=',$id);
    }
    public function scopeTipoCuenta($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('cuenta_id','=',$id);
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function scopeTipoMovimientoCuentaBancaria($query, $id){
        return $query->join('cuenta','cuenta.cuenta_id','=','tipo_movimiento_empleado.cuenta_id')->where('tipo_movimiento_empleado.empresa_id','=',Auth::user()->empresa_id)->where('tipo_id','=',$id);
    }
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'cuenta_id');
    }
}
