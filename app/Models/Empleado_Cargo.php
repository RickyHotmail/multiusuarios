<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Empleado_Cargo extends Model
{
    use HasFactory;
    protected $table='empleado_cargo';
    protected $primaryKey = 'empleado_cargo_id';
    public $timestamps = true;
    protected $fillable = [        
        'empleado_cargo_nombre',                     
        'empresa_id',
        'empleado_cargo_estado',         
    ];
    protected $guarded =[
    ];   
    public function scopeEmpleadoCargos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('empleado_cargo_estado','=','1')->orderBy('empleado_cargo_nombre','asc');
    }
    public function scopeEmpleadoCargo($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('empleado_cargo_id','=',$id);
    }
    public function scopeEmpleadoByNombre($query, $nom){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('empleado_cargo_nombre','=',$nom);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
