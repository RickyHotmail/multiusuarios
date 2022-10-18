<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Casillero_tributario extends Model
{
    use HasFactory;
    protected $table='casillero_tributario';
    protected $primaryKey = 'casillero_id';
    public $timestamps = true;
    protected $fillable = [        
        'casillero_codigo',
        'casillero_descripcion',         
        'casillero_tipo',
        'casillero_estado',
        'casillero_empresa',
    ];
    protected $guarded =[
    ];
    public function scopeCasillerosTributarios($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('casillero_estado','=','1')->orderBy('casillero_codigo','asc');
    }
    public function scopeCasilleroTributario($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('casillero_estado','=','1')->where('casillero_id','=',$id);
    }    
    public function scopeCasilleroTributarioPorCodigo($query, $codigo){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('casillero_codigo','=',$codigo);
    }
}
