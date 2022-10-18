<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tarjeta_Credito extends Model
{
    use HasFactory;
    protected $table='tarjeta_credito';
    protected $primaryKey = 'tarjeta_id';
    public $timestamps = true;
    protected $fillable = [        
        'tarjeta_nombre',               
        'tarjeta_estado', 
        'cuenta_id',
        'empresa_id',        
    ];
    protected $guarded =[
    ]; 
    public function scopeTarjetasCredito($query)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('tarjeta_estado', '=', '1');
    }

    public function scopeTarjetaCuenta($query)
    {
        return $query->join('cuenta', 'cuenta.cuenta_id','=','tarjeta_credito.cuenta_id'
                    )->where('tarjeta_credito.empresa_id', '=', Auth::user()->empresa_id)->where('tarjeta_credito.tarjeta_estado', '=', '1');
    }  

    public function scopeTarjetaCredito($query, $id)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('tarjeta_id', '=', $id);
    }
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
