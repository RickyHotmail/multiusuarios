<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Forma_Pago extends Model
{
    use HasFactory;
    protected $table='forma_pago';
    protected $primaryKey = 'forma_pago_id';
    public $timestamps = true;
    protected $fillable = [        
        'forma_pago_nombre',
        'forma_pago_codigo',                
        'empresa_id',
        'forma_pago_estado',         
    ];
    protected $guarded =[
    ];   
    public function scopeFormaPagos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('forma_pago_estado','=','1')->orderBy('forma_pago_codigo','asc');
    }
    public function scopeFormaPago($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('forma_pago_id','=',$id);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }

}
