<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Identificacion extends Model
{
    use HasFactory;
    protected $table='tipo_identificacion';
    protected $primaryKey = 'tipo_identificacion_id';
    public $timestamps=true;
    protected $fillable = [
        'tipo_identificacion_nombre',
        'tipo_identificacion_codigo', 
        'tipo_identificacion_estado',
        'empresa_id',
    ];
    protected $guarded =[
    ]; use HasFactory;

    public function scopeTipoIdentificaciones($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_identificacion_estado','=','1')->orderBy('tipo_identificacion_nombre','asc');
    
    }
    public function scopeTipoIdentificacion($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_identificacion_id','=',$id)->orderBy('tipo_identificacion_nombre','asc');
    
    }  
    public function scopeTipoIdentificacionNombre($query, $nombre){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_identificacion_nombre','=',$nombre)->orderBy('tipo_identificacion_nombre','asc');
    
    } 

}
