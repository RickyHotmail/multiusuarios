<?php

namespace App\Models;

use App\Http\Controllers\DiarioController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Movimiento_Caja extends Model
{
    use HasFactory;
    protected $table='movimiento_caja';
    protected $primaryKey = 'movimiento_id';
    public $timestamps = true;
    protected $fillable = [        
        'movimiento_fecha',
        'movimiento_hora',         
        'movimiento_tipo',
        'movimiento_descripcion',
        'movimiento_valor',
        'movimiento_documento',
        'movimiento_numero_documento',
        'movimiento_estado',
        'arqueo_id',
        'diario_id',        
    ];
    protected $guarded =[
    ];
    public function scopeMovimientoCajas($query){
        return $query->join('arqueo_caja','arqueo_caja.arqueo_id','=','movimiento_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('movimiento_estado','=','1')->orderBy('movimiento_fecha','desc');
    }
    public function scopeMovimientoCaja($query, $id){
        return $query->join('arqueo_caja','arqueo_caja.arqueo_id','=','movimiento_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('movimiento_id','=',$id);
    }
    public function scopeMovimientoCajaxarqueo($query, $id, $diarioID){
        return $query->join('arqueo_caja','arqueo_caja.arqueo_id','=','movimiento_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('movimiento_caja.arqueo_id','=',$id)->where('movimiento_caja.diario_id','=', $diarioID);
    }
    public function scopeMovimientoxCajaAbierta($query, $id){
        return $query->join('arqueo_caja','arqueo_caja.arqueo_id','=','movimiento_caja.arqueo_id')->where('arqueo_caja.empresa_id','=',Auth::user()->empresa_id)->where('movimiento_caja.arqueo_id','=',$id);
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function arqueo(){
        return $this->belongsTo(Arqueo_Caja::class, 'arqueo_id', 'arqueo_id');
    }

}
