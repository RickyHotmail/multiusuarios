<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Rango_Cheque extends Model
{
    use HasFactory;
    protected $table='rango_cheque';
    protected $primaryKey = 'rango_id';
    public $timestamps=true;
    protected $fillable = [
        'rango_inicio',
        'rango_fin', 
        'cuenta_bancaria_id',      
        'rango_estado',      
    ];
    protected $guarded =[
    ];

    public function scopeRangosCheques($query){
        return $query->join('cuenta_bancaria', 'cuenta_bancaria.cuenta_bancaria_id','=','rango_cheque.cuenta_bancaria_id')->join('cuenta', 'cuenta.cuenta_id',"=",'cuenta_bancaria.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('rango_estado','=','1')->orderBy('rango_cheque','asc');
    }
    public function scopeRangoCheque($query, $id){
        return $query->join('cuenta_bancaria', 'cuenta_bancaria.cuenta_bancaria_id','=','rango_cheque.cuenta_bancaria_id')->join('cuenta', 'cuenta.cuenta_id',"=",'cuenta_bancaria.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('rango_id','=',$id);
        
    }
    public function scopeCuentaRangoCheque($query, $id){
        return $query->join('cuenta_bancaria', 'cuenta_bancaria.cuenta_bancaria_id','=','rango_cheque.cuenta_bancaria_id')->join('cuenta', 'cuenta.cuenta_id',"=",'cuenta_bancaria.cuenta_id')->where('cuenta.empresa_id','=',Auth::user()->empresa_id)->where('cuenta_bancaria.cuenta_bancaria_id','=',$id);
        
    }
    public function cuentaBancaria()
    {
        return $this->belongsTo(cuenta_bancaria::class, 'cuenta_bancaria_id', 'cuenta_bancaria_id');
    }
}
