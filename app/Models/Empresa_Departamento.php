<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Empresa_Departamento extends Model
{
    use HasFactory;
    protected $table ='empresa_departamento';
    protected $primaryKey = 'departamento_id';
    public $timestamps = true;
    protected $fillable = [        
        'departamento_nombre',      
        'departamento_estado', 
        'sucursal_id',  
    ];
    protected $guarded =[
    ];
    public function scopeDepartamentos($query){
        return $query->join('sucursal','sucursal.sucursal_id','=','empresa_departamento.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('departamento_estado','=','1')->orderBy('departamento_nombre','asc');
    }
    public function scopeDepartamento($query, $id){
        return $query->join('sucursal','sucursal.sucursal_id','=','empresa_departamento.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('departamento_id','=',$id);
    }
    public function scopeDepartamentoByNomb($query, $nom){
        return $query->join('sucursal','sucursal.sucursal_id','=','empresa_departamento.sucursal_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('departamento_nombre','=',$nom);
    }
    public function sucursal()    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }
}
