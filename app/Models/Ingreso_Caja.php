<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Ingreso_Caja extends Model
{
    use HasFactory;
    protected $table ='ingreso_caja';
    protected $primaryKey = 'ingreso_id';
    public $timestamps = true;
    protected $fillable = [
        'ingreso_numero',
        'ingreso_serie',
        'ingreso_secuencial',        
        'ingreso_fecha',
        'ingreso_tipo', 
        'ingreso_valor',
        'ingreso_descripcion',
        'ingreso_beneficiario',    
        'arqueo_id', 
        'diario_id',
        'rango_id',
        'tipo_id',
        'ingreso_estado'    
    ];
    protected $guarded =[
    ];
    public function scopeIngresoCajas($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','ingreso_caja.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('ingreso_estado','=','1')->orderBy('ingreso_fecha','asc');
    }
    public function scopeReporteIngresoCajas($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','ingreso_caja.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('ingreso_estado','=','1');
    }
    public function scopeIngresoCaja($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','ingreso_caja.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('ingreso_id','=',$id);
    }
    public function scopeIngresoCajaxArqueo($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','ingreso_caja.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('ingreso_caja.arqueo_id','=',$id);
    }
    public function scopeIngresoCajaxArqueoSuma($query, $id){
        return $query->select(DB::raw('SUM(ingreso_valor) as sumaIngreso'))->join('rango_documento','rango_documento.rango_id','=','ingreso_caja.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('ingreso_caja.arqueo_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','ingreso_caja.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('rango_documento.rango_id','=',$id);

    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function movimiento()
    {
        return $this->belongsTo(Tipo_Movimiento_Caja::class, 'tipo_id', 'tipo_id');
    }
    public function arqueo()
    {
        return $this->belongsTo(Arqueo_Caja::class, 'arqueo_id', 'arqueo_id');
    }
    
    
}
