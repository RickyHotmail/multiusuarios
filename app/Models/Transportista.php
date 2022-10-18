<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transportista extends Model
{
    use HasFactory;
    protected $table='transportista';
    protected $primaryKey = 'transportista_id';
    public $timestamps = true;
    protected $fillable = [
        'transportista_cedula',
        'transportista_nombre',
        'transportista_placa',
        'transportista_embarcacion',        
        'empresa_id',
        'transportista_estado',         
    ];
    protected $guarded =[
    ];   
    public function scopeTransportistas($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('transportista_estado','=','1')->orderBy('transportista_nombre','asc');
    }
    public function scopeTransportista($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('transportista_id','=',$id);
    }
    public function scopeExiste($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('transportista_cedula','=',$id);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
