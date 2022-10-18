<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Entidad_Aseguradora extends Model
{
    use HasFactory;
    protected $table='entidad_aseguradora';
    protected $primaryKey = 'entidada_id';
    public $timestamps = true;
    protected $fillable = [        
        'entidada_estado',
        'entidad_id',
        'cliente_id',
    ];
    protected $guarded =[
    ];
    public function scopeEntidadAseguradoras($query)
    {
        return $query->join('entidad', 'entidad.entidad_id','=','entidad_aseguradora.entidad_id')->where('entidad.empresa_id','=',Auth::user()->empresa_id)->where('entidada_estado','=','1');
    }
    public function scopeEntidadAseguradora($query, $id)
    {
        return $query->join('entidad', 'entidad.entidad_id','=','entidad_aseguradora.entidad_id')->where('entidad.empresa_id','=',Auth::user()->empresa_id)->where('entidada_id', '=', $id);
    }

    public function scopeEntidadAseguradoraByEntidad($query, $id)
    {
        return $query->join('entidad', 'entidad.entidad_id','=','entidad_aseguradora.entidad_id'
                    )->where('entidad.empresa_id','=',Auth::user()->empresa_id
                    )->where('entidad.entidad_id', '=', $id);
    }

    public function scopeAseguradorasEntidades($query, $id){

        return $query->join('entidad', 'entidad.entidad_id','=','entidad_aseguradora.entidad_id' 
                    )->where('entidad.empresa_id','=',Auth::user()->empresa_id
                    )->where('entidad_aseguradora.cliente_id','=',$id
                    )->where('entidad_aseguradora.entidada_estado','=','1'
                    )->where('entidad.entidad_estado','=', 1);
    }
}
