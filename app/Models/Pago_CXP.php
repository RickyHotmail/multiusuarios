<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Pago_CXP extends Model
{
    use HasFactory;
    protected $table='pago_cxp';
    protected $primaryKey = 'pago_id';
    public $timestamps=true;
    protected $fillable = [
        'pago_descripcion',
        'pago_fecha',       
        'pago_tipo',        
        'pago_valor',       
        'pago_estado',    
        'diario_id',
        'arqueo_id'
    ];
    protected $guarded =[
    ];
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function scopePagoArqueoID($query, $arqueo){
        return $query->join('diario','diario.diario_id','=','pago_cxp.diario_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('pago_cxp.arqueo_id','=',$arqueo);
    }
    public function arqueoCaja(){
        return $this->belongsTo(Arqueo_Caja::class, 'arqueo_id', 'arqueo_id');
    }
    public function detalles(){
        return $this->hasMany(Detalle_Pago_CXP::class, 'pago_id', 'pago_id');
    }
}
