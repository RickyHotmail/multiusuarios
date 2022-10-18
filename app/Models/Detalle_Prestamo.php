<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalle_Prestamo extends Model
{
    use HasFactory;
    protected $table ='detalle_prestamo';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [
        'detalle_fecha',        
        'detalle_interes',
        'detalle_valor_interes',
        'detalle_total',
        'detalle_dias',
        'detalle_estado',
        'prestamo_id',
        'diario_id',
    ];
    protected $guarded =[
    ];  
    public function scopeIntereses($query, $id){
        return $query->join('prestamo_banco', 'prestamo_banco.prestamo_id','=','detalle_prestamo.prestamo_id')->where('detalle_prestamo.prestamo_id','=',$id)->where('empresa_id','=',Auth::user()->empresa_id)->where('detalle_estado','=','1')->orderBy('detalle_fecha','asc');;
    }  
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function prestamo()
    {
        return $this->belongsTo(Prestamo_Banco::class, 'prestamo_id', 'prestamo_id');
    } 
}
