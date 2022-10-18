<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Orden_Imagen extends Model
{
    use HasFactory;
    protected $table ='orden_imagen';
    protected $primaryKey = 'orden_id';
    public $timestamps=true;
    protected $fillable = [ 
        'orden_observacion',  
        'orden_estado',
        'expediente_id',        
    ];
    protected $guarded =[
    ];    
    public function scopeOrdenImagenes($query){
        return $query->select('orden_imagen.*'
                    )->join('expediente','expediente.expediente_id','=','orden_imagen.expediente_id'
                    )->join('orden_atencion','orden_atencion.orden_id','=','expediente.orden_id'
                    )->join('sucursal','sucursal.sucursal_id','=','orden_atencion.sucursal_id'
                    )->where('sucursal.empresa_id','=',Auth::user()->empresa_id       
                    )->orderBy('orden_atencion.orden_fecha', 'desc');
    }

    public function scopeOrdenImagen($query, $id){
        return $query->join('detalle_imagen','detalle_imagen.orden_id','=','orden_imagen.orden_id'
                    )->join('imagen','imagen.imagen_id','=','detalle_imagen.imagen_id'
                    )->join('tipo_imagen','tipo_imagen.tipo_id','=','imagen.tipo_id'
                    )->where('tipo_imagen.empresa_id','=',Auth::user()->empresa_id
                    )->where('orden_imagen.orden_id','=',$id);
    }   
    public function detalleImagen()
    {
        return $this->hasMany(Detalle_Imagen::class, 'orden_id', 'orden_id');
    }

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expediente_id', 'expediente_id');
    }
}
