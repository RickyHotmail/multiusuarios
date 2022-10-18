<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Expediente extends Model
{
    use HasFactory;
    protected $table ='expediente';
    protected $primaryKey = 'expediente_id';
    public $timestamps=true;
    protected $fillable = [  
        'expediente_observacion',  
        'expediente_proxima',      
        'expediente_estado',        
        'orden_id',        
    ];
    protected $guarded =[  ];    
    public function scopeExpedientes($query){
        return $query->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->where('expediente.expediente_estado','=','1');                    
    }
    public function scopeExpediente($query, $id){
        return $query->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->where('expediente.expediente_id','=',$id);         
    }
    public function scopeExpedienteorden($query, $id){
        return $query->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->where('orden_atencion.paciente_id','=',$id);         
    }
    public function ordenatencion()
    {
        return $this->belongsTo(Orden_Atencion::class, 'orden_id', 'orden_id');
    }

    public function detalleExpediente()
    {
        return $this->hasMany(Detalle_Expediente::class, 'expediente_id', 'expediente_id');
    }

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'expediente_id', 'expediente_id');
    }

    public function prescripcion()
    {
        return $this->hasOne(Prescripcion::class, 'expediente_id', 'expediente_id');
    }

    public function ordenExamen()
    {
        return $this->belongsTo(Orden_Examen::class, 'expediente_id', 'expediente_id');
    }

    public function ordenImagen()
    {
        return $this->belongsTo(Orden_Imagen::class, 'expediente_id', 'expediente_id');
    }

    public function signosVitales()
    {
        return $this->hasMany(Signos_Vitales::class, 'expediente_id', 'expediente_id');
    }

    public function scopeFindByOrden($query, $ordenId){
        return $query->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id
                    )->where('orden_atencion.orden_id','=',$ordenId);         
    }
}
