<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Zona extends Model
{
    use HasFactory;
    protected $table='zona';
    protected $primaryKey = 'zona_id';
    public $timestamps=true;
    protected $fillable = [
        'zona_nombre',
        'zona_descripcion', 
        'zona_estado',
        'empresa_id',
    ];
    protected $guarded =[
    ];
    public function scopeZonas($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('zona_estado','=','1')->orderBy('zona_nombre','asc');
    
    }
    public function scopeZona($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('zona_id','=',$id);
    
    }      
}
