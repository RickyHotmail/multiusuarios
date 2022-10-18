<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Signos_Vitales extends Model
{
    use HasFactory;
    protected $table ='signos_vitales';
    protected $primaryKey = 'signo_id';
    public $timestamps=true;
    protected $fillable = [   
        'signo_nombre',
        'signo_valor', 
        'signo_medida',   
        'signo_tipo',          
        'signo_estado',
        'expediente_id',        
    ];
    protected $guarded =[
    ];    
    public function scopeSignosVitales($query){
        return $query->join('expediente','expediente.expediente_id','=','signos_vitales.expediente_id'
                    )->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->where('signos_vitales.signo_estado','=','1');                    
    }
    public function scopeSignoVital($query, $id){
        return $query->join('expediente','expediente.expediente_id','=','signos_vitales.expediente_id'
                    )->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'      
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id,
                    )->where('signos_vitales.signo_id','=',$id);
    }
    public function scopeSignoVitalOrdenId($query, $id){
        return $query->join('expediente','expediente.expediente_id','=','signos_vitales.expediente_id'
                    )->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'      
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id,
                    )->where('orden_atencion.orden_id','=',$id);
    } 
    public function detalleExpediente(){
        return $this->belongsTo(Expediente::class, 'expediente_id', 'expediente_id');
    }  
}
