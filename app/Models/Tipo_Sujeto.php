<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Sujeto extends Model
{
    use HasFactory;
    protected $table='tipo_sujeto';
    protected $primaryKey = 'tipo_sujeto_id';
    public $timestamps=true;
    protected $fillable = [
        'tipo_sujeto_codigo',
        'tipo_sujeto_nombre', 
        'tipo_sujeto_estado',
        'empresa_id',
    ];
    protected $guarded =[
    ];
    public function scopeTipoSujetos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_sujeto_estado','=','1')->orderBy('tipo_sujeto_codigo','asc');
    
    }
    public function scopeTipoSujeto($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_sujeto_id','=',$id);
    
    } 
    public function scopeTipoSujetoNombre($query, $nombre){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_sujeto_nombre','=',$nombre);
    
    }   
}
