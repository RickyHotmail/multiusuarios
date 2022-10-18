<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Descuento_Quincena extends Model
{
    use HasFactory;
    protected $table='descuento_quincena';
    protected $primaryKey = 'descuento_id';
    public $timestamps=true;
    protected $fillable = [
        'descuento_fecha',
        'descuento_descripcion',
        'descuento_valor',       
        'descuento_estado', 
        'cabecera_rol_cm_id',        
        'quincena_id', 
        'diario_id',
    ];
    protected $guarded =[
    ];
    public function scopeAnticipos($query, $id){
        return $query->join('diario','diario.diario_id','=','descuento_quincena.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('quincena_id','=',$id);
    }
    public function rolcm()
    {
        return $this->belongsTo(Cabecera_Rol_CM::class, 'cabecera_rol_cm_id', 'cabecera_rol_id');
    }
    public function quincena()
    {
        return $this->belongsTo(Quincena::class, 'quincena_id', 'quincena_id');
    } 
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    } 
}
