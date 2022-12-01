<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Grupo_empresa_users extends Model
{
    use HasFactory;
    
    protected $table='grupo_empresa_users';
    protected $primaryKey = 'grupo_id';
    public $timestamps = true;
    protected $fillable = [        
        'empresa_id',                    
        'user_id',
        'grupo_estado',         
    ];
    protected $guarded =[
    ];   
    
    public function scopeGrupos($query,$id){
        return $query->where('empresa_id','=',$id)->where('grupo_estado','=','1');
    }
    public function scopeGruposusers($query,$iduser){
        return $query->where('user_id','=',$iduser)->where('grupo_estado','=','1');
    }
    public function empresas(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function usuarios(){
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
