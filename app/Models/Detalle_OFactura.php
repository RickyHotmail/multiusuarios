<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalle_OFactura extends Model
{
    use HasFactory;
    protected $table ='detalle_ofactura';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [   
        'detalle_observacion',
        'detalle_precio',         
        'detalle_estado',
        'orden_id',
        'procedimientoA_id',        
    ];
    protected $guarded =[
    ];    
    public function scopeDetalleOFacturas($query){
        return $query->join('aseguradora_procedimiento','aseguradora_procedimiento.procedimientoA_id','=','detalle_ofactura.procedimientoA_id'
                    )->join('procedimiento_especialidad','procedimiento_especialidad.procedimiento_id','=','aseguradora_procedimiento.procedimiento_id'
                    )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'        
                    )->where('especialidad.empresa_id','=',Auth::user()->empresa_id                      
                    )->where('detalle_ofactura.detalle_estado','=','1');                   
    }
    public function scopeDetalleOFactura($query, $id){
        return $query->join('aseguradora_procedimiento','aseguradora_procedimiento.procedimientoA_id','=','detalle_ofactura.procedimientoA_id'
                    )->join('procedimiento_especialidad','procedimiento_especialidad.procedimiento_id','=','aseguradora_procedimiento.procedimiento_id'
                    )->join('especialidad','especialidad.especialidad_id','=','procedimiento_especialidad.especialidad_id'        
                    )->where('especialidad.empresa_id','=',Auth::user()->empresa_id
                    )->where('detalle_ofactura.detalle_id','=',$id);
    }   
    
}
