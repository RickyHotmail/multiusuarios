<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Depreciacion_Activo_Fijo extends Model
{
    use HasFactory;
    protected $table='depreciacion_activo_fijo';
    protected $primaryKey = 'depreciacion_id';
    public $timestamps = true;
    protected $fillable = [        
        'depreciacion_fecha',
        'depreciacion_valor', 
        'depreciaciacion_estado',
        'activo_id',
        'diario_id',
    ];
    protected $guarded =[
    ];   
    public function scopeDepreciacionActivos($query){
        return $query->join('activo_fijo','activo_fijo.activo_id','=','depreciacion_activo_fijo.activo_id')->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('depreciacion_activo_fijo.depreciacion_estado','=','1')->orderBy('depreciacion_activo_fijo.depreciacion_fecha','desc');
    }
    public function scopeDepreciacionActivo($query, $id){
        return $query->join('activo_fijo','activo_fijo.activo_id','=','depreciacion_activo_fijo.activo_id')->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('depreciacion_id','=',$id);
    } 
    public function scopeDepreciacionActivoxFechas($query, $fecha,$diario){
        return $query->join('activo_fijo','activo_fijo.activo_id','=','depreciacion_activo_fijo.activo_id')->join('grupo_activo','grupo_activo.grupo_id','=','activo_fijo.grupo_id')->join('sucursal','sucursal.sucursal_id','=','grupo_activo.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('depreciacion_fecha','=',$fecha)->where('depreciacion_activo_fijo.diario_id','=',$diario);
    }
    public function scopeDepreciacionActivoxDiario($query, $id){
        return $query->where('diario_id','=',$id);
    }      
    public function scopeSumactivoDepreciacion($query, $id, $fech){
        return $query->select(DB::raw('SUM(depreciacion_valor) as depreciacionValorAcum'))->where('depreciacion_activo_fijo.activo_id','=',$id)->where('depreciacion_fecha','<=',$fech);
    }
    public function scopeSumactivoDepreciacionFechas($query, $id, $fech1, $fech2){
        return $query->select(DB::raw('SUM(depreciacion_valor) as depreciacionValorAcum'))->where('depreciacion_activo_fijo.activo_id','=',$id)->where('depreciacion_fecha','>=',$fech1)->where('depreciacion_fecha','<=',$fech2);
    }
    public function activoFijo(){
        return $this->belongsTo(Activo_Fijo::class, 'activo_id', 'activo_id');
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
}
