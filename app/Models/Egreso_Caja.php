<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Egreso_Caja extends Model
{
    use HasFactory;
    protected $table ='egreso_caja';
    protected $primaryKey = 'egreso_id';
    public $timestamps = true;
    protected $fillable = [        
        'egreso_numero',
        'egreso_serie',
        'egreso_secuencial',
        'egreso_fecha',
        'egreso_tipo', 
        'egreso_valor',
        'egreso_descripcion',
        'egreso_beneficiario',    
        'arqueo_id', 
        'diario_id',
        'rango_id',
        'tipo_id',
        'egreso_estado'    
    ];
    protected $guarded =[
    ];
    public function scopeEgresoCajas($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','egreso_caja.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('egreso_estado','=','1')->orderBy('egreso_fecha','asc');
    }
    public function scopeReporteEgresoCajas($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','egreso_caja.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('egreso_estado','=','1');
    }
    public function scopeEgresoCaja($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','egreso_caja.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('egreso_id','=',$id);
    }
    public function scopeEgresoCajaIdArqueo($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','egreso_caja.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('egreso_caja.arqueo_id','=',$id);
    }
    public function scopeEgresoCajaIdArqueoSuma($query, $id){
        return $query->select(DB::raw('SUM(egreso_valor) as sumaEgreso'))->join('rango_documento','rango_documento.rango_id','=','egreso_caja.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('egreso_caja.arqueo_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','egreso_caja.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('rango_documento.rango_id','=',$id);
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
