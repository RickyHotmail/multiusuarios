<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Detalle_Imagen extends Model
{
    use HasFactory;
    protected $table ='detalle_imagen';
    protected $primaryKey = 'detalle_id';
    public $timestamps=true;
    protected $fillable = [   
        'detalle_indicacion',     
        'detalle_estado',
        'orden_id',
        'imagen_id',        
    ];
    protected $guarded =[
    ];    
    public function scopeDetalleImagenes($query){
        return $query->join('imagen','imagen.imagen_id','=','detalle_imagen.imagen_id'
                    )->join('tipo_imagen','tipo_imagen.tipo_id','=','imagen.tipo_id'
                    )->where('tipo_imagen.empresa_id','=',Auth::user()->empresa_id
                    )->where('detalle_imagen.detalle_estado','=','1');       
    }

    public function scopeDetalleImagenesOrden($query, $id){
        return $query->join('imagen','imagen.imagen_id','=','detalle_imagen.imagen_id'
                    )->join('tipo_imagen','tipo_imagen.tipo_id','=','imagen.tipo_id'
                    )->where('tipo_imagen.empresa_id','=',Auth::user()->empresa_id
                    )->where('detalle_imagen.detalle_estado','=','1'
                    )->where('detalle_imagen.orden_id',"=", $id);       
    }


    public function scopeDetalleImagen($query, $id){
        return $query->join('imagen','imagen.imagen_id','=','detalle_imagen.imagen_id'
                    )->join('tipo_imagen','tipo_imagen.tipo_id','=','imagen.tipo_id'
                    )->where('tipo_imagen.empresa_id','=',Auth::user()->empresa_id
                    )->where('detalle_imagen.detalle_id','=',$id);
    }

    public function imagen()
    {
        return $this->hasOne(Imagen::class, 'imagen_id', 'imagen_id');
    }
}
