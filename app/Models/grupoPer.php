<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class GrupoPer extends Model
{
    use HasFactory;
    protected $table='grupo_permiso';
    protected $primaryKey = 'grupo_id';
    public $timestamps=true;
    protected $fillable = [
        'grupo_nombre',
        'grupo_icono',
        'grupo_orden', 
        'grupo_estado',
        'empresa_id',    
    ];
    protected $guarded =[
    ];
    public function scopeGrupos($query){
        //return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('grupo_estado','=','1')->orderBy('grupo_orden','asc');
        return $query->where('grupo_estado','=','1')->orderBy('grupo_orden','asc');
    }
    public function scopeGrupo($query, $id){
        //return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('grupo_id','=',$id);
        return $query->where('grupo_id','=',$id);
    }
    public function scopeExiste($query, $id){
        //return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('grupo_nombre','=',$id);
        return $query->where('grupo_nombre','=',$id);
    }
    public function permisos(){
        return $this->hasMany(Permiso::class, 'grupo_id', 'grupo_id');
    }
    public function detalles(){
        return $this->hasMany(Tipo_Grupo::class, 'grupo_id', 'grupo_id');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
