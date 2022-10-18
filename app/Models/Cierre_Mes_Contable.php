<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cierre_Mes_Contable extends Model
{
    use HasFactory;
    protected $table='cierre_mes_contable';
    protected $primaryKey = 'cierre_id';
    public $timestamps=true;
    protected $fillable = [
        'cierre_ano',
        'cierre_01', 
        'cierre_02',
        'cierre_03',
        'cierre_04',      
        'cierre_05',
        'cierre_06',
        'cierre_07',
        'cierre_08',
        'cierre_09',
        'cierre_10',
        'cierre_11',
        'cierre_12',
        'cierre_estado',        
        'sucursal_id',           
    ];
    protected $guarded =[
    ];
    public function scopeCierreBySucursal($query,$sucursal_id){
        return $query->join('sucursal','cierre_mes_contable.sucursal_id','=','sucursal.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cierre_mes_contable.sucursal_id','=',$sucursal_id)->where('cierre_estado','=','1')->orderBy('cierre_ano','asc');
    }
    public function scopeCierreAnioSucursal($query,$anio,$sucursal_id){
        return $query->join('sucursal','cierre_mes_contable.sucursal_id','=','sucursal.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cierre_mes_contable.cierre_ano','=',$anio)->where('cierre_mes_contable.sucursal_id','=',$sucursal_id)->where('cierre_estado','=','1')->orderBy('cierre_ano','asc');
    }
    /*
    public function scopeCierre($query,$anio){
        return $query->join('sucursal','cierre_mes_contable.sucursal_id','=','sucursal.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cierre_ano','=',$anio)->where('cierre_estado','=','1')->orderBy('cierre_ano','asc');
    }
    */
    public function scopeCierre($query,$anio,$sucursal){
        return $query->join('sucursal','cierre_mes_contable.sucursal_id','=','sucursal.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('cierre_ano','=',$anio)->where('sucursal.sucursal_id','=',$sucursal)->where('cierre_estado','=','1')->orderBy('cierre_ano','asc');
    }
    
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }
}
