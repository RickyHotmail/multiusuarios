<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Transaccion extends Model
{
    use HasFactory;
    protected $table='tipo_transaccion';
    protected $primaryKey = 'tipo_transaccion_id';
    public $timestamps=true;
    protected $fillable = [
        'tipo_transaccion_nombre',
        'tipo_transaccion_codigo',         
        'tipo_transaccion_estado',       
        'empresa_id',    
                 
    ];
    protected $guarded =[
    ];
    public function scopeTipoTransacciones($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_transaccion_estado','=','1')->orderBy('tipo_transaccion_codigo','asc');
    }
    public function scopeTipoTransaccion($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_transaccion_id','=',$id);
    
    }
}
