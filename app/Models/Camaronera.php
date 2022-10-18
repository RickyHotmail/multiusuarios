<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Camaronera extends Model
{
    use HasFactory;
    protected $table='camaronera';
    protected $primaryKey = 'camaronera_id';
    public $timestamps=true;
    protected $fillable = [
        'camaronera_nombre',
        'camaronera_ubicacion',  
        'camaronera_area', 
        'camaronera_estado',      
        'empresa_id',  
    ];
    protected $guarded =[
    ];
    public function scopecamaroneras($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('camaronera_estado','=','1');
    }
    public function scopecamaronera($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('camaronera_id','=',$id);
    } 
    public function scopecamaroneraid($query, $id){
        return $query->where('empresa_id','=',$id);
    } 
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
