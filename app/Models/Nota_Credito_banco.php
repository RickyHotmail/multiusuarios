<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Nota_Credito_banco extends Model
{
    use HasFactory;
    protected $table ='nota_credito_banco';
    protected $primaryKey = 'nota_id';
    public $timestamps = true;
    protected $fillable = [        
        'nota_numero',
        'nota_serie',
        'nota_secuencial',
        'nota_fecha',
        'nota_valor',
        'nota_descripcion',
        'nota_beneficiario', 
        'nota_estado',  
        'diario_id',
        'rango_id',
        'cuenta_bancaria_id', 
        'nota_conciliacion',
        'nota_fecha_conciliacion',       
    ];
    protected $guarded =[
    ];
    public function scopeNotasCreditoBancos($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('nota_estado','=','1')->orderBy('nota_fecha','asc');
    }
    public function scopeNotaCreditoBanco($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('nota_id','=',$id);
    }
    public function scopeNCbancoByCuenta($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('nota_credito_banco.cuenta_bancaria_id','=',$id)->orderby('nota_credito_banco.nota_fecha','asc');
    }
    public function scopeNCbancoOtrosByCuenta($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('nota_credito_banco.cuenta_bancaria_id','=',$id)->orderby('nota_credito_banco.nota_fecha','asc');
    }
    public function scopeSecuencial($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','nota_credito_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('rango_documento.rango_id','=',$id);
    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function cuentaBancaria()
    {
        return $this->belongsTo(Cuenta_Bancaria::class, 'cuenta_bancaria_id', 'cuenta_bancaria_id');
    }
    public function rangoDocumento()
    {
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
    public function detallesTipoMovimiento(){
        return $this->hasMany(movimiento_nota_credito::class, 'nota_id', 'nota_id');
    }
    public function scopeNotaCreditoSumatoria($query, $idCuentaBancaria,$fechaHasta){
        return $query->select(DB::raw('SUM(nota_valor) as sumanotacredito'))
        ->join('rango_documento','rango_documento.rango_id','=','nota_credito_banco.rango_id')
        ->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')
        ->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)
        ->where('nota_estado','=','1') 
        ->where('cuenta_bancaria_id','=',$idCuentaBancaria)      
        ->where('nota_conciliacion','=',true)        
        ->where('nota_fecha','<=',$fechaHasta)
        ->where('nota_fecha_conciliacion','<=',$fechaHasta);

    }
}
