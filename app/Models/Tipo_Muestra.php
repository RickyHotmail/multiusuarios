<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Muestra extends Model
{
    protected $table='tipo_muestra';
    protected $primaryKey = 'tipo_muestra_id';
    public $timestamps=true;
    protected $fillable = [
        'tipo_nombre', 
        'tipo_estado',
        'empresa_id',
    ];
    protected $guarded =[
    ]; 
    public function scopeTipos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_estado','=','1')->orderBy('tipo_nombre','asc');
    }
    public function scopeTipo($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_muestra_id','=',$id);
    }

}
