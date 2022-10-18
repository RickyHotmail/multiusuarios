<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Enfermedad extends Model
{
    use HasFactory;
    protected $table='enfermedad';
    protected $primaryKey = 'enfermedad_id';
    public $timestamps=true;
    protected $fillable = [
        'enfermedad_codigo',           
        'enfermedad_nombre',          
        'enfermedad_estado',
        'empresa_id',        
    ];
    protected $guarded =[
    ];
    public function scopeEnfermedades($query){
        return $query->join('empresa','empresa.empresa_id','=','enfermedad.empresa_id'
                    )->where('empresa.empresa_id','=',Auth::user()->empresa_id
                    )->where('enfermedad_estado','=','1')->orderBy('enfermedad_nombre','asc');
    }
    public function scopeEnfermedad($query, $id){
        return $query->join('empresa','empresa.empresa_id','=','enfermedad.empresa_id'
                    )->where('empresa.empresa_id','=',Auth::user()->empresa_id
                    )->where('enfermedad_id','=',$id);
    }
    public function empresa() {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }       
}
