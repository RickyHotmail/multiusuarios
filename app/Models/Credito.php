<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Credito extends Model
{
    use HasFactory;
    protected $table='credito';
    protected $primaryKey = 'credito_id';
    public $timestamps = true;
    protected $fillable = [        
        'credito_nombre',
        'credito_descripcion',
        'credito_monto',                
        'empresa_id',
        'credito_estado',         
    ];
    protected $guarded =[
    ];   
    public function scopeCreditos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('credito_estado','=','1')->orderBy('credito_nombre','asc');
    }
    public function scopeCredito($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('credito_id','=',$id);
    }
    public function scopeCreditoEmpresaNombre($query, $nombre, $empresa){
        return $query->where('empresa_id','=', $empresa)->where('credito_nombre','=',$nombre);
    }
    public function scopeCreditoEmpresa($query,  $empresa){
        return $query->where('empresa_id','=', $empresa);
    }
    public function scopeCreditoNombre($query, $nom){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('credito_nombre','=',$nom);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
