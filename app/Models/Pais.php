<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Pais extends Model
{
    use HasFactory;
    protected $table='pais';
    protected $primaryKey = 'pais_id';
    public $timestamps=true;
    protected $fillable = [
        'pais_nombre',
        'pais_codigo',       
        'pais_estado',        
        'empresa_id',           
    ];
    protected $guarded =[
    ];
    public function scopePaises($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('pais_estado','=','1')->orderBy('pais_nombre','asc');
    }
    public function scopePais($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('pais_id','=',$id);
    }
    public function scopePaisNombre($query, $nombre){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('pais_nombre','=',$nombre);
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
