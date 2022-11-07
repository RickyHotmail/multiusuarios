<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model{
    use HasFactory;
    protected $table='pago';
    protected $primaryKey='pago_id';

    protected $fillable=[
        'suscripcion_id',
        'plan_id',
        'pago_fecha',
        'pago_fecha_validacion',
        'pago_documento',
        'pago_comprobante',
        'pago_banco_nombre',
        'pago_banco_numero',
        'pago_valor',
        'pago_estado'
    ];
    protected $guarded=[
    ];

    public function scopeFindByBancoNumero($query, $banco_id, $cuenta_numero){
        return $query->where('pago_banco_nombre', '=', $banco_id)
                     ->where('pago_banco_numero','=',$cuenta_numero);
    }
}
