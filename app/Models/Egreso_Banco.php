<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Egreso_Banco extends Model
{
    use HasFactory;
    protected $table ='egreso_banco';
    protected $primaryKey = 'egreso_id';
    public $timestamps = true;
    protected $fillable = [        
        'egreso_numero',
        'egreso_serie',
        'egreso_secuencial',
        'egreso_fecha',
        'egreso_valor',
        'egreso_descripcion',
        'egreso_beneficiario', 
        'egreso_estado',  
        'tipo_id', 
        'diario_id',
        'cuenta_bancaria_id',     
        'rango_id',
        'cheque_id',
        'transferencia_id'           
    ];
    protected $guarded =[
    ];
    public function scopeEgresoBancos($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','egreso_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('egreso_estado','=','1')->orderBy('egreso_fecha','asc');
    }
    public function scopeReporteEgresoBancos($query){
        return $query->join('rango_documento','rango_documento.rango_id','=','egreso_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('egreso_estado','=','1');
    }
    public function scopeEgresoBanco($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','egreso_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('egreso_id','=',$id);
    }
    public function scopeEgresoBancoByCuenta($query, $id, $fechaInicio, $fechaFin){
        return $query->join('rango_documento','rango_documento.rango_id','=','egreso_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('egreso_banco.cuenta_bancaria_id','=',$id)->where('egreso_banco.egreso_fecha','>=',$fechaInicio)->where('egreso_banco.egreso_fecha','<=',$fechaFin)->orderby('egreso_banco.egreso_fecha');
    }
    public function scopeSecuencial($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','egreso_banco.rango_id')->join('tipo_comprobante','tipo_comprobante.tipo_comprobante_id','=','rango_documento.tipo_comprobante_id')->where('tipo_comprobante.empresa_id','=',Auth::user()->empresa_id)->where('rango_documento.rango_id','=',$id);

    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function cheque()
    {
        return $this->belongsTo(Cheque::class, 'cheque_id', 'cheque_id');
    }
    public function transferencia()
    {
        return $this->belongsTo(Transferencia::class, 'transferencia_id', 'transferencia_id');
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
