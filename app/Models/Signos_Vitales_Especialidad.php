<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Signos_Vitales_Especialidad extends Model
{
    use HasFactory;
    protected $table='signos_vitales_especialidad';
    protected $primaryKey = 'signose_id';
    public $timestamps=true;
    protected $fillable = [
        'signose_nombre',
        'signose_tipo',
        'signose_medida',            
        'signose_estado',     
        'especialidad_id',      
    ];
    protected $guarded =[
    ];
    public function scopeSignoVital($query, $id){
        return $query->where('signos_vitales_especialidad.especialidad_id','=',$id);
    }
}
