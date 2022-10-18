<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Imagen extends Model
{
    use HasFactory;
    protected $table='tipo_imagen';
    protected $primaryKey = 'tipo_id';
    public $timestamps = true;
    protected $fillable = [
        'tipo_nombre',
        'tipo_estado',
        'empresa_id',
    ];
    protected $guarded =[
    ];
    public function scopeTipoImagenes($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_estado','=','1')->orderBy('tipo_nombre','asc');
    }
    public function scopeTipoImagen($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_id','=',$id);
    }   
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    } 
}
