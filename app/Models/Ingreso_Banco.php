<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Ingreso_Banco extends Model
{
    use HasFactory;
    protected $table ='ingreso_banco';
    protected $primaryKey = 'ingreso_id';
    public $timestamps = true;
    protected $fillable = [        
        'ingreso_numero',
        'ingreso_serie',
        'ingreso_secuencial',
        'ingreso_fecha',
        'ingreso_valor',
        'ingreso_descripcion',
        'ingreso_beneficiario', 
        'ingreso_estado',  
        'tipo_id', 
        'diario_id',
        'cuenta_bancaria_id',     
        'rango_id',
        'deposito_id',          
    ];
    protected $guarded =[
    ];
    public function scopeingresoBancos($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','ingreso_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('ingreso_estado','=','1')->orderBy('ingreso_fecha','asc');
    }
    public function scopeReporteingresoBancos($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','ingreso_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('ingreso_estado','=','1');
    }
    public function scopeingresoBanco($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','ingreso_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('ingreso_id','=',$id);
    }
    public function scopeIngresoBancoByCuenta($query, $id, $fechaInicio, $fechaFin){
        return $query->join('rango_documento','rango_documento.rango_id','=','ingreso_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('ingreso_banco.cuenta_bancaria_id','=',$id)->where('ingreso_banco.ingreso_fecha','>=',$fechaInicio)->where('ingreso_banco.ingreso_fecha','<=',$fechaFin)->orderby('ingreso_banco.ingreso_fecha');
    }
    public function scopeSecuencial($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','ingreso_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('rango_documento.rango_id','=',$id);
    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function deposito()
    {
        return $this->belongsTo(Deposito::class, 'deposito_id', 'deposito_id');
    }
    public function tipoMovimientoBanco()
    {
        return $this->belongsTo(Tipo_Movimiento_Banco::class, 'tipo_id', 'tipo_id');
    }
    public function cuentaBancaria()
    {
        return $this->belongsTo(Cuenta_Bancaria::class, 'cuenta_bancaria_id', 'cuenta_bancaria_id');
    }
}
