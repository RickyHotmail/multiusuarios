<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Rango_Documento extends Model
{
    use HasFactory;
    protected $table='rango_documento';
    protected $primaryKey = 'rango_id';
    public $timestamps=true;
    protected $fillable = [
        'rango_descripcion',
        'rango_inicio', 
        'rango_fin',    
        'rango_fecha_inicio',
        'rango_fecha_fin',
        'rango_autorizacion', 
        'rango_estado',
        'tipo_comprobante_id',
        'punto_id',
    ];
    protected $guarded =[
    ];
    public function scopeRangos($query){
        return $query->join('tipo_comprobante', 'tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
                    ->join('punto_emision', 'punto_emision.punto_id','=','rango_documento.punto_id')
                    ->join('sucursal', 'sucursal.sucursal_id','=','punto_emision.sucursal_id')
                    ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
                    ->where('rango_estado','=','1')
                    ->orderBy('rango_descripcion','asc');
    }
    public function scopeRango($query, $id){
        return $query->join('tipo_comprobante', 'tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('rango_id','=',$id); 
    }         
    public function scopePuntoRango($query, $id, $tipoDocumento){
        return $query->join('tipo_comprobante', 'tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.tipo_comprobante_nombre','=',$tipoDocumento)->where('punto_id','=',$id); 
    }
    public function scopePuntoRangoNombre($query, $tipoDocumento){
        return $query->join('tipo_comprobante', 'tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.tipo_comprobante_nombre','=',$tipoDocumento); 
    }
    public function tipoComprobante()
    {
        return $this->belongsTo(Tipo_Comprobante::class, 'tipo_comprobante_id', 'tipo_comprobante_id');
    }
    public function puntoEmision()
    {
        return $this->belongsTo(Punto_Emision::class, 'punto_id', 'punto_id');
    }
    public function empresa()
    {
        return $this->hasOneThrough(Empresa::class, Tipo_Comprobante::class,'tipo_comprobante_id','empresa_id','tipo_comprobante_id','empresa_id');
    }
}
