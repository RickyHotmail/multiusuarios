<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Parametrizacion_Contable extends Model
{
    use HasFactory;
    protected $table='parametrizacion_contable';
    protected $primaryKey = 'parametrizacion_id';
    public $timestamps=true;
    protected $fillable = [
        'parametrizacion_nombre',
        'parametrizacion_cuenta_general',    
        'parametrizacion_orden',
        'parametrizacion_estado', 
        'cuenta_id',      
        'sucursal_id',
    ];
    protected $guarded =[
    ];

    public function scopeParametrizaciones($query){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','parametrizacion_contable.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('parametrizacion_estado','=','1')->orderBy('parametrizacion_nombre','asc');
    }
    public function scopeParametrizacion($query, $id){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','parametrizacion_contable.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('parametrizacion_id','=',$id);
    }
    public function scopeParametrizacionBySucursal($query, $sucursal_id){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','parametrizacion_contable.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('parametrizacion_contable.sucursal_id','=',$sucursal_id);
    }    
    public function scopeParametrizacionByNombre($query, $sucursal, $nombre){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','parametrizacion_contable.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('parametrizacion_contable.sucursal_id','=',$sucursal)->where('parametrizacion_nombre','=',$nombre);
    }
    public function scopeParametrizacionByNombreFinanciero($query, $nombre){
        return $query->join('sucursal', 'sucursal.sucursal_id','=','parametrizacion_contable.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('parametrizacion_nombre','=',$nombre);
    }
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'cuenta_id');
    }
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }
}
