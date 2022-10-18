<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table='users';
    protected $primaryKey = 'user_id';
    public $timestamps=true;
    protected $fillable = [
        'user_username', 
        'user_cedula',
        'user_nombre',
        'user_correo',
        'user_estado', 
        'user_tipo', 
        'password',
        'empresa_id',
        'user_cambio_clave',
        'user_fcm_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    /*protected $casts = [
        'email_verified_at' => 'datetime',
    ];*/
    public function scopeUsuarios($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('user_username','<>','SuperAdministrador')->where('user_estado','=','1')->orderBy('user_username','asc');
    }
    public function scopeUsuario($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('user_username','<>','SuperAdministrador')->where('user_id','=',$id);
    }

    public function scopeFindByEmail($query, $mail){
        return $query->where('user_correo','=',$mail);
    }

    public function roles(){
        return $this->hasManyThrough(Rol::class, Usuario_Rol::class, 'user_id', 'rol_id', 'user_id', 'rol_id');
    }
    public function puntosEmision(){
        return $this->hasManyThrough(Punto_Emision::class, Usuario_PuntoE::class, 'user_id', 'punto_id', 'user_id', 'punto_id');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
