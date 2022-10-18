<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Documento_Anulado extends Model
{
    use HasFactory;
    protected $table='documento_anulado';
    protected $primaryKey = 'documento_anulado_id';
    public $timestamps=true;
    protected $fillable = [
        'documento_anulado_fecha',
        'documento_anulado_motivo',       
        'documento_anulado_estado',        
        'empresa_id', 
    ];
    protected $guarded =[
    ];

    public function scopeDocumentosByFecha($query, $fechaInicio, $fechaFin, $documento){
        if($documento == '1'){
            $query->join('factura_venta','factura_venta.documento_anulado_id','=','documento_anulado.documento_anulado_id');
        }
        if($documento == '2'){
            $query->join('nota_credito','nota_credito.documento_anulado_id','=','documento_anulado.documento_anulado_id');
        }
        if($documento == '3'){
            $query->join('nota_debito','nota_debito.documento_anulado_id','=','documento_anulado.documento_anulado_id');
        }
        if($documento == '4'){
            $query->join('retencion_compra','retencion_compra.documento_anulado_id','=','documento_anulado.documento_anulado_id');
        }
        if($documento == '5'){
            $query->join('liquidacion_compra','liquidacion_compra.documento_anulado_id','=','documento_anulado.documento_anulado_id');
        }
        if($documento == '6'){
            $query->join('guia_remision','guia_remision.documento_anulado_id','=','documento_anulado.documento_anulado_id');
        }
        $query->where('empresa_id','=',Auth::user()->empresa_id)->where('documento_anulado_fecha','>=',$fechaInicio)->where('documento_anulado_fecha','<=',$fechaFin);
        
        return $query;
    }
    public function facturaVenta()
    {
        return $this->hasOne(factura_venta::class, 'documento_anulado_id', 'documento_anulado_id');
    }
    public function notaCredito()
    {
        return $this->hasOne(Nota_Credito::class, 'documento_anulado_id', 'documento_anulado_id');
    }
    public function notaDebito()
    {
        return $this->hasOne(Nota_Debito::class, 'documento_anulado_id', 'documento_anulado_id');
    }
    public function retencion()
    {
        return $this->hasOne(Retencion_Compra::class, 'documento_anulado_id', 'documento_anulado_id');
    }
    public function liquidacion()
    {
        return $this->hasOne(Liquidacion_Compra::class, 'documento_anulado_id', 'documento_anulado_id');
    }
}
