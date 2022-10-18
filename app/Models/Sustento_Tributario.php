<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Sustento_Tributario extends Model
{
    use HasFactory;
    protected $table='sustento_tributario';
    protected $primaryKey = 'sustento_id';
    public $timestamps=true;
    protected $fillable = [
        'sustento_nombre',
        'sustento_codigo',
        'sustento_credito',
        'sustento_venta12',
        'sustento_venta0',
        'sustento_compra12',
        'sustento_compra0',
        'sustento_estado',    
        'empresa_id',
    ];
    protected $guarded =[
    ];  
    public function scopeSustentos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('sustento_estado','=','1')->orderBy('sustento_codigo','asc');
    }
    public function scopeSustento($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('sustento_id','=',$id);
    }
    public function empressa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
