<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Sobrante_Caja extends Model
{
    use HasFactory;
    protected $table='sobrante_caja';
    protected $primaryKey = 'sobrante_id';
    public $timestamps=true;
    protected $fillable = [
        'sobrante_numero',
        'sobrante_serie',
        'sobrante_secuencial',
        'sobrante_fecha', 
        'sobrante_observacion',
        'sobrante_estado',
        'arqueo_id', 
        'diario_id',
        'rango_id',
                    
    ];
    protected $guarded =[
    ];

    public function scopeSobrantes($query){
        return $query->join('arqueo_caja','arqueo_caja.arqueo_id','=','sobrante_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->orderBy('sobrante_fecha','asc');
    }    
    public function scopeSobrante($query, $id){
        return $query->join('arqueo_caja','arqueo_caja.arqueo_id','=','sobrante_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('sobrante_id','=',$id);
    }
    public function scopeSobrantexArqueo($query, $id){
        return $query->join('arqueo_caja','arqueo_caja.arqueo_id','=','sobrante_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('sobrante_caja.arqueo_id','=',$id);
    }
    public function scopeSobrantexArqueoSuma($query, $id){
        return $query->select(DB::raw('SUM(sobrante_monto) as sumaSobrante'))->join('arqueo_caja','arqueo_caja.arqueo_id','=','sobrante_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('sobrante_caja.arqueo_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->join('arqueo_caja','arqueo_caja.arqueo_id','=','sobrante_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('rango_id','=',$id);

    }
    public function arqueo()
    {
        return $this->belongsTo(Arqueo_Caja::class, 'arqueo_id', 'arqueo_id');
    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
}
