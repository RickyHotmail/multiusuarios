<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Dependencia extends Model
{
    use HasFactory;
    protected $table='tipo_dependencia';
    protected $primaryKey = 'tipod_id';
    public $timestamps=true;
    protected $fillable = [
        'tipod_codigo',           
        'tipod_nombre',  
        'tipod_estado',  
        'empresa_id',        
    ];
    protected $guarded =[
    ];

    public function scopeTiposDependencias($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipod_estado','=','1')->orderBy('tipod_nombre','asc');
    }
    public function scopeTipoDependencia($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipod_id','=',$id);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
