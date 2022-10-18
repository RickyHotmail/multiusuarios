<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Diagnostico extends Model
{
    use HasFactory;
    protected $table='diagnostico';
    protected $primaryKey = 'diagnostico_id';
    public $timestamps=true;
    protected $fillable = [
        'diagnostico_observacion',        
        'diagnostico_estado',
        'expediente_id',
    ];
    protected $guarded =[
    ];    
    public function scopeDiagnosticos($query){
        return $query->join('expediente','expediente.expediente_id','=','diagnostico.expediente_id'
                    )->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id       
                    )->where('diagnostico.diagnostico_estado','=','1');                       
    }
    public function scopeDiagnostico($query, $id){
        return $query->join('expediente','expediente.expediente_id','=','diagnostico.expediente_id'
                    )->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id  
                    )->where('diagnostico.diagnostico_id','=',$id);
    }
    public function detallediagnostico()
    {
        return $this->hasMany(Detalle_Diagnostico::class, 'diagnostico_id', 'diagnostico_id');
    }
    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expediente_id', 'expediente_id');
    }
}
