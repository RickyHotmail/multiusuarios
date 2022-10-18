<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Bodega extends Model
{
    use HasFactory;
    protected $table='bodega';
    protected $primaryKey = 'bodega_id';
    public $timestamps=true;
    protected $fillable = [        
        'bodega_nombre',
        'bodega_descripcion', 
        'bodega_direccion',
        'bodega_telefono',
        'bodega_fax',
        'bodega_estado',         
        'ciudad_id',  
        'sucursal_id',  
    ];
    protected $guarded =[
    ];
    public function scopeBodegas($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('bodega_estado','=','1')->orderBy('bodega_nombre','asc');
    }
    public function scopeBodega($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('bodega_id','=',$id);
    }
    public function scopeBodegasSucursal($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->join('punto_emision','punto_emision.sucursal_id','=','bodega.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('punto_id','=',$id);
    }
    public function scopeSucursalBodega($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','bodega.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('sucursal.sucursal_id','=',$id);
    }
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id', 'ciudad_id');
    }
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }
}
