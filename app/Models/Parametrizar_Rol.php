<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Parametrizar_Rol extends Model
{
    use HasFactory;
    protected $table='parametrizar_rol';
    protected $primaryKey = 'parametrizar_id';
    public $timestamps=true;
    protected $fillable = [
        'parametrizar_dias_trabajo',
        'parametrizar_sueldo_basico',
        'parametrizar_iess_personal', 
        'parametrizar_iess_patronal',
        'parametrizar_fondos_reserva',
        'parametrizar_horas_extras',
        'parametrizar_iece_secap',
        'parametrizar_porcentaje_he',
        'parametrizar_iess_gerencial',   
        'parametrizar_estado',
        'empresa_id',
       
        
    ];
    protected $guarded =[
    ];
    public function scopeRoles($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('parametrizar_estado','=','1');
    }
    public function scopeRol($query,$id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('parametrizar_id','=',$id);
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
