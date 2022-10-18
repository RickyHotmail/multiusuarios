<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Analisis_Laboratorio extends Model
{
    use HasFactory;
    protected $table ='analisis_laboratorio';
    protected $primaryKey = 'analisis_laboratorio_id';
    public $timestamps = true;
    protected $fillable = [        
        'analisis_numero',
        'analisis_serie', 
        'analisis_secuencial',
        'analisis_fecha',
        'analisis_otros',
        'analisis_observacion',
        'analisis_estado',
        'sucursal_id',
        'orden_id',
        'orden_particular_id',
        'user_id',
    ];
    protected $guarded =[
    ];
    public function scopeanalisis($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','analisis_laboratorio.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('analisis_estado','=','1')->orderBy('analisis_numero','asc');
    }
    public function scopeanalisisatender($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','analisis_laboratorio.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('user_id','=',null)->orderBy('analisis_numero','asc');
    }
    public function scopeanalisisUSERS($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','analisis_laboratorio.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('user_id','=',Auth::user()->user_id)->orderBy('analisis_numero','asc');
    }
    public function scopeorden($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','analisis_laboratorio.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('analisis_estado','=','1')->where('analisis_laboratorio.orden_id','=',$id);
    }
    public function scopeAnalisisByOrden($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','analisis_laboratorio.sucursal_id')->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('analisis_laboratorio.orden_id','=',$id);
    }

    public function scopeAnalisisById($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','analisis_laboratorio.sucursal_id')
                     //->where('sucursal.empresa_id','=',Auth::user()->empresa_id)
                     //->where('analisis_estado','=','1')
                     ->where('analisis_laboratorio.orden_id','=',$id);
    }
    
    public function scopeSecuencial($query, $id){
        return $query->join('orden_examen','analisis_laboratorio.orden_id','=','orden_examen.orden_id'
        )->join('expediente','expediente.expediente_id','=','orden_examen.expediente_id'
        )->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
        )->join('paciente','paciente.paciente_id','=','orden_atencion.paciente_id'
        )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
        )->where('sucursal.empresa_id','=',Auth::user()->empresa_id);
    }
    public function scopeOrdenanalisis($query, $id){
        return $query->join('orden_examen','analisis_laboratorio.orden_id','=','orden_examen.orden_id'
                    )->join('detalle_analisis','detalle_analisis.analisis_laboratorio_id','=','analisis_laboratorio.analisis_laboratorio_id'
                    )->join('producto','producto.producto_id','=','detalle_analisis.producto_id'
                    )->join('examen','examen.producto_id','=','producto.producto_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->join('sucursal','sucursal.sucursal_id','=','analisis_laboratorio.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id 
                    )->where('analisis_laboratorio.analisis_laboratorio_id','=',$id)
                    ->orderBy('tipo_examen.tipo_id','asc');;
    } 
    public function orden()
    {
        return $this->belongsTo(Orden_Examen::class, 'orden_id', 'orden_id');
    }  
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }
    public function detalles(){
        return $this->hasMany(Detalle_Analisis::class, 'analisis_laboratorio_id', 'analisis_laboratorio_id');
    }
}
