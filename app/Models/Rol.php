<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Rol extends Model
{
    use HasFactory;
    protected $table='rol';
    protected $primaryKey = 'rol_id';
    public $timestamps=true;
    protected $fillable = [
        'rol_nombre',
        'rol_tipo', 
        'rol_estado', 
    ];
    protected $guarded = [
    ];
    public function scopeRoles($query){
        return $query->where('rol_estado','=','1')
                     ->where('empresa_id', '=',Auth::user()->empresa_id)
                     ->orderBy('rol_nombre','asc');
    }
    public function scopeRol($query, $id){
        return $query->where('rol_id','=',$id);
    }

    public function scopeByNombre($query, $nombre){
        return $query->where('rol_estado','=','1')
                     ->where('rol_nombre','=',$nombre)
                     ->where('empresa_id', '=',Auth::user()->empresa_id);
    }
    public function permisos(){
        return $this->hasManyThrough(Permiso::class, Rol_Permiso::class, 'rol_id', 'permiso_id', 'rol_id', 'permiso_id');
    }
}
