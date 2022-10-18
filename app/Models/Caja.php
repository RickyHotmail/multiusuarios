<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Caja extends Model
{
    use HasFactory;
    protected $table='caja';
    protected $primaryKey = 'caja_id';
    public $timestamps = true;
    protected $fillable = [        
        'caja_nombre',
        'caja_estado',         
        'empresa_id',
        'cuenta_id',
        'sucursal_id',
    ];
    protected $guarded =[
    ];
    public function scopeCajas($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('caja_estado','=','1')->orderBy('caja_nombre','asc');
    }
    public function scopeCaja($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('caja_id','=',$id);
    }
    public function scopeCajaSucursal($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('sucursal_id','=',$id);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function cuenta(){
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'cuenta_id');
    }
    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }        
}
