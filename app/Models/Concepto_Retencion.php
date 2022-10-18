<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Concepto_Retencion extends Model
{
    use HasFactory;
    protected $table='concepto_retencion';
    protected $primaryKey = 'concepto_id';
    public $timestamps = true;
    protected $fillable = [        
        'concepto_nombre',
        'concepto_codigo',
        'concepto_porcentaje',                
        'concepto_tipo',
        'concepto_objeto',
        'concepto_estado',
        'concepto_emitida_cuenta',
        'concepto_recibida_cuenta',
        'empresa_id',        
    ];
    protected $guarded =[
    ];   
    public function scopeConceptoRetenciones($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('concepto_estado','=','1')->orderBy('concepto_nombre','asc');
    }
    public function scopeConceptoRetencion($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('concepto_id','=',$id);
    }
    public function scopeConceptoRetencionByCodigo($query, $codigo){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('concepto_codigo','=',$codigo);
    }
    public function scopeConceptosFuente($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('concepto_estado','=','1')->where('concepto_tipo','=','1')->orderBy('concepto_nombre','asc');
    }
    public function scopeConceptosIva($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('concepto_estado','=','1')->where('concepto_tipo','=','2')->orderBy('concepto_nombre','asc');
    }   
    public function cuentaEmitida(){
        return $this->belongsTo(Cuenta::class, 'concepto_emitida_cuenta', 'cuenta_id');
    }
    public function cuentaRecibida(){
        return $this->belongsTo(Cuenta::class, 'concepto_recibida_cuenta', 'cuenta_id');
    }   

}
