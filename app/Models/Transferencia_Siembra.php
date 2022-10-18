<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transferencia_Siembra extends Model
{
    use HasFactory;
    protected $table='transferencia_siembra';
    protected $primaryKey = 'transferencia_id';
    public $timestamps = true;
    protected $fillable = [
        'transferencia_codigo',        
        'transferencia_area',
        'transferencia_fecha',   
        'transferencia_volumen',              
        'transferencia_cosecha_juvenil',
        'transferencia_numero_juvenil',  
        'transferencia_peso_juvenil',
        'transferencia_juvenil',
        'transferencia_libras',
        'transferencia_longitud',    
        'transferencia_densidad',              
        'transferencia_cultivo', 
        'transferencia_estado',              
        'siembra_id',
        'siembra_padre_id',
       
    ];
    protected $guarded =[
    ];   
    public function scopeSiembras($query){
        return $query->join('siembra','siembra.siembra_id','=','transferencia_siembra.siembra_id')->join('piscina','siembra.piscina_id','=','piscina.piscina_id')->join('camaronera','camaronera.camaronera_id','=','piscina.camaronera_id')->where('camaronera.empresa_id','=',Auth::user()->empresa_id)->where('transferencia_estado','=','1');
    }
    public function siembra()
    {
        return $this->belongsTo(siembra::class, 'siembra_id', 'siembra_id');
    }
    public function siembrapadre()
    {
        return $this->belongsTo(siembra::class, 'siembra_padre_id', 'siembra_id');
    }
}
