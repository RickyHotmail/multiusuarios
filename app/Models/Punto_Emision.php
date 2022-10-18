<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Punto_Emision extends Model
{
    use HasFactory;
    protected $table='punto_emision';
    protected $primaryKey = 'punto_id';
    public $timestamps=true;
    protected $fillable = [
        'punto_serie',
        'punto_descripcion',        
        'punto_estado',        
        'sucursal_id',       
    ];
    protected $guarded =[
    ];
    public function scopePuntos($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('punto_estado','=','1')->orderBy('punto_id','asc');
    }
    public function scopePunto($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('punto_id','=',$id);
    }
    public function scopePuntoSucursalUser($query, $sucursal,$user){
        return $query->join('usuario_puntoe','usuario_puntoe.punto_id','=','punto_emision.punto_id')->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('sucursal.sucursal_id','=',$sucursal)->where('usuario_puntoe.user_id','=',$user);
    }
    public function scopePuntoxSerie($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('punto_serie','=',$id);
    }
    public function scopePuntoxSucursal($query, $sucursal_id){
        return $query->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('punto_emision.sucursal_id','=',$sucursal_id);
    }
    public function scopePuntoSucursalRango($query, $sucursal_id){
        return $query->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')->join('rango_documento','rango_documento.punto_id','=','punto_emision.punto_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('tipo_comprobante.tipo_comprobante_nombre','=','Quincena')->where('punto_emision.sucursal_id','=',$sucursal_id);
    }
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }
    public function rangos(){
        return $this->hasMany(Rango_Documento::class, 'punto_id', 'punto_id');
    }
}
