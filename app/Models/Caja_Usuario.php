<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Caja_Usuario extends Model
{
    use HasFactory;
    protected $table='caja_usuario';
    protected $primaryKey = 'cajau_id';
    public $timestamps = true;
    protected $fillable = [        
        'caja_id',
        'user_id',       
        
    ];
    protected $guarded =[
    ];
    public function scopeCajaUsuarios($query){
        return $query->join('caja','caja.caja_id','=','caja_usuario.caja_id')->where('caja.empresa_id','=',Auth::user()->empresa_id)->orderBy('cajau_id','asc');
    }
    public function scopeCajaUsuario($query, $id){
        return $query->join('caja','caja.caja_id','=','caja_usuario.caja_id')->where('caja.empresa_id','=',Auth::user()->empresa_id)->where('caja_usuario.caja_id','=',$id);
    }
    public function scopeCajaXusuario($query, $id){
        return $query->join('caja','caja.caja_id','=','caja_usuario.caja_id')->where('caja.empresa_id','=',Auth::user()->empresa_id)->where('caja_usuario.user_id','=',$id);
    }
    public function caja(){
        return $this->belongsTo(Caja::class, 'caja_id', 'caja_id');
    }  
    public function usuario(){
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }  
}
