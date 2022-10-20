<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cuenta extends Model
{
    use HasFactory;
    protected $table='cuenta';
    protected $primaryKey = 'cuenta_id';
    public $timestamps=true;
    protected $fillable = [
        'cuenta_numero',
        'cuenta_nombre',       
        'cuenta_secuencial',        
        'cuenta_nivel',
        'cuenta_estado',
        'cuenta_padre_id',
        'empresa_id',            
    ];
    protected $guarded =[
    ];
    
    public function scopeNivel($query, $cuentaPadre){
        if ($cuentaPadre == 0){
            return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->whereNull('cuenta_padre_id');
        }else{
            return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('cuenta_padre_id','=',$cuentaPadre);
        }
    }
    public function scopeNivelPadre($query, $cuentaPadre){
            return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('cuenta_numero','=',$cuentaPadre);
    }
    public function scopebuscarCuenta($query, $cuenta){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('cuenta_numero','=',$cuenta);
    }
    public function scopeBuscarByCuentaPadre($query, $cuentaPadre){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })
        ->where('cuenta_padre_id','=',$cuentaPadre);
    }
    public function scopeBuscarByCuenta($query, $cuenta){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })
        ->where('cuenta_nombre','like','%'.$cuenta.'%');
    }

    public function scopeCuentasNivel($query){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('cuenta_estado','=','1')->orderBy('cuenta_nivel','desc');
    }
    public function scopeCuentas($query){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('cuenta_estado','=','1')->orderBy('cuenta_numero','asc');
    }
    public function scopeCuentasMovimiento($query){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('cuenta_estado','=','1')
        ->groupBy('cuenta_id','cuenta_numero','cuenta_nombre')
        ->havingRaw(DB::raw('(select count(*) from cuenta as hijas where cuenta.cuenta_id=hijas.cuenta_padre_id ) = 0'))
        ->orderBy('cuenta_numero','asc');
    }

    public function scopeMayorAuxiliar($query, $cuenta_ini, $cuenta_fin, $desde, $hasta, $sucursal){
        $query->raw("
            Select
                c.*,
                coalesce(
                (select SUM(detalle_debe)-SUM(detalle_haber) as saldo
                from detalle_diario 
                inner join diario on diario.diario_id = detalle_diario.diario_id
                where  empresa_id is null  or empresa_id = ".Auth::user()->empresa_id." and diario.diario_fecha < '$desde' and detalle_diario.cuenta_id=c.cuenta_id limit 1), 0)
                as saldo,
                dt.detalle_debe, dt.detalle_haber, dt.detalle_comentario, dt.detalle_tipo_documento, dt.detalle_numero_documento
            from 
                cuenta as c, detalle_diario as dt, diario as d
            where 
                c.empresa_id is null  or c.empresa_id = ".Auth::user()->empresa_id." and c.cuenta_estado = '1' 
                and c.cuenta_numero >= '$cuenta_ini' and c.cuenta_numero <= '$cuenta_fin' 
                and dt.cuenta_id=c.cuenta_id
                and d.diario_id=dt.diario_id
                and d.diario_fecha >= '$desde' and d.diario_fecha <= '$hasta' 
            order by c.cuenta_numero asc
        ");

        //if($sucursal>0) $query->where('d.sucursal_id', '=', $sucursal);

        return $query;
    }

    public function scopeCuentasResultado($query){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('cuenta_estado','=','1')->where('cuenta_numero','like','4%')->orwhere('cuenta_numero','like','5%')->orwhere('cuenta_numero','like','6%');
    }
    public function scopeCuentasFinanciero($query){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('cuenta_estado','=','1')->where('cuenta_numero','like','1%')->orwhere('cuenta_numero','like','2%')->orwhere('cuenta_numero','like','3%');
    }
    public function scopeCuentasDesc($query){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('cuenta_estado','=','1')->orderBy('cuenta_numero','desc');
    }
    public function scopeCuenta($query, $id){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('cuenta_id','=',$id);
    }
    public function scopeCuentaByNumero($query, $numero){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('cuenta_numero','=',$numero);
    }
    public function scopeCuentasRango($query,$cuentaInicio,$cuentaFin){
        return $query->where(function ($query){ $query->whereNull('empresa_id')->orwhere('empresa_id','=',Auth::user()->empresa_id); })->where('cuenta_estado','=','1')->where('cuenta_numero','>=',$cuentaInicio)->where('cuenta_numero','<=',$cuentaFin)->orderBy('cuenta_numero','asc');
    }
    public function cuentaPadre()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_padre_id', 'cuenta_id');
    }
    public function cuentasHija()
    {
        return $this->hasMany(Cuenta::class, 'cuenta_padre_id', 'cuenta_id');
    }
}
