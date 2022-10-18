<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class sucursal extends Model
{
    use HasFactory;
    protected $table='sucursal';
    protected $primaryKey = 'sucursal_id';
    public $timestamps=true;
    protected $fillable = [
        'sucursal_nombre',
        'sucursal_codigo',
        'sucursal_direccion',
        'sucursal_telefono', 
        'empresa_id',
        'sucursal_estado',  
    ];
    protected $guarded =[
    ];    
    protected static function booted()
    {
        static::created(function ($sucursal) {
            foreach(Cuentas_Parametrizar::cuentas()->get() as $cuenta){
                $parametrizacionContable = new Parametrizacion_Contable();
                $parametrizacionContable->parametrizacion_nombre = $cuenta->parametrizar_nombre;
                $parametrizacionContable->parametrizacion_cuenta_general ="1";   
                $parametrizacionContable->parametrizacion_estado  = 1;
                $parametrizacionContable->parametrizacion_orden = $cuenta->parametrizar_orden;
                $parametrizacionContable->sucursal()->associate($sucursal);
                $parametrizacionContable->save();
            }
        });
    }
    public function scopeSucursales($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('sucursal_estado','=','1')->orderBy('sucursal_codigo','asc');
    }
    public function scopeSucursalesDistinc($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('sucursal_estado','=','1')->orderBy('sucursal_nombre','asc');
    }
    public function scopeSucursal($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('sucursal_id','=',$id);
    }
    public function scopeSucursalByNombre($query, $nom){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('sucursal_nombre','=',$nom);
    }
    public function scopeSucursalByContable($query, $id, $nom){
        return $query->join('parametrizacion_contable','parametrizacion_contable.sucursal_id','=','sucursal.sucursal_id')->join('cuenta','cuenta.cuenta_id','=','parametrizacion_contable.cuenta_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('sucursal.sucursal_id','=',$id)->where('parametrizacion_nombre','=',$nom);
    }
    public function scopeSucursalId($query, $buscar){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('sucursal_estado','=','1')->where('sucursal_id','=',$buscar)->orderBy('sucursal_nombre','asc');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function parametrizacionContable(){
        return $this->hasMany(Parametrizacion_Contable::class, 'sucursal_id', 'sucursal_id');
    }
    
}
