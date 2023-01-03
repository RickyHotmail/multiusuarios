<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Orden_Examen extends Model
{
    use HasFactory;
    protected $table ='orden_examen';
    protected $primaryKey = 'orden_id';
    public $timestamps=true;
    protected $fillable = [   
        'orden_otros',     
        'orden_estado',
        'expediente_id',        
    ];
    protected $guarded =[
    ];    
    public function scopeOrdenExamenes($query){
        return $query->join('expediente','expediente.expediente_id','=','orden_examen.expediente_id'
                    )->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('paciente','paciente.paciente_id','=','orden_atencion.paciente_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id       
    /* )->where('orden_examen.orden_estado','=','1'*/);               
    }
    public function scopeOrdenExamenesHOY($query){
        return $query->join('expediente','expediente.expediente_id','=','orden_examen.expediente_id'
                    )->leftJoin('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('paciente','paciente.paciente_id','=','orden_atencion.paciente_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where(DB::raw('DATE(orden_examen.created_at)'), '=', date("Y-m-d")
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id       
                    )->where('orden_examen.orden_estado','=','1'
                    )->orderBy('orden_atencion.orden_numero','desc');
    }
    public function scopeOrdenExamen($query, $id){
        return $query->join('expediente','expediente.expediente_id','=','orden_examen.expediente_id'
                    )->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('paciente','paciente.paciente_id','=','orden_atencion.paciente_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id 
                    )->where('orden_examen.orden_id','=',$id);
    } 
    public function scopeOrdenanalisis($query, $id){
        return $query->join('analisis_laboratorio','analisis_laboratorio.orden_id','=','orden_examen.orden_id'
                    )->join('detalle_analisis','detalle_analisis.analisis_laboratorio_id','=','analisis_laboratorio.analisis_laboratorio_id'
                    )->join('producto','producto.producto_id','=','detalle_analisis.producto_id'
                    )->join('examen','examen.producto_id','=','producto.producto_id'
                    )->join('sucursal','sucursal.sucursal_id','=','analisis_laboratorio.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id 
                    )->where('orden_examen.orden_id','=',$id);
    } 
    public function scopeOrdenetiquetas($query, $id){
        return $query->join('analisis_laboratorio','analisis_laboratorio.orden_id','=','orden_examen.orden_id'
                    )->join('detalle_analisis','detalle_analisis.analisis_laboratorio_id','=','analisis_laboratorio.analisis_laboratorio_id'
                    )->join('producto','producto.producto_id','=','detalle_analisis.producto_id'
                    )->join('examen','examen.producto_id','=','producto.producto_id'
                    )->join('tipo_examen','tipo_examen.tipo_id','=','examen.tipo_id'
                    )->join('tipo_recipiente','tipo_examen.tipo_recipiente_id','=','tipo_recipiente.tipo_recipiente_id'
                    )->join('sucursal','sucursal.sucursal_id','=','analisis_laboratorio.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id 
                    )->where('orden_examen.orden_id','=',$id)
                    ->orderBy('tipo_recipiente.tipo_recipiente_id','asc');;
    } 
    public function scopeOrdenesByFechaSuc($query,$fechaI,$fechaF,$sucursal){
        return $query->join('expediente','expediente.expediente_id','=','orden_examen.expediente_id'
                    )->rightJoin('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('paciente','paciente.paciente_id','=','orden_atencion.paciente_id'
                    )->join('sucursal', 'sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('orden_fecha','>=',$fechaI
                    )->where('orden_fecha','<=',$fechaF
                    )->where('orden_atencion.sucursal_id','=',$sucursal
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->where('orden_atencion.orden_estado','>=', 3
                    )->orderBy('orden_atencion.orden_fecha','desc');
    }
    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expediente_id', 'expediente_id');
    } 
    public function analisis()
    {
        return $this->belongsTo(Analisis_Laboratorio::class, 'orden_id', 'orden_id');
    } 
     
    public function detalle()
    {
        return $this->hasMany(Detalle_Examen::class, 'orden_id', 'orden_id');
    }
}
