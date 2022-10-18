<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responsable_User_Mantenimiento extends Model
{
    use HasFactory;
    protected $table="responsable_user_mantenimiento";
    protected $primaryKey="responsable_user_id";
    public $timestamps=true;
    
    protected $fillable=[
        'user_id',
        'empleado_id',
        'responsable_user_nombre',
        'responsable_user_apellido',
        'responsable_user_cedula',
        'responsable_user_telefono'
    ];

    protected $guarded=[
    ];

    public function scopeTecnicos($query){
        return $query;
    }

    public function empleado(){
        return $this->hasOne(Empleado::class, 'empleado_id', 'empleado_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'user_id', 'user_id');
    }

    public function scopeSearchByEmpleado($query, $id){
        return $query->where('empleado_id','=',$id);
    }

    public function scopeSearchByuser($query, $id){
        return $query->where('user_id','=',$id);
    }
}
